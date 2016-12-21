<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_ShutdownHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Logging_Strategy_LoggingInterface */
    protected $logging;
    
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider */
    protected $lastErrorProvider;
    
    public function setUp()
    {
        $this->logging = $this
            ->getMockBuilder('Shopgate_Helper_Logging_Strategy_LoggingInterface')
            ->getMock()
        ;
        
        $this->lastErrorProvider = $this
            ->getMockBuilder('Shopgate_Helper_Error_Handling_Shutdown_Handler_LastErrorProvider')
            ->setMethods(array('get'))
            ->getMock()
        ;
    }
    
    public function testLoggerCalledOnShutdownFatalErrors()
    {
        $this->logging
            ->expects($this->exactly(2))
            ->method('log')
            ->with(
                new PHPUnit_Framework_Constraint_IsAnything(), // message
                Shopgate_Helper_Logging_Strategy_LoggingInterface::LOGTYPE_ERROR,
                ''
            )
            ->willReturnOnConsecutiveCalls(true)
        ;
        
        // E_ERROR
        $this->lastErrorProvider
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                array(
                    'type'    => E_ERROR,
                    'message' => 'Call to member function on a non-object.',
                    'file'    => '/var/www/failing_script.php',
                    'line'    => 99
                ),
                array(
                    'type'    => E_USER_ERROR,
                    'message' => 'Call to member function on a non-object.',
                    'file'    => '/var/www/failing_script.php',
                    'line'    => 99
                )
            )
        ;
        
        $SUT = new Shopgate_Helper_Error_Handling_ShutdownHandler($this->logging, $this->lastErrorProvider);
        $SUT->handle(); // E_ERROR
        $SUT->handle(); // E_USER_ERROR
    }
    
    public function testLoggerNotCalledOnShutdownNonFatalError()
    {
        $this->logging->expects($this->never())->method('log');
        
        $this->lastErrorProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                array(
                    'type'    => E_WARNING,
                    'message' => 'Illegal string offset \'bla\'.',
                    'file'    => '/var/www/failing_script.php',
                    'line'    => 99
                )
            )
        ;
        
        $SUT = new Shopgate_Helper_Error_Handling_ShutdownHandler($this->logging, $this->lastErrorProvider);
        $SUT->handle();
    }
    
    public function testLoggerNotCalledOnRegularShutdownOrErrorGetLastNotAvailable()
    {
        $this->logging->expects($this->never())->method('log');
        
        $this->lastErrorProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn(null)
        ;
        
        $SUT = new Shopgate_Helper_Error_Handling_ShutdownHandler($this->logging, $this->lastErrorProvider);
        $SUT->handle();
    }
}