<?php
/*
* Shopgate GmbH
*
* URHEBERRECHTSHINWEIS
*
* Dieses Plugin ist urheberrechtlich geschützt. Es darf ausschließlich von Kunden der Shopgate GmbH
* zum Zwecke der eigenen Kommunikation zwischen dem IT-System des Kunden mit dem IT-System der
* Shopgate GmbH über www.shopgate.com verwendet werden. Eine darüber hinausgehende Vervielfältigung, Verbreitung,
* öffentliche Zugänglichmachung, Bearbeitung oder Weitergabe an Dritte ist nur mit unserer vorherigen
* schriftlichen Zustimmung zulässig. Die Regelungen der §§ 69 d Abs. 2, 3 und 69 e UrhG bleiben hiervon unberührt.
*
* COPYRIGHT NOTICE
*
* This plugin is the subject of copyright protection. It is only for the use of Shopgate GmbH customers,
* for the purpose of facilitating communication between the IT system of the customer and the IT system
* of Shopgate GmbH via www.shopgate.com. Any reproduction, dissemination, public propagation, processing or
* transfer to third parties is only permitted where we previously consented thereto in writing. The provisions
* of paragraph 69 d, sub-paragraphs 2, 3 and paragraph 69, sub-paragraph e of the German Copyright Act shall remain unaffected.
*
*  @author Shopgate GmbH <interfaces@shopgate.com>
*/

/**
 * Class ShopgatePluginApi
 *
 * @method ShopgatePlugin getPlugin()
 * @method ShopgatePluginApi setPlugin(ShopgatePlugin $value)
 * @method ShopgateConfigInterface getConfig()
 * @method ShopgatePluginApi setConfig(ShopgateConfigInterface $value)
 * @method ShopgateMerchantApiInterface getMerchantApi()
 * @method ShopgatePluginApi setMerchantApi(ShopgateMerchantApiInterface $value)
 * @method ShopgateAuthentificationServiceInterface getAuthService()
 * @method ShopgatePluginApi setAuthService(ShopgateAuthentificationServiceInterface $value)
 * @method array getParams()
 * @method ShopgatePluginApi setParams(array $value)
 * @method array getActionWhitelist()
 * @method ShopgatePluginApi setActionWhitelist(array $value)
 * @method mixed getResponseData()
 * @method ShopgatePluginApi setResponseData(mixed $value)
 * @method ShopgatePluginApiResponse getResponse()
 * @method ShopgatePluginApi setResponse(ShopgatePluginApiResponse $value)
 * @method string getTraceId()
 * @method ShopgatePluginApi setTraceId(string $value)
 */
class ShopgatePluginApi extends ShopgateObject implements ShopgatePluginApiInterface
{
	public function __construct(
		ShopgateConfigInterface $config,
		ShopgateAuthentificationServiceInterface $authService,
		ShopgateMerchantApiInterface $merchantApi,
		ShopgatePlugin $plugin,
		ShopgatePluginApiResponse $response = null
	) {
		$this->setConfig($config);
		$this->setAuthService($authService);
		$this->setMerchantApi($merchantApi);
		$this->setPlugin($plugin);
		$this->setResponse($response);
		$this->setResponseData(array());

		// initialize action whitelist
		$this->setActionWhitelist(
			 array(
				  'ping',
				  'cron',
				  'add_order',
				  'update_order',
				  'get_debug_info',
				  'get_items_csv',
				  'get_categories_csv',
				  'get_reviews_csv',
				  'get_pages_csv',
				  'get_log_file',
				  'clear_log_file',
				  'clear_cache',
				  'check_cart',
				  'redeem_coupons',
				  'get_customer',
				  'register_customer',
				  'get_settings',
				  'set_settings',
			 )
		);
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function handleRequest(array $data = array())
	{
		// log incoming request
		$this->log(var_export($data, true), ShopgateLogger::LOGTYPE_ACCESS);
		// save the params
		$this->setParams($data);
		$params    = $this->getParams();
		$errorText = '';

		// save trace_id
		if (isset($params['trace_id'])) {
			$this->setTraceId($params['trace_id']);
		}

		try {
			$this->getAuthService()->checkAuthentification();

			// set error handler to Shopgate's handler if requested
			if (!empty($params['use_errorhandler'])) {
				set_error_handler('ShopgateErrorHandler');
			}

			if (!empty($params['use_shutdown_handler'])) {
				register_shutdown_function('ShopgateShutdownHandler');
			}

			// enable debugging if requested
			if (!empty($params['debug_log'])) {
				ShopgateLogger::getInstance()->enableDebug();
				ShopgateLogger::getInstance()->keepDebugLog(!empty($params['keep_debug_log']));
			}

			// enable error reporting if requested
			if (!empty($params['error_reporting'])) {
				error_reporting($params['error_reporting']);
				ini_set('display_errors', (version_compare(PHP_VERSION, '5.2.4', '>=')) ? 'stdout' : true);
			}

			// memory logging size unit setup
			if (!empty($params['memory_logging_unit'])) {
				ShopgateLogger::getInstance()->setMemoryAnalyserLoggingSizeUnit($params['memory_logging_unit']);
			} else {
				// MB by default if none is set
				ShopgateLogger::getInstance()->setMemoryAnalyserLoggingSizeUnit('MB');
			}

			// check if the request is for the correct shop number or an adapter-plugin
			if (
				!$this->getConfig()->getIsShopgateAdapter() && !empty($params['shop_number'])
				&& ($params['shop_number'] != $this->getConfig()->getShopNumber())
			) {
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_UNKNOWN_SHOP_NUMBER, "{$params['shop_number']}");
			}

			// check if an action to call has been passed, is known and enabled
			if (empty($params['action'])) {
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_ACTION,
												   'Passed parameters: ' . var_export($params, true));
			}

			// check if the action is white-listed
			if (!in_array($params['action'], $this->getActionWhitelist())) {
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_UNKNOWN_ACTION, "{$params['action']}");
			}

			// check if action is enabled in the config
			$configArray = $this->getConfig()->toArray();
			if (empty($configArray['enable_' . $params['action']])) {
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_DISABLED_ACTION, "{$params['action']}");
			}

			// call the action
			$action = $this->camelize($params['action']);
			$this->{$action}();
		} catch (ShopgateLibraryException $e) {
			$error     = $e->getCode();
			$errorText = $e->getMessage();
		} catch (ShopgateMerchantApiException $e) {
			$error = ShopgateLibraryException::MERCHANT_API_ERROR_RECEIVED;
			$errorText
				   =
				ShopgateLibraryException::getMessageFor(ShopgateLibraryException::MERCHANT_API_ERROR_RECEIVED) . ': "'
				. $e->getCode() . ' - ' . $e->getMessage() . '"';
		} catch (Exception $e) {
			$message = "\n" . get_class($e) . "\n";
			$message .= 'with code:   ' . $e->getCode() . "\n";
			$message .= 'and message: \'' . $e->getMessage() . "'\n";

			// new ShopgateLibraryException to build proper error message and perform logging
			$se        = new ShopgateLibraryException($message);
			$error     = $se->getCode();
			$errorText = $se->getMessage();
		}

		$response = $this->getResponse();
		// print out the response
		if (!empty($error)) {
			if (empty($response)) {
				$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
			}
			$this->getResponse()->markError($error, $errorText);
		}

		$response = $this->getResponse();
		if (empty($response)) {
			trigger_error('No response object defined. This should _never_ happen.', E_USER_ERROR);
		}

		$this->getResponse()->setData($this->getResponseData());
		$this->getResponse()->send();

		// return true or false
		return (empty($error));
	}

	######################################################################
	## Following methods represent the Shopgate Plugin API's actions:   ##
	######################################################################

	/**
	 * Represents the "ping" action.
	 *
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_ping
	 */
	protected function ping()
	{
		// obfuscate data relevant for authentication
		$config                    = $this->getConfig()->toArray();
		$config['customer_number'] = ShopgateLogger::OBFUSCATION_STRING;
		$config['shop_number']     = ShopgateLogger::OBFUSCATION_STRING;
		$config['apikey']          = ShopgateLogger::OBFUSCATION_STRING;

		$responseData = $this->getResponseData();

		// prepare response data array
		$responseData['pong']                     = 'OK';
		$responseData['configuration']            = $config;
		$responseData['plugin_info']              = $this->getPlugin()->createPluginInfo();
		$responseData['permissions']              = $this->getPermissions();
		$responseData['php_version']              = phpversion();
		$responseData['php_config']               = $this->getPhpSettings();
		$responseData['php_curl']                 = function_exists('curl_version')
			? curl_version()
			: 'No PHP-CURL installed';
		$responseData['php_extensions']           = get_loaded_extensions();
		$responseData['shopgate_library_version'] = SHOPGATE_LIBRARY_VERSION;
		$responseData['plugin_version']           = defined('SHOPGATE_PLUGIN_VERSION')
			? SHOPGATE_PLUGIN_VERSION
			: 'UNKNOWN';

		$this->setResponseData($responseData);

		// set data and return response
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
	}

	/**
	 * Represents the "debug" action.
	 *
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_ping
	 */
	protected function getDebugInfo()
	{
		// prepare response data array
		$this->setResponseData($this->getPlugin()->getDebugInfo());

		// set data and return response
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
	}

	/**
	 * Represents the "add_order" action.
	 *
	 * @throws ShopgateLibraryException
	 */
	protected function cron()
	{
		$params = $this->getParams();
		if (empty($params['jobs']) || !is_array($params['jobs'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_CRON_NO_JOBS);
		}

		// time tracking
		$startTime = microtime(true);
		// references
		$message    = '';
		$errorCount = 0;

		// execute the jobs
		foreach ($params['jobs'] as $job) {
			if (empty($job['job_name'])) {
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_CRON_NO_JOB_NAME);
			}

			if (empty($job['job_params'])) {
				$job['job_params'] = array();
			}

			try {
				$jobErrorCount = 0;

				// job execution
				$this->getPlugin()->cron($job['job_name'], $job['job_params'], $message, $jobErrorCount);

				// check error count
				if ($jobErrorCount > 0) {
					$message .= 'Errors happend in job: "' . $job['job_name'] . '" (' . $jobErrorCount . ' errors)';
					$errorCount += $jobErrorCount;
				}
			} catch (Exception $e) {
				$errorCount++;
				$message .= 'Job aborted: "' . $e->getMessage() . '"';
			}
		}

		// time tracking
		$endTime = microtime(true);
		$runtime = $endTime - $startTime;
		$runtime = round($runtime, 4);

		// prepare response
		$responses                          = array();
		$responses['message']               = $message;
		$responses['execution_error_count'] = $errorCount;
		$responses['execution_time']        = $runtime;

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
		$this->setResponseData($responses);
	}

	/**
	 * Represents the "add_order" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_add_order
	 */
	protected function addOrder()
	{
		$params = $this->getParams();
		if (!isset($params['order_number'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_ORDER_NUMBER);
		}

		$orders = $this->getMerchantApi()->getOrders(
					   array('order_numbers[0]' => $params['order_number'], 'with_items' => 1)
		)              ->getData();
		if (empty($orders)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'"orders" not set. Response: ' . var_export($orders, true)
			);
		}
		if (count($orders) > 1) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'more than one order in response. Response: ' . var_export($orders, true)
			);
		}

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}

		$orderData = $this->getPlugin()->addOrder($orders[0]);
		if (is_array($orderData)) {
			$this->setResponseData($orderData);
		} else {
			$responseData                          = $this->getResponseData();
			$responseData['external_order_id']     = $orderData;
			$responseData['external_order_number'] = null;
			$this->setResponseData($responseData);
		}
	}

	/**
	 * Represents the "update_order" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_update_order
	 */
	protected function updateOrder()
	{
		$params = $this->getParams();
		if (!isset($params['order_number'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_ORDER_NUMBER);
		}

		$orders = $this->getMerchantApi()->getOrders(
					   array('order_numbers[0]' => $params['order_number'], 'with_items' => 1)
		)              ->getData();

		if (empty($orders)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'"order" not set. Response: ' . var_export($orders, true)
			);
		}

		if (count($orders) > 1) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'more than one order in response. Response: ' . var_export($orders, true));
		}

		$payment  = 0;
		$shipping = 0;

		if (isset($params['payment'])) {
			$payment = (bool)$params['payment'];
		}
		if (isset($params['shipping'])) {
			$shipping = (bool)$params['shipping'];
		}

		/** @var ShopgateOrder $order */
		$order = $orders[0];
		$order->setUpdatePayment($payment);
		$order->setUpdateShipping($shipping);

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}

		$orderData = $this->getPlugin()->updateOrder($orders[0]);
		if (is_array($orderData)) {
			$this->setResponseData($orderData);
		} else {
			$responseData                          = $this->getResponseData();
			$responseData['external_order_id']     = $orderData;
			$responseData['external_order_number'] = null;
			$this->setResponseData($responseData);
		}
	}

	/**
	 * Represents the "redeem_coupons" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_redeem_coupons
	 */
	protected function redeemCoupons()
	{
		$params = $this->getParams();
		if (!isset($params['cart'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_CART);
		}
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}

		$cart       = new ShopgateCart($params['cart']);
		$couponData = $this->getPlugin()->redeemCoupons($cart);

		if (!is_array($couponData)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
				'Plugin Response: ' . var_export($couponData, true)
			);
		}

		// Workaround:
		// $couponData was specified to be a ShopgateExternalCoupon[].
		// Now supports the same format as checkCart(), i.e. array('external_coupons' => ShopgateExternalCoupon[]).
		if (!empty($couponData['external_coupons']) && is_array($couponData['external_coupons'])) {
			$couponData = $couponData['external_coupons'];
		}

		$responseData = array("external_coupons" => array());
		foreach ($couponData as $coupon) {
			/** @var ShopgateExternalCoupon $coupon */
			if (!is_object($coupon) || !($coupon instanceof ShopgateExternalCoupon)) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
					'Plugin Response: ' . var_export($coupon, true)
				);
			}
			$coupon = $coupon->toArray();
			unset($coupon["order_index"]);

			$responseData["external_coupons"][] = $coupon;
		}

		$this->setResponseData($responseData);
	}

	/**
	 * Represents the "check_cart" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_check_cart
	 */
	protected function checkCart()
	{
		$params = $this->getParams();
		if (!isset($params['cart'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_CART);
		}
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}

		$cart         = new ShopgateCart($params['cart']);
		$cartData     = $this->getPlugin()->checkCart($cart);
		$responseData = array();

		if (!is_array($cartData)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
				'Plugin Response: ' . var_export($cartData, true)
			);
		}

		$shippingMethods = array();
		foreach ($cartData["shipping_methods"] as $shippingMethod) {
			/** @var ShopgateShippingMethod $shippingMethod */
			if (!is_object($shippingMethod) || !($shippingMethod instanceof ShopgateShippingMethod)) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
					'Plugin Response: ' . var_export($shippingMethod, true)
				);
			}
			$shippingMethods[] = $shippingMethod->toArray();
		}
		$responseData["shipping_methods"] = $shippingMethods;

		if (!empty($cartData['payment_methods'])) {
			$responseData['payment_methods'] = $cartData['payment_methods'];
		}

		$paymentMethods = array();
		foreach ($cartData["payment_methods"] as $paymentMethod) {
			/** @var ShopgatePaymentMethod $paymentMethod */
			if (!is_object($paymentMethod) || !($paymentMethod instanceof ShopgatePaymentMethod)) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
					'Plugin Response: ' . var_export($paymentMethod, true));
			}
			$paymentMethods[] = $paymentMethod->toArray();
		}
		$responseData["payment_methods"] = $paymentMethods;

		$cartItems = array();
		foreach ($cartData["cart_items"] as $cartItem) {
			/** @var ShopgateCartItem $cartItem */
			if (!is_object($cartItem) || !($cartItem instanceof ShopgateCartItem)) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
					'Plugin Response: ' . var_export($cartItem, true)
				);
			}
			$cartItems[] = $cartItem->toArray();
		}
		$responseData["cart_items"] = $cartItems;

		$coupons = array();
		foreach ($cartData["external_coupons"] as $coupon) {
			/** @var ShopgateExternalCoupon $coupon */
			if (!is_object($coupon) || !($coupon instanceof ShopgateExternalCoupon)) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
					'Plugin Response: ' . var_export($coupon, true)
				);
			}

			$coupon = $coupon->toArray();
			unset($coupon["order_index"]);
			$coupons[] = $coupon;
		}
		$responseData["external_coupons"] = $coupons;

		$this->setResponseData($responseData);
	}

	/**
	 * Represents the "get_settings" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_settings
	 */
	protected function getSettings()
	{
		$this->setResponseData($this->getPlugin()->getSettings());

		// set data and return response
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
	}

	/**
	 * Represents the "set_settings" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_set_settings
	 */
	protected function setSettings()
	{
		$params = $this->getParams();
		if (empty($params['shopgate_settings']) || !is_array($params['shopgate_settings'])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_NO_SHOPGATE_SETTINGS,
				'Request: ' . var_export($params, true)
			);
		}
		// settings that may never be changed:
		$shopgateSettingsBlacklist = array(
			'shop_number', 'customer_number', 'apikey', 'plugin_name',
			'export_folder_path', 'log_folder_path', 'cache_folder_path',
			'items_csv_filename', 'categories_csv_filename', 'reviews_csv_filename', 'pages_csv_filename',
			'access_log_filename', 'error_log_filename', 'request_log_filename', 'debug_log_filename',
			'redirect_keyword_cache_filename', 'redirect_skip_keyword_cache_filename',
		);

		// filter the new settings
		$shopgateSettingsNew = array();
		$shopgateSettingsOld = $this->getConfig()->toArray();
		foreach ($params['shopgate_settings'] as $setting) {
			if (!isset($setting['name'])) {
				throw new ShopgateLibraryException(
					ShopgateLibraryException::PLUGIN_API_NO_SHOPGATE_SETTINGS,
					'Wrong format: ' . var_export($setting, true)
				);
			}

			if (in_array($setting['name'], $shopgateSettingsBlacklist)) {
				continue;
			}

			if (!in_array($setting['name'], array_keys($shopgateSettingsOld))) {
				continue;
			}

			$shopgateSettingsNew[$setting['name']] = isset($setting['value']) ? $setting['value'] : null;
		}

		$this->getConfig()->load($shopgateSettingsNew);
		$this->getConfig()->save(array_keys($shopgateSettingsNew), true);

		$diff = array();
		foreach ($shopgateSettingsNew as $setting => $value) {
			$diff[] = array('name' => $setting, 'old' => $shopgateSettingsOld[$setting], 'new' => $value);
		}

		// set data and return response
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
		$responseData                      = $this->getResponseData();
		$responseData['shopgate_settings'] = $diff;
		$this->setResponseData($responseData);
	}

	/**
	 * Represents the "get_customer" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_customer
	 */
	protected function getCustomer()
	{
		$params = $this->getParams();
		if (!isset($params['user'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_USER);
		}

		if (!isset($params['pass'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_PASS);
		}

		$customer = $this->getPlugin()->getCustomer($params['user'], $params['pass']);
		if (!is_object($customer) || !($customer instanceof ShopgateCustomer)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_WRONG_RESPONSE_FORMAT,
				'Plugin Response: ' . var_export($customer, true));
		}

		$customerData = $customer->toArray();
		$addressList  = $customerData['addresses'];
		unset($customerData['addresses']);

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
		$responseData              = $this->getResponseData();
		$responseData["user_data"] = $customerData;
		$responseData["addresses"] = $addressList;
		$this->setResponseData($responseData);
	}

	protected function registerCustomer()
	{
		$params = $this->getParams();
		if (!isset($params['user'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_USER);
		}

		if (!isset($params['pass'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_NO_PASS);
		}

		if (!isset($params['user_data'])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_NO_USER_DATA,
				"missing user_data",
				true
			);
		}

		if (!$this->getConfig()->getEnableGetCustomer()) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_API_DISABLED_ACTION,
				"Action 'get_customer' is not activated but is needed by register_customer",
				true
			);
		}

		$user     = $params['user'];
		$pass     = $params['pass'];
		$customer = new ShopgateCustomer($params['user_data']);
		$userData = $params["user_data"];

		if (isset($userData['addresses']) && is_array($userData['addresses'])) {
			$addresses = array();
			foreach ($userData['addresses'] as $address) {
				$addresses[] = new ShopgateAddress($address);
			}
			$customer->setAddresses($addresses);
		}

		$this->getPlugin()->registerCustomer($user, $pass, $customer);

		$newCustomer  = $this->getPlugin()->getCustomer($user, $pass);
		$customerData = $newCustomer->toArray();
		$addressList  = $customerData['addresses'];
		unset($customerData['addresses']);

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
		$responseData              = $this->getResponseData();
		$responseData["user_data"] = $customerData;
		$responseData["addresses"] = $addressList;
		$this->setResponseData($responseData);
	}

	/**
	 * Represents the "get_items_csv" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_items_csv
	 */
	protected function getItemsCsv()
	{
		$params = $this->getParams();
		if (isset($params['limit']) && isset($params['offset'])) {
			$this->getPlugin()->setExportLimit((int)$params['limit']);
			$this->getPlugin()->setExportOffset((int)$params['offset']);
			$this->getPlugin()->setSplittedExport(true);
		}

		// generate / update items csv file if requested
		$this->getPlugin()->startGetItemsCsv();

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseTextCsv($this->getTraceId()));
		}
		$this->setResponseData($this->getConfig()->getItemsCsvPath());
	}

	/**
	 * Represents the "get_categories_csv" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_categories_csv
	 */
	protected function getCategoriesCsv()
	{
		// generate / update categories csv file
		$this->getPlugin()->startGetCategoriesCsv();

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseTextCsv($this->getTraceId()));
		}
		$this->setResponseData($this->getConfig()->getCategoriesCsvPath());
	}

	/**
	 * Represents the "get_reviews_csv" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_reviews_csv
	 */
	protected function getReviewsCsv()
	{
		$params = $this->getParams();
		if (isset($params['limit']) && isset($params['offset'])) {
			$this->getPlugin()->setExportLimit((int)$params['limit']);
			$this->getPlugin()->setExportOffset((int)$params['offset']);
			$this->getPlugin()->setSplittedExport(true);
		}

		// generate / update reviews csv file
		$this->getPlugin()->startGetReviewsCsv();

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseTextCsv($this->getTraceId()));
		}
		$this->setResponseData($this->getConfig()->getReviewsCsvPath());
	}

	/**
	 * Represents the "get_pages_csv" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_pages_csv
	 */
	protected function getPagesCsv()
	{
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseTextCsv($this->getTraceId()));
		}
		$this->setResponseData($this->getConfig()->getPagesCsvPath());
	}

	/**
	 * Represents the "get_log_file" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_log_file
	 */
	protected function getLogFile()
	{
		// disable debug log for this action
		$logger = ShopgateLogger::getInstance();
		$logger->disableDebug();
		$logger->keepDebugLog(true);
		$params = $this->getParams();
		$type   = (empty($params['log_type'])) ? ShopgateLogger::LOGTYPE_ERROR : $params['log_type'];
		$lines  = (!isset($params['lines'])) ? null : $params['lines'];
		$log    = $logger->tail($type, $lines);

		// return the requested log file content and end the script
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseTextPlain($this->getTraceId()));
		}
		$this->setResponseData($log);
	}

	/**
	 * Represents the "clear_log_file" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_clear_log_file
	 */
	protected function clearLogFile()
	{
		$params = $this->getParams();
		if (empty($params['log_type'])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_UNKNOWN_LOGTYPE);
		}

		switch ($params['log_type']) {
			case ShopgateLogger::LOGTYPE_ACCESS:
				$logFilePath = $this->getConfig()->getAccessLogPath();
				break;
			case ShopgateLogger::LOGTYPE_REQUEST:
				$logFilePath = $this->getConfig()->getRequestLogPath();
				break;
			case ShopgateLogger::LOGTYPE_ERROR:
				$logFilePath = $this->getConfig()->getErrorLogPath();
				break;
			case ShopgateLogger::LOGTYPE_DEBUG:
				$logFilePath = $this->getConfig()->getDebugLogPath();
				break;
			default:
				throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_UNKNOWN_LOGTYPE);
		}

		$logFilePointer = @fopen($logFilePath, 'w');
		if ($logFilePointer === false) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_FILE_OPEN_ERROR, "File: $logFilePath", true);
		}
		fclose($logFilePointer);

		// return the path of the deleted log file
		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
	}

	/**
	 * Represents the "clear_cache" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_clear_cache
	 */
	protected function clearCache()
	{
		$files   = array();
		$files[] = $this->getConfig()->getRedirectKeywordCachePath();
		$files[] = $this->getConfig()->getRedirectSkipKeywordCachePath();

		$errorFiles = array();
		foreach ($files as $file) {
			if (@file_exists($file) && is_file($file)) {
				if (!@unlink($file)) {
					$errorFiles[] = $file;
				}
			}
		}

		if (!empty($errorFiles)) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_FILE_DELETE_ERROR,
				"Cannot delete files (" . implode(', ', $errorFiles) . ")",
				true
			);
		}

		$response = $this->getResponse();
		if (empty($response)) {
			$this->setResponse(new ShopgatePluginApiResponseAppJson($this->getTraceId()));
		}
	}

	/**
	 * Represents the "get_orders" action.
	 *
	 * @throws ShopgateLibraryException
	 * @see http://wiki.shopgate.com/Shopgate_Plugin_API_get_orders
	 * @todo
	 */
	protected function getOrders()
	{
		/**** not yet implemented ****/
	}

	###############
	### Helpers ###
	###############

	/**
	 * @return array
	 */
	private function getPhpSettings()
	{
		$settingDetails = array();
		$allSettings    = function_exists('ini_get_all') ? ini_get_all() : array();
		$settings       = array(
			'max_execution_time',
			'memory_limit',
			'allow_call_time_pass_reference',
			'disable_functions',
			'display_errors',
			'file_uploads',
			'include_path',
			'register_globals',
			'safe_mode'
		);

		foreach ($settings as $setting) {
			$settingDetails[$setting] = (!empty($allSettings[$setting]))
				? $allSettings[$setting]
				: 'undefined';
		}

		return $settingDetails;
	}

	/**
	 * @return array
	 */
	private function getPermissions()
	{
		$permissions = array();
		$files       = array(
			# default paths
			SHOPGATE_BASE_DIR . '/config/myconfig.php',
			$this->getConfig()->getExportFolderPath(),
			$this->getConfig()->getLogFolderPath(),
			$this->getConfig()->getCacheFolderPath(),

			# csv files
			$this->getConfig()->getItemsCsvPath(),
			$this->getConfig()->getCategoriesCsvPath(),
			$this->getConfig()->getReviewsCsvPath(),

			# log files
			$this->getConfig()->getAccessLogPath(),
			$this->getConfig()->getRequestLogPath(),
			$this->getConfig()->getErrorLogPath(),
			$this->getConfig()->getDebugLogPath(),

			# cache files
			$this->getConfig()->getRedirectKeywordCachePath(),
			$this->getConfig()->getRedirectSkipKeywordCachePath(),
		);

		foreach ($files as $file) {
			$permissions[] = $this->_getFileMeta($file, 1);
		}

		return $permissions;
	}

	/**
	 * get meta data for given file.
	 * if file doesn't exists, move up to parent directory
	 *
	 * @param string $file
	 * @param int    $parentLevel max numbers of parent directory lookups
	 *
	 * @return array with file meta data
	 */
	private function _getFileMeta($file, $parentLevel = 0)
	{
		$meta = array('file' => $file);

		if ($meta['exist'] = (bool)file_exists($file)) {
			$meta['writeable'] = (bool)is_writable($file);

			$uid = fileowner($file);
			if (function_exists('posix_getpwuid')) {
				$uinfo = posix_getpwuid($uid);
				$uid   = $uinfo['name'];
			}

			$gid = filegroup($file);
			if (function_exists('posix_getgrgid')) {
				$ginfo = posix_getgrgid($gid);
				$gid   = $ginfo['name'];
			}

			$meta['owner']                  = $uid;
			$meta['group']                  = $gid;
			$meta['permission']             = substr(sprintf('%o', fileperms($file)), -4);
			$meta['last_modification_time'] = date('d.m.Y H:i:s', filemtime($file));

			if (is_file($file)) {
				$meta['filesize'] = round(filesize($file) / (1024 * 1024), 4) . ' MB';
			}
		} else {
			if ($parentLevel > 0) {
				$fInfo = pathinfo($file);
				if (file_exists($fInfo['dirname'])) {
					$meta['parent_dir'] = $this->_getFileMeta($fInfo['dirname'], --$parentLevel);
				}
			}
		}

		return $meta;
	}
}

/**
 * Class ShopgateMerchantApi
 *
 * @method ShopgateAuthentificationServiceInterface getAuthService()
 * @method ShopgateMerchantApi setAuthService(ShopgateAuthentificationServiceInterface $value)
 * @method string getShopNumber()
 * @method ShopgateMerchantApi setShopNumber(string $value)
 * @method string getApiUrl()
 * @method ShopgateMerchantApi setApiUrl(string $value)
 */
class ShopgateMerchantApi extends ShopgateObject implements ShopgateMerchantApiInterface
{
	/**
	 * @param ShopgateAuthentificationServiceInterface $authService
	 * @param                                          $shopNumber
	 * @param                                          $apiUrl
	 */
	public function __construct(ShopgateAuthentificationServiceInterface $authService, $shopNumber, $apiUrl)
	{
		$this->setAuthService($authService);
		$this->setShopNumber($shopNumber);
		$this->setApiUrl($apiUrl);
	}

	/**
	 * Returns an array of curl-options for requests
	 *
	 * @param mixed[] $override cURL options to override for this request.
	 *
	 * @return mixed[] The default cURL options for a Shopgate Merchant API request merged with the options in $override.
	 */
	protected function getCurlOptArray($override = array())
	{
		$opt                         = array();
		$opt[CURLOPT_HEADER]         = false;
		$opt[CURLOPT_USERAGENT]      = 'ShopgatePlugin/' . (defined('SHOPGATE_PLUGIN_VERSION')
				? SHOPGATE_PLUGIN_VERSION
				: 'called outside plugin');
		$opt[CURLOPT_SSL_VERIFYPEER] = false;
		$opt[CURLOPT_RETURNTRANSFER] = true;
		$opt[CURLOPT_HTTPHEADER]     = array(
			'X-Shopgate-Library-Version: ' . SHOPGATE_LIBRARY_VERSION,
			'X-Shopgate-Plugin-Version: '
			. (defined('SHOPGATE_PLUGIN_VERSION')
				? SHOPGATE_PLUGIN_VERSION
				: 'called outside plugin'),
			$this->getAuthService()->buildAuthUserHeader(),
			$this->getAuthService()->buildMerchantApiAuthTokenHeader()
		);

		$opt[CURLOPT_TIMEOUT] = 30; // Default timeout 30sec
		$opt[CURLOPT_POST]    = true;

		return ($override + $opt);
	}

	/**
	 * Prepares the request and sends it to the configured Shopgate Merchant API.
	 *
	 * @param array $parameters      The parameters to send.
	 * @param array $curlOptOverride cURL options to override for this request.
	 *
	 * @return ShopgateMerchantApiResponse The response object.
	 * @throws ShopgateMerchantApiException
	 * @throws ShopgateLibraryException in case the connection can't be established, the response is invalid or an error occured.
	 */
	protected function sendRequest($parameters, $curlOptOverride = array())
	{
		$parameters['shop_number'] = $this->getShopNumber();
		$parameters['trace_id']    = 'spa-' . uniqid();

		$this->log(
			 'Sending request to "' . $this->getApiUrl() . '": '
			 . ShopgateLogger::getInstance()->cleanParamsForLog($parameters),
			 ShopgateLogger::LOGTYPE_REQUEST
		);

		// init new auth session and generate cURL options
		$this->getAuthService()->startNewSession();
		$curlOpt = $this->getCurlOptArray($curlOptOverride);

		// init cURL connection and send the request
		$curl = curl_init($this->getApiUrl());
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parameters));
		curl_setopt_array($curl, $curlOpt);
		$response = curl_exec($curl);
		curl_close($curl);

		// check the result
		if (!$response) {
			// exception without logging - this might cause spamming your logs and we will know when our API is offline anyways
			throw new ShopgateLibraryException(ShopgateLibraryException::MERCHANT_API_NO_CONNECTION, null, false, false);
		}

		$decodedResponse = $this->jsonDecode($response, true);

		if (empty($decodedResponse)) {
			// exception without logging - this might cause spamming your logs and we will know when our API is offline anyways
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'Response: ' . $response,
				true,
				false
			);
		}

		$responseObject = new ShopgateMerchantApiResponse($decodedResponse);

		if ($decodedResponse['error'] != 0) {
			throw new ShopgateMerchantApiException(
				$decodedResponse['error'],
				$decodedResponse['error_text'],
				$responseObject
			);
		}

		return $responseObject;
	}

	######################################################################
	## Following methods represent the Shopgate Merchant API's actions: ##
	######################################################################

	######################################################################
	## Orders                                                           ##
	######################################################################
	/**
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 * @throws ShopgateLibraryException
	 */
	public function getOrders($parameters)
	{
		$request  = array(
			'action' => 'get_orders',
		);
		$request  = array_merge($request, $parameters);
		$response = $this->sendRequest($request);

		// check and reorganize the data of the SMA response
		$data = $response->getData();
		if (empty($data['orders']) || !is_array($data['orders'])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'"orders" is not set or not an array. Response: ' . var_export($data, true)
			);
		}

		$orders = array();
		foreach ($data['orders'] as $order) {
			$orders[] = new ShopgateOrder($order);
		}

		// put the reorganized data into the response object and return ist
		$response->setData($orders);
		return $response;
	}

	/**
	 * @param string $orderNumber
	 * @param string $shippingServiceId
	 * @param int    $trackingNumber
	 * @param bool   $markAsCompleted
	 * @param bool   $sendCustomerEmail
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function addOrderDeliveryNote(
		$orderNumber, $shippingServiceId, $trackingNumber, $markAsCompleted = false, $sendCustomerEmail = false
	) {
		$request = array(
			'action'              => 'add_order_delivery_note',
			'order_number'        => $orderNumber,
			'shipping_service_id' => $shippingServiceId,
			'tracking_number'     => (string)$trackingNumber,
			'mark_as_completed'   => $markAsCompleted,
			'send_customer_email' => $sendCustomerEmail,
		);

		return $this->sendRequest($request);
	}

	/**
	 * @param string $orderNumber
	 * @param bool   $sendCustomerEmail
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function setOrderShippingCompleted($orderNumber, $sendCustomerEmail = false)
	{
		$request = array(
			'action'              => 'set_order_shipping_completed',
			'order_number'        => $orderNumber,
			'send_customer_email' => $sendCustomerEmail,
		);

		return $this->sendRequest($request);
	}

	/**
	 * @param string $orderNumber
	 * @param bool   $cancelCompleteOrder
	 * @param array  $cancellationItems
	 * @param bool   $cancelShipping
	 * @param string $cancellationNote
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function cancelOrder(
		$orderNumber, $cancelCompleteOrder = false, $cancellationItems = array(), $cancelShipping = false,
		$cancellationNote = ''
	) {
		$request = array(
			'action'                => 'cancel_order',
			'order_number'          => $orderNumber,
			'cancel_complete_order' => $cancelCompleteOrder,
			'cancellation_items'    => $cancellationItems,
			'cancel_shipping'       => $cancelShipping,
			'cancellation_note'     => $cancellationNote,
		);

		return $this->sendRequest($request);
	}

	######################################################################
	## Mobile Redirect                                                  ##
	######################################################################
	/**
	 * This method is deprecated, please use getMobileRedirectUserAgents().
	 *
	 * @deprecated
	 * @return array|mixed
	 */
	public function getMobileRedirectKeywords()
	{
		$request = array(
			'action' => 'get_mobile_redirect_keywords',
		);

		$response = $this->sendRequest($request, array(CURLOPT_TIMEOUT => 1));
		return $response->getData();
	}

	/**
	 * @return array|mixed
	 * @throws ShopgateLibraryException
	 */
	public function getMobileRedirectUserAgents()
	{
		$request  = array(
			'action' => 'get_mobile_redirect_user_agents',
		);
		$response = $this->sendRequest($request, array(CURLOPT_TIMEOUT => 1));

		$responseData = $response->getData();
		if (!isset($responseData["keywords"]) || !isset($responseData["skip_keywords"])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				"\"keyword\" or \"skip_keyword\" is not set. Response: " . var_export($responseData, true)
			);
		}

		return $response->getData();
	}

	######################################################################
	## Items                                                            ##
	######################################################################
	/**
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 * @throws ShopgateLibraryException
	 */
	public function getItems($parameters)
	{
		$parameters['action'] = 'get_items';
		$response             = $this->sendRequest($parameters);

		// check and reorganize the data of the SMA response
		$data = $response->getData();
		if (empty($data['items']) || !is_array($data['items'])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'"items" is not set or not an array. Response: ' . var_export($data, true)
			);
		}

		$items = array();
		foreach ($data['items'] as $item) {
			$items[] = new ShopgateItem($item);
		}

		// put the reorganized data into the response object and return ist
		$response->setData($items);
		return $response;
	}

	/**
	 * @param mixed[]|ShopgateItem $item
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function addItem($item)
	{
		$request = ($item instanceof ShopgateItem)
			? $item->toArray()
			: $item;

		$request['action'] = 'add_item';

		return $this->sendRequest($request);
	}

	/**
	 * @param mixed[]|ShopgateItem $item
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function updateItem($item)
	{
		$request = ($item instanceof ShopgateItem)
			? $item->toArray()
			: $item;

		$request['action'] = 'update_item';

		return $this->sendRequest($request);
	}

	/**
	 * @param string $itemNumber
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function deleteItem($itemNumber)
	{
		$request = array(
			'action'      => 'delete_item',
			'item_number' => $itemNumber,
		);

		return $this->sendRequest($request);
	}

	/**
	 * @param mixed[]|ShopgateItem[] $items
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function batchAddItems($items)
	{
		$request = array(
			'items'  => array(),
			'action' => 'batch_add_items',
		);

		foreach ($items as $item) {
			$request['items'][] = ($item instanceof ShopgateItem)
				? $item->toArray()
				: $item;
		}

		return $this->sendRequest($request);
	}

	/**
	 * @param mixed[]|ShopgateItem[] $items
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function batchUpdateItems($items)
	{
		$request = array(
			'items'  => array(),
			'action' => 'batch_update_items',
		);

		foreach ($items as $item) {
			$request['items'][] = ($item instanceof ShopgateItem)
				? $item->toArray()
				: $item;
		}

		return $this->sendRequest($request);
	}

	######################################################################
	## Categories                                                       ##
	######################################################################
	/**
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 * @throws ShopgateLibraryException
	 */
	public function getCategories($parameters)
	{
		$parameters['action'] = 'get_categories';
		$response             = $this->sendRequest($parameters);

		// check and reorganize the data of the SMA response
		$data = $response->getData();
		if (empty($data['categories']) || !is_array($data['categories'])) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::MERCHANT_API_INVALID_RESPONSE,
				'"categories" is not set or not an array. Response: ' . var_export($data, true)
			);
		}

		$categories = array();
		foreach ($data['categories'] as $category) {
			$categories[] = new ShopgateCategory($category);
		}

		// put the reorganized data into the response object and return ist
		$response->setData($categories);

		return $response;
	}

	/**
	 * @param mixed[]|ShopgateCategory $category
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function addCategory($category)
	{
		$request = ($category instanceof ShopgateCategory)
			? $category->toArray()
			: $category;

		$request['action'] = 'add_category';

		return $this->sendRequest($request);
	}

	/**
	 * @param mixed[]|ShopgateCategory $category
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function updateCategory($category)
	{
		$request = ($category instanceof ShopgateCategory)
			? $category->toArray()
			: $category;

		$request['action'] = 'update_category';

		return $this->sendRequest($request);
	}

	/**
	 * @param string $categoryNumber
	 * @param bool   $deleteSubCategories
	 * @param bool   $deleteItems
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function deleteCategory($categoryNumber, $deleteSubCategories = false, $deleteItems = false)
	{
		$request = array(
			'action'               => 'delete_category',
			'category_number'      => $categoryNumber,
			'delete_subcategories' => $deleteSubCategories ? 1 : 0,
			'delete_items'         => $deleteItems ? 1 : 0,
		);

		return $this->sendRequest($request);
	}

	/**
	 * @param string $itemNumber
	 * @param string $categoryNumber
	 * @param null   $orderIndex
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function addItemToCategory($itemNumber, $categoryNumber, $orderIndex = null)
	{
		$request = array(
			'action'          => 'add_item_to_category',
			'category_number' => $categoryNumber,
			'item_number'     => $itemNumber,
		);

		if (isset($orderIndex)) {
			$request['order_index'] = $orderIndex;
		}

		return $this->sendRequest($request);
	}

	/**
	 * @param string $itemNumber
	 * @param string $categoryNumber
	 *
	 * @return ShopgateMerchantApiResponse
	 */
	public function deleteItemFromCategory($itemNumber, $categoryNumber)
	{
		$request = array(
			'action'          => 'delete_item_from_category',
			'category_number' => $categoryNumber,
			'item_number'     => $itemNumber,
		);

		return $this->sendRequest($request);
	}
}

/**
 * Class ShopgateAuthentificationService
 *
 * @method string getCustomerNumber()
 * @method ShopgateAuthentificationService setCustomerNumber(string $value)
 * @method string getApiKey()
 * @method ShopgateAuthentificationService setApiKey(string $value)
 * @method string getTimestamp()
 * @method ShopgateAuthentificationService setTimestamp(string $value)
 */
class ShopgateAuthentificationService extends ShopgateObject implements ShopgateAuthentificationServiceInterface
{
	/**
	 * @param $customerNumber
	 * @param $apiKey
	 */
	public function __construct($customerNumber, $apiKey)
	{
		$this->setCustomerNumber($customerNumber);
		$this->setApiKey($apiKey);
		$this->startNewSession();
	}

	public function startNewSession()
	{
		$this->setTimestamp(time());
	}

	/**
	 * @return string
	 */
	public function buildAuthUser()
	{
		return $this->getCustomerNumber() . '-' . $this->getTimestamp();
	}

	/**
	 * @return string
	 */
	public function buildAuthUserHeader()
	{
		return self::HEADER_X_SHOPGATE_AUTH_USER . ': ' . $this->buildAuthUser();
	}

	/**
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function buildAuthToken($prefix = 'SMA')
	{
		return $this->buildCustomAuthToken(
					$prefix, $this->getCustomerNumber(), $this->getTimestamp(), $this->getApiKey()
		);
	}

	/**
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function buildAuthTokenHeader($prefix = 'SMA')
	{
		return self::HEADER_X_SHOPGATE_AUTH_TOKEN . ': ' . $this->buildAuthToken($prefix);
	}

	/**
	 * @return string
	 */
	public function buildMerchantApiAuthTokenHeader()
	{
		return $this->buildAuthTokenHeader('SMA');
	}

	/**
	 * @return string
	 */
	public function buildPluginApiAuthTokenHeader()
	{
		return $this->buildAuthTokenHeader('SPA');
	}

	/**
	 * @throws ShopgateLibraryException
	 */
	public function checkAuthentification()
	{
		if (defined('SHOPGATE_DEBUG') && SHOPGATE_DEBUG === 1) {
			return;
		}

		if (empty($_SERVER[self::PHP_X_SHOPGATE_AUTH_USER]) || empty($_SERVER[self::PHP_X_SHOPGATE_AUTH_TOKEN])) {
			throw new ShopgateLibraryException(ShopgateLibraryException::AUTHENTICATION_FAILED, 'No authentication data present.');
		}

		// for convenience
		$name  = $_SERVER[self::PHP_X_SHOPGATE_AUTH_USER];
		$token = $_SERVER[self::PHP_X_SHOPGATE_AUTH_TOKEN];

		// extract customer number and timestamp from username
		$matches = array();
		if (!preg_match('/(?P<customer_number>[1-9][0-9]+)-(?P<timestamp>[1-9][0-9]+)/', $name, $matches)) {
			throw new ShopgateLibraryException(ShopgateLibraryException::AUTHENTICATION_FAILED,
											   'Cannot parse: ' . $name . '.');
		}

		// for convenience
		$customerNumber = $matches['customer_number'];
		$timestamp      = $matches['timestamp'];
		$apiKey         = $this->getApiKey();

		// request shouldn't be older than 30 minutes or more than 30 minutes in the future
		if (abs($this->getTimestamp() - $timestamp) > (30 * 60)) {
			throw new ShopgateLibraryException(ShopgateLibraryException::AUTHENTICATION_FAILED, 'Request too old or too far in the future.');
		}

		// create the authentification-password
		$generatedPassword = $this->buildCustomAuthToken('SPA', $customerNumber, $timestamp, $apiKey);

		// compare customer-number and auth-password and make sure, the API key was set in the configuration
		if (($customerNumber != $this->getCustomerNumber()) || ($token != $generatedPassword)
			|| (empty($apiKey))
		) {
			throw new ShopgateLibraryException(ShopgateLibraryException::AUTHENTICATION_FAILED, 'Invalid authentication data.');
		}
	}

	/**
	 * Generates the auth token with the given parameters.
	 *
	 * @param string $prefix
	 * @param string $customerNumber
	 * @param int    $timestamp
	 * @param string $apiKey
	 *
	 * @throws ShopgateLibraryException when no customer number or API key is set
	 * @return string The SHA-1 hash Auth Token for Shopgate's Authentication
	 */
	protected function buildCustomAuthToken($prefix, $customerNumber, $timestamp, $apiKey)
	{
		if (empty($customerNumber) || empty($apiKey)) {
			throw new ShopgateLibraryException(ShopgateLibraryException::CONFIG_INVALID_VALUE, 'Shopgate customer number or  API key not set.', true, false);
		}

		return sha1("{$prefix}-{$customerNumber}-{$timestamp}-{$apiKey}");
	}
}

/**
 * Wrapper for responses by the Shopgate Plugin API.
 *
 * Each content type is represented by a subclass.
 *
 * @author Shopgate GmbH, 35510 Butzbach, DE
 *
 * @method int getError()
 * @method ShopgateMerchantApiResponse setError(int $value)
 * @method int getErrorText()
 * @method ShopgateMerchantApiResponse setErrorText(int $value)
 * @method int getTraceId()
 * @method ShopgateMerchantApiResponse setTraceId(int $value)
 * @method string getVersion()
 * @method ShopgateMerchantApiResponse setVersion(string $value)
 * @method string getPluginVersion()
 * @method ShopgateMerchantApiResponse setPluginVersion(string $value)
 * @method string getShopgateLibraryVersion()
 * @method ShopgateMerchantApiResponse setShopgateLibraryVersion(string $value)
 */
abstract class ShopgatePluginApiResponse extends ShopgateObject
{
	/**
	 * @param        $traceId
	 * @param string $version
	 * @param null   $pluginVersion
	 */
	public function __construct($traceId, $version = SHOPGATE_LIBRARY_VERSION, $pluginVersion = null)
	{
		$this->setError(0);
		$this->setErrorText(null);
		$this->setTraceId($traceId);
		$this->setVersion($version);
		$this->setPluginVersion(
			 (empty($pluginVersion) && defined('SHOPGATE_PLUGIN_VERSION'))
				 ? SHOPGATE_PLUGIN_VERSION
				 : $pluginVersion
		);
	}

	/**
	 * Marks the response as error.
	 */
	public function markError($code, $message)
	{
		$this->setError($code);
		$this->setErrorText($message);
	}

	abstract public function send();
}

/**
 * @author Shopgate GmbH, 35510 Butzbach, DE
 */
class ShopgatePluginApiResponseTextPlain extends ShopgatePluginApiResponse
{
	public function send()
	{
		header('HTTP/1.0 200 OK');
		header('Content-Type: text/plain; charset=UTF-8');
		echo $this->getData();
		exit;
	}
}

/**
 * @author Shopgate GmbH, 35510 Butzbach, DE
 *
 * @method string getFile()
 * @method ShopgateMerchantApiResponse setFile(string $value)
 */
class ShopgatePluginApiResponseTextCsv extends ShopgatePluginApiResponse
{
	/**
	 * @param array|string $data
	 * @param null         $value
	 *
	 * @return ShopgateObject|void
	 * @throws ShopgateLibraryException
	 */
	public function setData($data, $value = null)
	{
		if (!file_exists($data)) {
			throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_FILE_NOT_FOUND, 'File: ' . $data, true);
		}

		$this->setFile($data);
	}

	public function send()
	{
		$fp = @fopen($this->getFile(), 'r');
		if (!$fp) {
			throw new ShopgateLibraryException(
				ShopgateLibraryException::PLUGIN_FILE_OPEN_ERROR,
				'File: ' . $this->getFile(),
				true
			);
		}

		// output headers ...
		header('HTTP/1.0 200 OK');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . basename($this->getFile()) . '"');

		// ... and csv file
		while ($line = fgets($fp)) {
			echo $line;
		}

		// clean up and leave
		fclose($fp);
		exit;
	}
}

/**
 * @author Shopgate GmbH, 35510 Butzbach, DE
 */
class ShopgatePluginApiResponseAppJson extends ShopgatePluginApiResponse
{
	public function send()
	{
		$this->setShopgateLibraryVersion($this->getVersion());
		header("HTTP/1.0 200 OK");
		header("Content-Type: application/json");
		echo $this->jsonEncode($this->getData());
	}
}

/**
 * Wrapper for responses by the Shopgate Merchant API
 *
 * @author Shopgate GmbH, 35510 Butzbach, DE
 *
 * @method int getSmaVersion()
 * @method ShopgateMerchantApiResponse setSmaVersion(int $value)
 * @method int getTraceId()
 * @method ShopgateMerchantApiResponse setTraceId(int $value)
 * @method int getLimit()
 * @method ShopgateMerchantApiResponse setLimit(int $value)
 * @method int getOffset()
 * @method ShopgateMerchantApiResponse setOffset(int $value)
 * @method bool getHasMoreResults()
 * @method ShopgateMerchantApiResponse setHasMoreResults(bool $value)
 * @method array getErrors()
 * @method ShopgateMerchantApiResponse setErrors(array $value)
 */
class ShopgateMerchantApiResponse extends ShopgateContainer
{
	/**
	 * @param array $data
	 */
	public function __construct($data = array())
	{
		$this->setSmaVersion('');
		$this->setTraceId('');
		$this->setLimit(1);
		$this->setOffset(1);
		$this->setHasMoreResults(false);
		$this->setErrors(array());
		$this->setData($data);
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		return;
	}
}

/**
 * This interface represents the Shopgate Plugin API as described in our wiki.
 *
 * It provides all available actions and calls the plugin implementation's callback methods for data retrieval if necessary.
 *
 * @see    http://wiki.shopgate.com/Shopgate_Plugin_API
 * @author Shopgate GmbH, 35510 Butzbach, DE
 */
interface ShopgatePluginApiInterface
{
	/**
	 * Inspects an incoming request, performs the requested actions, prepares and prints out the response to the requesting entity.
	 *
	 * Note that the method usually returns true or false on completion, depending on the success of the operation. However, some actions such as
	 * the get_*_csv actions, might stop the script after execution to prevent invalid data being appended to the output.
	 *
	 * @param mixed[] $data The incoming request's parameters.
	 *
	 * @return bool false if an error occured, otherwise true.
	 */
	public function handleRequest(array $data = array());
}

/**
 * This class represents the Shopgate Merchant API as described in our wiki.
 *
 * It provides all available actions, calls to the configured API, retrieves, parses and formats the data.
 *
 * @author Shopgate GmbH, 35510 Butzbach, DE
 */
interface ShopgateMerchantApiInterface
{
	######################################################################
	## Orders                                                           ##
	######################################################################
	/**
	 * Represents the "get_orders" action.
	 *
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_get_orders
	 */
	public function getOrders($parameters);

	/**
	 * Represents the "add_order_delivery_note" action.
	 *
	 * @param string $orderNumber
	 * @param string $shippingServiceId
	 * @param int    $trackingNumber
	 * @param bool   $markAsCompleted
	 * @param bool   $sendCustomerMail
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_add_order_delivery_note
	 */
	public function addOrderDeliveryNote(
		$orderNumber, $shippingServiceId, $trackingNumber, $markAsCompleted = false, $sendCustomerMail = true
	);

	/**
	 * Represents the "set_order_shipping_completed" action.
	 *
	 * @param string $orderNumber
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_set_order_shipping_completed
	 */
	public function setOrderShippingCompleted($orderNumber);

	/**
	 * Represents the "cancel_order" action.
	 *
	 * @param string $orderNumber
	 * @param bool   $cancelCompleteOrder
	 * @param        array ('item_number' => string, 'quantity' => int)[] $cancellationItems
	 * @param bool   $cancelShipping
	 * @param string $cancellationNote
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_cancel_order
	 */
	public function cancelOrder(
		$orderNumber, $cancelCompleteOrder = false, $cancellationItems = array(), $cancelShipping = false,
		$cancellationNote = ''
	);

	######################################################################
	## Mobile Redirect                                                  ##
	######################################################################
	/**
	 * Represents the "get_mobile_redirect_keywords" action.
	 *
	 * This method is deprecated, please use getMobileRedirectUserAgents().
	 *
	 * @return array('keywords' => string[], 'skipKeywords' => string[])
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @deprecated
	 */
	public function getMobileRedirectKeywords();

	/**
	 * Represents the "get_mobile_user_agents" action.
	 *
	 * @return array 'keywords' => string[], 'skip_keywords' => string[]
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_get_mobile_redirect_user_agents
	 */
	public function getMobileRedirectUserAgents();

	######################################################################
	## Items                                                            ##
	######################################################################
	/**
	 * Represents the "get_items" action.
	 *
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_get_items
	 */
	public function getItems($parameters);

	/**
	 * Represents the "add_item" action.
	 *
	 * @param mixed[]|ShopgateItem $item
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_add_item
	 */
	public function addItem($item);

	/**
	 * Represents the "update_item" action.
	 *
	 * @param mixed[]|ShopgateItem $item
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_update_item
	 */
	public function updateItem($item);

	/**
	 * Represents the "delete_item" action.
	 *
	 * @param string $itemNumber
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_delete_item
	 */
	public function deleteItem($itemNumber);

	/**
	 * Represents the "batch_add_items" action.
	 *
	 * @param mixed[]|ShopgateItem[] $items
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_batch_add_items
	 */
	public function batchAddItems($items);

	/**
	 * Represents the "batch_update_items" action.
	 *
	 * @param mixed[]|ShopgateItem[] $items
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_batch_update_items
	 */
	public function batchUpdateItems($items);

	######################################################################
	## Categories                                                       ##
	######################################################################
	/**
	 * Represents the "get_categories" action.
	 *
	 * @param mixed[] $parameters
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_get_categories
	 */
	public function getCategories($parameters);

	/**
	 * Represents the "add_category" action.
	 *
	 * @param mixed[]|ShopgateCategory $category
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_add_category
	 */
	public function addCategory($category);

	/**
	 * Represents the "update_category" action.
	 *
	 * @param mixed[]|ShopgateCategory $category
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_update_category
	 */
	public function updateCategory($category);

	/**
	 * Represents the "delete_category" action.
	 *
	 * @param string $categoryNumber
	 * @param bool   $deleteSubCategories
	 * @param bool   $deleteItems
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_delete_category
	 */
	public function deleteCategory($categoryNumber, $deleteSubCategories = false, $deleteItems = false);

	/**
	 * Represents the "add_item_to_category" action.
	 *
	 * @param string $itemNumber
	 * @param string $categoryNumber
	 * @param int    $orderIndex
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_add_item_to_category
	 */
	public function addItemToCategory($itemNumber, $categoryNumber, $orderIndex = null);

	/**
	 * Represents the "delete_item_from_category" action.
	 *
	 * @param string $itemNumber
	 * @param string $categoryNumber
	 *
	 * @return ShopgateMerchantApiResponse
	 *
	 * @throws ShopgateLibraryException in case the connection can't be established
	 * @throws ShopgateMerchantApiException in case the response is invalid or an error occured
	 *
	 * @see http://wiki.shopgate.com/Merchant_API_delete_item_from_category
	 */
	public function deleteItemFromCategory($itemNumber, $categoryNumber);
}

/**
 * This class provides methods to check and generate authentification strings.
 *
 * It is used internally by the Shopgate Library to send requests or check incoming requests.
 *
 * To check authentication on incoming request it accesses the $_SERVER variable which should contain the required X header fields for
 * authentication.
 *
 * @author Shopgate GmbH, 35510 Butzbach, DE
 */
interface ShopgateAuthentificationServiceInterface
{
	const HEADER_X_SHOPGATE_AUTH_USER  = 'X-Shopgate-Auth-User';
	const HEADER_X_SHOPGATE_AUTH_TOKEN = 'X-Shopgate-Auth-Token';
	const PHP_X_SHOPGATE_AUTH_USER     = 'HTTP_X_SHOPGATE_AUTH_USER';
	const PHP_X_SHOPGATE_AUTH_TOKEN    = 'HTTP_X_SHOPGATE_AUTH_TOKEN';

	/**
	 * @return string The auth user string.
	 */
	public function buildAuthUser();

	/**
	 * @return string The X-Shopgate-Auth-User HTTP header for an outgoing request.
	 */
	public function buildAuthUserHeader();

	/**
	 * @param $prefix string SMA|SPA
	 *
	 * @return string The auth token string.
	 */
	public function buildAuthToken($prefix = 'SMA');

	/**
	 * @param $prefix string SMA|SPA
	 *
	 * @return string The X-Shopgate-Auth-Token HTTP header.
	 */
	public function buildAuthTokenHeader($prefix = 'SMA');

	/**
	 * @return string The X-Shopgate-Auth-Token HTTP header for an outgoing request.
	 */
	public function buildMerchantApiAuthTokenHeader();

	/**
	 * @return string The X-Shopgate-Auth-Token HTTP header for an incoming request.
	 */
	public function buildPluginApiAuthTokenHeader();

	/**
	 * @throws ShopgateLibraryException if authentication fails
	 */
	public function checkAuthentification();

	/**
	 * Start a new Authentication session
	 */
	public function startNewSession();
}