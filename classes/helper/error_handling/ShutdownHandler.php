<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_ShutdownHandler
{
    /** @var Shopgate_Helper_Logging_Strategy_LoggingInterface */
    protected $logging;
    
    /** @var Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider */
    protected $lastErrorProvider;
    
    /**
     * @param Shopgate_Helper_Logging_Strategy_LoggingInterface                 $logging
     * @param Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider $lastErrorProvider
     */
    public function __construct(
        Shopgate_Helper_Logging_Strategy_LoggingInterface $logging,
        Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider $lastErrorProvider
    ) {
        $this->logging           = $logging;
        $this->lastErrorProvider = $lastErrorProvider;
    }
    
    /**
     * Handles errors upon shutdown of PHP.
     *
     * This will look up if a fatal error caused PHP to shut down. If so, the error will be logged to the error log.
     */
    public function handle()
    {
        $error = $this->lastErrorProvider->get();
        
        if ($error === null) {
            return;
        }
        
        if (!($error['type'] & (E_ERROR | E_USER_ERROR))) {
            return;
        }
        
        $this->logging->log(
            'Script stopped due to FATAL error in ' . $error['file'] .
            ' in line ' . $error['line'] .
            ' with message: ' . $error['message']
        );
    }
}