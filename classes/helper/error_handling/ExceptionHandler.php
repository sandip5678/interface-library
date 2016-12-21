<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_ExceptionHandler
{
    /** @var Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface */
    protected $stackTraceGenerator;
    
    /** @var Shopgate_Helper_Logging_Strategy_LoggingInterface */
    protected $logging;
    
    /**
     * @param Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface $stackTraceGenerator
     * @param Shopgate_Helper_Logging_Strategy_LoggingInterface      $logging
     */
    public function __construct(
        Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface $stackTraceGenerator,
        Shopgate_Helper_Logging_Strategy_LoggingInterface $logging
    ) {
        $this->stackTraceGenerator = $stackTraceGenerator;
        $this->logging             = $logging;
    }
    
    /**
     * Handles uncaught exceptions of type ShopgateLibraryException.
     *
     * This handler will take any Exception or Throwable but will only act upon receiving a ShopgateLibraryException.
     * In that case it will log a stack trace to the error log. In all other cases it will return without doing
     * anything.
     *
     * @param Throwable|Exception $e Will accept Throwable for PHP 7 or Exception for PHP < 7.
     *
     * @see http://php.net/manual/en/function.set-exception-handler.php
     */
    public function handle($e)
    {
        if (!($e instanceof ShopgateLibraryException)) {
            return;
        }
        
        $this->logging->log(
            'FATAL: Uncaught ShopgateLibraryException',
            Shopgate_Helper_Logging_Strategy_LoggingInterface::LOGTYPE_ERROR,
            $this->stackTraceGenerator->generate($e)
        );
    }
}