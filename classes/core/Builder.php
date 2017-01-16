<?php

class Shopgate_Core_Builder
{
    const AUTHENTICATION_SERVICE_SHOPGATE = 'ShopgateAuthenticationServiceShopgate';
    const AUTHENTICATION_SERVICE_OAUTH    = 'ShopgateAuthenticationServiceOAuth';
    
    /** @var ShopgateConfigInterface */
    protected $config;
    
    /** @var Shopgate_Helper_Logging_Strategy_LoggingInterface */
    protected $logging;
    
    /**
     * Loads configuration and initializes the ShopgateLogger class.
     *
     * @param ShopgateConfigInterface $config
     */
    public function __construct(ShopgateConfigInterface $config = null)
    {
        if (empty($config)) {
            $this->config = new ShopgateConfig();
        } else {
            $this->config = $config;
        }
        
        // set up logger
        ShopgateLogger::getInstance($this->config->getAccessLogPath(), $this->config->getRequestLogPath(),
            $this->config->getErrorLogPath(), $this->config->getDebugLogPath());
        
        // set up logging strategy
        /** @noinspection PhpDeprecationInspection */
        $this->logging = ShopgateLogger::getInstance()->getLoggingStrategy();
        
        // set error reporting
        $errorReporting = $this->determineErrorReporting($_REQUEST);
        $this->setErrorReporting($errorReporting);
        
        // enable debug logging if requested
        if (!empty($_REQUEST['debug_log'])) {
            $this->enableDebug(true);
        }
        
        // set custom error and exception handlers if requested
        if (!empty($_REQUEST['use_errorhandler'])) {
            $this->enableErrorHandler($errorReporting);
        }
        
        // register shutdown function if requested
        if (!empty($_REQUEST['use_shutdown_handler'])) {
            $this->enableShutdownFunction();
        }
        
        // set memory logging size unit; default to MB
        $this->setMemoryLoggingSizeUnit(isset($_REQUEST['memory_logging_unit'])
            ? $_REQUEST['memory_logging_unit']
            : 'MB'
        );
    }
    
    /**
     * Activates the Shopgate error handler for a given error level.
     *
     * The Shopgate error handler will log every message within the given log level using the given logger.
     *
     * Limited by the PHP implementation this cannot handle any fatal errors. Use enableShutdownHandler() for that.
     *
     * @param int $errorReporting
     */
    public function enableErrorHandler($errorReporting = 32767)
    {
        set_error_handler(
            array(
                new Shopgate_Helper_Error_Handling_ErrorHandler($this->buildStackTraceGenerator(), $logger),
                'handle',
            ),
            $errorReporting
        );
        
        set_exception_handler(array(
            new Shopgate_Helper_Error_Handling_ExceptionHandler($this->buildStackTraceGenerator(), $logger),
            'handle'
        ));
        
        $logFileHandler = @fopen($this->config->getErrorLogPath(), 'a');
        @fclose($logFileHandler);
        @chmod($this->config->getErrorLogPath(), 0777);
        @chmod($this->config->getErrorLogPath(), 0755);
        @error_reporting(E_ALL ^ E_DEPRECATED);
        @ini_set('log_errors', 1);
        @ini_set('error_log', $this->config->getErrorLogPath());
        @ini_set('ignore_repeated_errors', 1);
        @ini_set('html_errors', 0);
    }
    
    /**
     * Registers the Shopgate shutdown handler.
     *
     * The Shopgate shutdown handler will log any fatal error using the given logger.
     */
    public function enableShutdownFunction()
    {
        register_shutdown_function(array(
            new Shopgate_Helper_Error_Handling_ShutdownHandler(
                $this->logging,
                new Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider()
            ),
            'handle'
        ));
    }
    
    public function enableDebug($keepDebugLog)
    {
        $this->logging->enableDebug();
        $this->logging->keepDebugLog($keepDebugLog);
        
        // legacy
        /** @noinspection PhpDeprecationInspection */
        ShopgateLogger::getInstance()->enableDebug();
        
        /** @noinspection PhpDeprecationInspection */
        ShopgateLogger::getInstance()->keepDebugLog($keepDebugLog);
    }
    
    /**
     * @param int $errorReporting
     */
    public function setErrorReporting($errorReporting = 0)
    {
        error_reporting($errorReporting);
        ini_set('display_errors', (version_compare(PHP_VERSION, '5.2.4', '>=')) ? 'stdout' : true);
    }
    
    /**
     * @param string $unit
     */
    public function setMemoryLoggingSizeUnit($unit = 'MB')
    {
        /** @noinspection PhpDeprecationInspection */
        ShopgateLogger::getInstance()->setMemoryAnalyserLoggingSizeUnit($unit);
    }
    
    /**
     * Builds the Shopgate Library object graph for a given ShopgatePlugin object.
     *
     * This initializes all necessary objects of the library, wires them together and injects them into
     * the plugin class via its set* methods.
     *
     * @param ShopgatePlugin $plugin The ShopgatePlugin instance that should be wired to the framework.
     */
    public function buildLibraryFor(ShopgatePlugin $plugin)
    {
        // set error handler if configured
        if ($this->config->getUseCustomErrorHandler()) {
            set_error_handler('ShopgateErrorHandler');
        }
        
        // instantiate API stuff
        // -> MerchantAPI auth service (needs to be initialized first, since the config still can change along with the authentication information
        switch ($this->config->getSmaAuthServiceClassName()) {
            case ShopgateConfigInterface::SHOPGATE_AUTH_SERVICE_CLASS_NAME_SHOPGATE:
                $smaAuthService = new ShopgateAuthenticationServiceShopgate($this->config->getCustomerNumber(),
                    $this->config->getApikey());
                $smaAuthService->setup($this->config);
                $merchantApi = new ShopgateMerchantApi($smaAuthService, $this->config->getShopNumber(),
                    $this->config->getApiUrl());
                break;
            case ShopgateConfigInterface::SHOPGATE_AUTH_SERVICE_CLASS_NAME_OAUTH:
                $smaAuthService = new ShopgateAuthenticationServiceOAuth($this->config->getOauthAccessToken());
                $smaAuthService->setup($this->config);
                $merchantApi = new ShopgateMerchantApi($smaAuthService, null, $this->config->getApiUrl());
                break;
            default:
                // undefined auth service
                return trigger_error('Invalid SMA-Auth-Service defined - this should not happen with valid plugin code',
                    E_USER_ERROR);
        }
        // -> PluginAPI auth service (currently the plugin API supports only one auth service)
        $spaAuthService =
            new ShopgateAuthenticationServiceShopgate($this->config->getCustomerNumber(), $this->config->getApikey());
        $pluginApi      = new ShopgatePluginApi($this->config, $spaAuthService, $merchantApi, $plugin, null,
            $this->buildStackTraceGenerator(), $this->logging);
        
        if ($this->config->getExportConvertEncoding()) {
            array_splice(ShopgateObject::$sourceEncodings, 1, 0, $this->config->getEncoding());
            ShopgateObject::$sourceEncodings = array_unique(ShopgateObject::$sourceEncodings);
        }
        
        if ($this->config->getForceSourceEncoding()) {
            ShopgateObject::$sourceEncodings = array($this->config->getEncoding());
        }
        
        // instantiate export file buffer
        if (!empty($_REQUEST['action'])
            && (($_REQUEST['action'] == 'get_items')
                || ($_REQUEST['action'] == 'get_categories')
                || ($_REQUEST['action'] == 'get_reviews'))
        ) {
            $xmlModelNames = array(
                'get_items'      => 'Shopgate_Model_Catalog_Product',
                'get_categories' => 'Shopgate_Model_Catalog_Category',
                'get_reviews'    => 'Shopgate_Model_Review'
            );
            
            $format = (!empty($_REQUEST['response_type'])) ? $_REQUEST['response_type'] : '';
            switch ($format) {
                default:
                case 'xml':
                    /* @var $xmlModel Shopgate_Model_AbstractExport */
                    $xmlModel   = new $xmlModelNames[$_REQUEST['action']]();
                    $xmlNode    = new Shopgate_Model_XmlResultObject($xmlModel->getItemNodeIdentifier());
                    $fileBuffer =
                        new ShopgateFileBufferXml($xmlModel, $xmlNode, $this->config->getExportBufferCapacity(),
                            $this->config->getExportConvertEncoding(), ShopgateObject::$sourceEncodings);
                    break;
                
                case 'json':
                    $fileBuffer = new ShopgateFileBufferJson($this->config->getExportBufferCapacity(),
                        $this->config->getExportConvertEncoding(), ShopgateObject::$sourceEncodings);
                    break;
            }
        } else {
            if (!empty($_REQUEST['action'])
                && (($_REQUEST['action'] == 'get_items_csv')
                    || ($_REQUEST['action'] == 'get_categories_csv')
                    || ($_REQUEST['action'] == 'get_reviews_csv'))
            ) {
                $fileBuffer = new ShopgateFileBufferCsv($this->config->getExportBufferCapacity(),
                    $this->config->getExportConvertEncoding(), ShopgateObject::$sourceEncodings);
            } else {
                $fileBuffer = new ShopgateFileBufferCsv($this->config->getExportBufferCapacity(),
                    $this->config->getExportConvertEncoding(), ShopgateObject::$sourceEncodings);
            }
        }
        
        // inject apis into plugin
        $plugin->setConfig($this->config);
        $plugin->setMerchantApi($merchantApi);
        $plugin->setPluginApi($pluginApi);
        $plugin->setBuffer($fileBuffer);
    }
    
    /**
     * @return Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault
     */
    public function buildStackTraceGenerator()
    {
        return new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            ShopgateLogger::getInstance()->getObfuscator(),
            new Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderReflection()
        );
    }
    
    /**
     * @param string $type
     *
     * @return ShopgateAuthenticationServiceInterface
     */
    public function buildAuthenticationService($type = self::AUTHENTICATION_SERVICE_SHOPGATE)
    {
        switch ($type) {
            case self::AUTHENTICATION_SERVICE_OAUTH:
                $authService = new ShopgateAuthenticationServiceOAuth($this->config->getOauthAccessToken());
                break;
            
            case self::AUTHENTICATION_SERVICE_SHOPGATE:
            default:
                $authService = new ShopgateAuthenticationServiceShopgate(
                    $this->config->getCustomerNumber(),
                    $this->config->getApikey()
                );
        }
        
        $authService->setup($this->config);
        
        return $authService;
    }
    
    /**
     * Builds the Shopgate Library object graph for ShopgateMerchantApi and returns the instance.
     *
     * @return ShopgateMerchantApi
     */
    public function buildMerchantApi()
    {
        $merchantApi = null;
        switch ($smaAuthServiceClassName = $this->config->getSmaAuthServiceClassName()) {
            case ShopgateConfigInterface::SHOPGATE_AUTH_SERVICE_CLASS_NAME_SHOPGATE:
                $smaAuthService = new ShopgateAuthenticationServiceShopgate($this->config->getCustomerNumber(),
                    $this->config->getApikey());
                $smaAuthService->setup($this->config);
                $merchantApi = new ShopgateMerchantApi($smaAuthService, $this->config->getShopNumber(),
                    $this->config->getApiUrl());
                break;
            case ShopgateConfigInterface::SHOPGATE_AUTH_SERVICE_CLASS_NAME_OAUTH:
                $smaAuthService = new ShopgateAuthenticationServiceOAuth($this->config->getOauthAccessToken());
                $smaAuthService->setup($this->config);
                $merchantApi = new ShopgateMerchantApi($smaAuthService, null, $this->config->getApiUrl());
                break;
            default:
                // undefined auth service
                trigger_error('Invalid SMA-Auth-Service defined - this should not happen with valid plugin code',
                    E_USER_ERROR);
                break;
        }
        
        return $merchantApi;
    }
    
    /**
     * Builds the Shopgate Library object graph for Shopgate mobile redirect and returns the instance.
     *
     * @return ShopgateMobileRedirect
     *
     * @deprecated Will be removed in 3.0.0. Use SopgateBuilder::buildMobileRedirect() instead.
     */
    public function buildRedirect()
    {
        $merchantApi     = $this->buildMerchantApi();
        $settingsManager = new Shopgate_Helper_Redirect_SettingsManager(
            $this->config,
            $_GET,
            $_COOKIE
        );
        
        $templateParser = new Shopgate_Helper_Redirect_TemplateParser();
        
        $linkBuilder = new Shopgate_Helper_Redirect_LinkBuilder(
            $settingsManager,
            $templateParser
        );
        
        $tagsGenerator = new Shopgate_Helper_Redirect_TagsGenerator(
            $linkBuilder,
            $templateParser
        );
        
        $redirect = new ShopgateMobileRedirect(
            $this->config,
            $merchantApi,
            $tagsGenerator
        );
        
        return $redirect;
    }
    
    /**
     * Builds the Shopgate Library object graph for Shopgate mobile redirect and returns the instance.
     *
     * @param string $userAgent The requesting entity's user agent, e.g. $_SERVER['HTTP_USER_AGENT']
     * @param array  $get       [string, mixed] A copy of $_GET or the query string in the form of $_GET.
     * @param array  $cookie    [string, mixed] A copy of $_COOKIE or the request cookies in the form of $_COOKIE.
     *
     * @return Shopgate_Helper_Redirect_MobileRedirect
     *
     * @deprecated 3.0.0 - deprecated as of 2.9.51
     * @see        buildJsRedirect()
     * @see        buildHttpRedirect()
     */
    public function buildMobileRedirect($userAgent, array $get, array $cookie)
    {
        $settingsManager = new Shopgate_Helper_Redirect_SettingsManager($this->config, $get, $cookie);
        $templateParser  = new Shopgate_Helper_Redirect_TemplateParser();
        
        $linkBuilder = new Shopgate_Helper_Redirect_LinkBuilder(
            $settingsManager,
            $templateParser
        );
        
        $redirector = new Shopgate_Helper_Redirect_Redirector(
            $settingsManager,
            new Shopgate_Helper_Redirect_KeywordsManager(
                $this->buildMerchantApi(),
                $this->config->getRedirectKeywordCachePath(),
                $this->config->getRedirectSkipKeywordCachePath()
            ),
            $linkBuilder,
            $userAgent
        );
        
        $tagsGenerator = new Shopgate_Helper_Redirect_TagsGenerator(
            $linkBuilder,
            $templateParser
        );
        
        return new Shopgate_Helper_Redirect_MobileRedirect(
            $redirector,
            $tagsGenerator,
            $settingsManager,
            $templateParser,
            dirname(__FILE__) . '/../assets/js_header.html',
            $this->config->getShopNumber()
        );
    }
    
    /**
     * Generates JavaScript code to redirect the
     * current page Shopgate mobile site
     *
     * @param array $get
     * @param array $cookie
     *
     * @return Shopgate_Helper_Redirect_Type_Js
     */
    public function buildJsRedirect(array $get, array $cookie)
    {
        $settingsManager = new Shopgate_Helper_Redirect_SettingsManager($this->config, $get, $cookie);
        $templateParser  = new Shopgate_Helper_Redirect_TemplateParser();
        
        $linkBuilder   = new Shopgate_Helper_Redirect_LinkBuilder(
            $settingsManager,
            $templateParser
        );
        $tagsGenerator = new Shopgate_Helper_Redirect_TagsGenerator(
            $linkBuilder,
            $templateParser
        );
        
        $jsBuilder = new Shopgate_Helper_Redirect_JsScriptBuilder(
            $tagsGenerator,
            $settingsManager,
            $templateParser,
            dirname(__FILE__) . '/../assets/js_header.html',
            $this->config->getShopNumber()
        );
        
        $jsType = new Shopgate_Helper_Redirect_Type_Js($jsBuilder);
        
        return $jsType;
    }
    
    /**
     * Attempts to redirect via an HTTP header call
     * before the page is loaded
     *
     * @param string $userAgent - browser agent string
     * @param array  $get
     * @param array  $cookie
     *
     * @return Shopgate_Helper_Redirect_Type_Http
     */
    public function buildHttpRedirect($userAgent, array $get, array $cookie)
    {
        $settingsManager = new Shopgate_Helper_Redirect_SettingsManager($this->config, $get, $cookie);
        $templateParser  = new Shopgate_Helper_Redirect_TemplateParser();
        
        $linkBuilder = new Shopgate_Helper_Redirect_LinkBuilder(
            $settingsManager,
            $templateParser
        );
        
        $redirector = new Shopgate_Helper_Redirect_Redirector(
            $settingsManager,
            new Shopgate_Helper_Redirect_KeywordsManager(
                $this->buildMerchantApi(),
                $this->config->getRedirectKeywordCachePath(),
                $this->config->getRedirectSkipKeywordCachePath()
            ),
            $linkBuilder,
            $userAgent
        );
        
        return new Shopgate_Helper_Redirect_Type_Http($redirector);
    }
    
    /**
     * @param array $request The request parameters.
     *
     * @return int
     */
    private function determineErrorReporting($request)
    {
        // determine desired error reporting (default to 0)
        $errorReporting = (isset($request['error_reporting'])) ? $request['error_reporting'] : 0;
        
        // determine error reporting for the current stage (custom, pg => E_ALL; the previously requested otherwise)
        $serverTypesAdvancedErrorLogging = array('custom', 'pg');
        $errorReporting                  = (isset($serverTypesAdvancedErrorLogging[$this->config->getServer()]))
            ? 32767
            : $errorReporting;
        
        return $errorReporting;
    }
}