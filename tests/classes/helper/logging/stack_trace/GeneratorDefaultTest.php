<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Logging_Stack_Trace_GeneratorDefaultTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Logging_Obfuscator */
    protected $obfuscator;
    
    /** @var PHPUnit_Framework_MockObject_MockObject|Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface */
    protected $namedParameterProvider;
    
    /** @var Shopgate_Helper_Logging_Stack_Trace_GeneratorDefaultTestFixtureBuilder */
    protected $fixtureProvider;
    
    public function setUp()
    {
        // workaround for PHP versions below 7: load Throwable interface; TODO move to some bootstrap.php or the like
        include_once(dirname(__FILE__) . '/../../../../stubs/Throwable.php');
        
        $this->obfuscator = $this
            ->getMockBuilder('Shopgate_Helper_Logging_Obfuscator')
            ->getMock()
        ;
        
        $this->namedParameterProvider = $this
            ->getMockBuilder('Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderInterface')
            ->getMockForAbstractClass()
        ;
        
        include_once(dirname(__FILE__)
            . '/../../../../fixtures/helper/logging/stack_trace/GeneratorDefaultTestFixtureBuilder.php');
        $this->fixtureProvider = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefaultTestFixtureBuilder($this);
    }
    
    public function testSimpleExceptionProducesProperStackTrace()
    {
        $this->obfuscator
            ->expects($this->any())
            ->method('cleanParamsForLog')
            ->withAnyParameters()
            ->willReturnArgument(0)
        ;
        
        $this->namedParameterProvider->expects($this->any())->method('get')->willReturnArgument(2);
        
        $SUT = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            $this->obfuscator,
            $this->namedParameterProvider
        );
        
        $this->assertEquals(
            $this->fixtureProvider->getSimpleExceptionExpected(),
            $SUT->generate($this->fixtureProvider->getSimpleException()));
    }
    
    public function testExceptionWithPreviousExceptionsProducesProperStackTrace()
    {
        $this->obfuscator
            ->expects($this->any())
            ->method('cleanParamsForLog')
            ->withAnyParameters()
            ->willReturnArgument(0)
        ;
        
        $this->namedParameterProvider->expects($this->any())->method('get')->willReturnArgument(2);
        
        $SUT = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            $this->obfuscator,
            $this->namedParameterProvider
        );
        
        $this->assertEquals(
            $this->fixtureProvider->getExceptionWithPreviousExceptionsExpected(),
            $SUT->generate($this->fixtureProvider->getExceptionWithPreviousExceptions())
        );
    }
    
    public function testDepthLimitIsHonoured()
    {
        $this->obfuscator
            ->expects($this->any())
            ->method('cleanParamsForLog')
            ->withAnyParameters()
            ->willReturnArgument(0)
        ;
        
        $this->namedParameterProvider->expects($this->any())->method('get')->willReturnArgument(2);
        
        $SUT = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            $this->obfuscator,
            $this->namedParameterProvider
        );
        
        $this->assertEquals(
            $this->fixtureProvider->getExceptionWithPreviousExceptionsDepth2Expected(),
            $SUT->generate($this->fixtureProvider->getExceptionWithPreviousExceptions(), 2)
        );
    }
    
    public function testNamedParameterProviderAndObfuscatorIsCalled()
    {
        // build arguments for $this->fixtureProvider->get() from ShopgateLibraryException fixture
        list($args1, $args2, $args3, $args4) = $this->fixtureProvider->buildMockFromTraceFixture(
            $this->fixtureProvider->getTraceFixture('ShopgateLibraryExceptionStub')
        );
        
        // build arguments for $this->fixtureProvider->get() from LoginException fixture
        list($args5, $args6, $args7, $args8, $args9) = $this->fixtureProvider->buildMockFromTraceFixture(
            $this->fixtureProvider->getTraceFixture('LoginException')
        );
        
        $this->namedParameterProvider
            ->expects($this->exactly(9))
            ->method('get')
            ->withConsecutive($args1, $args2, $args3, $args4, $args5, $args6, $args7, $args8, $args9)
            ->willReturn(array())
        ;
        
        $this->obfuscator
            ->expects($this->exactly(9))
            ->method('cleanParamsForLog')
            ->willReturn(array())
        ;
        
        $SUT = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            $this->obfuscator,
            $this->namedParameterProvider
        );
        
        $SUT->generate($this->fixtureProvider->getExceptionExampleForFailedGetCustomer());
    }
    
    public function testIntegrationObfuscation()
    {
        // build arguments for $this->fixtureProvider->get() from ShopgateLibraryException fixture
        list($args1, $args2, $args3, $args4) = $this->fixtureProvider->buildMockFromTraceFixture(
            $this->fixtureProvider->getTraceFixture('ShopgateLibraryExceptionStub')
        );
        
        // build arguments for $this->fixtureProvider->get() from LoginException fixture
        list($args5, $args6, $args7, $args8, $args9) = $this->fixtureProvider->buildMockFromTraceFixture(
            $this->fixtureProvider->getTraceFixture('LoginException')
        );
        
        $this->namedParameterProvider
            ->expects($this->exactly(9))
            ->method('get')
            ->withConsecutive($args1, $args2, $args3, $args4, $args5, $args6, $args7, $args8, $args9)
            ->willReturnOnConsecutiveCalls(
            // for ShopgateLibraryException
                array('user' => 'herp@derp.com', 'pass' => 'herpiderp'), // ShopgatePluginMyCart::getCustomer()
                array(),                                                 // ShopgatePluginApi::getCustomer()
                array('data' => array()),                                // ShopgatePluginApi::handleRequest()
                array('data' => array()),                                // ShopgatePlugin::handleRequest()
                
                // for LoginException
                array('user' => 'herp@derp.com', 'pass' => 'herpiderp'), // User::login()
                array('user' => 'herp@derp.com', 'pass' => 'herpiderp'), // ShopgatePluginMyCart::getCustomer()
                array(),                                                 // ShopgatePluginApi::getCustomer()
                array('data' => array()),                                // ShopgatePluginApi::handleRequest()
                array('data' => array())                                 // ShopgatePlugin::handleRequest()
            )
        ;
    
        // use the real obfuscator as this is an integration test
        $SUT = new Shopgate_Helper_Logging_Stack_Trace_GeneratorDefault(
            new Shopgate_Helper_Logging_Obfuscator(),
            $this->namedParameterProvider
        );
        
        $this->assertEquals(
            $this->fixtureProvider->getExceptionExampleForFailedGetCustomerObfuscationExpected(),
            $SUT->generate($this->fixtureProvider->getExceptionExampleForFailedGetCustomer())
        );
    }
}