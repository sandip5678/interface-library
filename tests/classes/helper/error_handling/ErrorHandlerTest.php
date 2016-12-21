<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Error_Handling_ErrorHandlerTest extends PHPUnit_Framework_TestCase
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
    
    public function testStackTraceGeneratorAndLoggerCalled()
    {
        $stackTrace = 'Dummy Stack Trace';
        
        $this->stackTraceGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(new PHPUnit_Framework_Constraint_Exception('Exception'))
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
        
        $SUT = new Shopgate_Helper_Error_Handling_ErrorHandler($this->stackTraceGenerator, $this->logging);
        $SUT->handle(123, 'a message', '/var/www/failingscript.php', 100, array());
    }
    
    public function testStackTraceGeneratorAndLoggerNotCalledOnErrorSupression()
    {
        $this->stackTraceGenerator
            ->expects($this->never())
            ->method('generate')
        ;
        
        $this->logging
            ->expects($this->never())
            ->method('log')
        ;
        
        $SUT = new Shopgate_Helper_Error_Handling_ErrorHandler($this->stackTraceGenerator, $this->logging);
        $SUT->handle(0, 'a message', '/var/www/failingscript.php', 100, array());
    }
    
    public function testUseInternalErrorHandler()
    {
        // from php.net about the error handling function:
        // "If the function returns FALSE then the normal error handler continues."
        
        // internal error handler should be used by default
        $SUT = new Shopgate_Helper_Error_Handling_ErrorHandler($this->stackTraceGenerator, $this->logging);
        $this->assertFalse($SUT->handle(123, 'a message', '/var/www/failingscript.php', 100, array()));
        
        // internal error handler explicitly used
        $SUT = new Shopgate_Helper_Error_Handling_ErrorHandler($this->stackTraceGenerator, $this->logging, false);
        $this->assertFalse($SUT->handle(123, 'a message', '/var/www/failingscript.php', 100, array()));
        
        // internal error handler disabled
        $SUT = new Shopgate_Helper_Error_Handling_ErrorHandler($this->stackTraceGenerator, $this->logging, true);
        $this->assertTrue($SUT->handle(123, 'a message', '/var/www/failingscript.php', 100, array()));
    }
}