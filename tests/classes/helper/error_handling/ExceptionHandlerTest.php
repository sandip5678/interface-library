<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface */
    protected $stackTraceGenerator;
    
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Logging_Strategy_LoggingInterface */
    protected $logging;
    
    public function setUp()
    {
        $this->stackTraceGenerator = $this
            ->getMockBuilder('Shopgate_Helper_Logging_Stack_Trace_GeneratorInterface')
            ->getMock()
        ;
        
        $this->logging = $this
            ->getMockBuilder('Shopgate_Helper_Logging_Strategy_LoggingInterface')
            ->getMock()
        ;
    }
    
    public function testStackTraceGeneratorAndLoggerCalledOnShopgateLibraryException()
    {
        $exception = $this
            ->getMockBuilder('ShopgateLibraryException')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $stackTrace = 'Dummy Stack Trace';
        
        $this->stackTraceGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($exception)
            ->willReturn($stackTrace)
        ;
        
        $this->logging
            ->expects($this->once())
            ->method('log')
            ->with(
                new PHPUnit_Framework_Constraint_IsAnything(), // message
                Shopgate_Helper_Logging_Strategy_LoggingInterface::LOGTYPE_ERROR,
                $stackTrace
            )
            ->willReturn(true)
        ;
        
        $SUT = new Shopgate_Helper_Error_Handling_ExceptionHandler($this->stackTraceGenerator, $this->logging);
        $SUT->handle($exception);
    }
    
    public function testStackTraceGeneratorAndLoggerNotCalledOnNonShopgateLibraryException()
    {
        $this->stackTraceGenerator->expects($this->never())->method('generate');
        $this->logging->expects($this->never())->method('log');
        
        $SUT = new Shopgate_Helper_Error_Handling_ExceptionHandler($this->stackTraceGenerator, $this->logging);
        $SUT->handle(new Exception());
    }
}