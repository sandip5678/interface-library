<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderReflectionTest extends PHPUnit_Framework_TestCase
{
    /** @var Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderReflection */
    protected $SUT;
    
    public function setUp()
    {
        // load some defined functions for testing; TODO move to some bootstrap.php or the like
        include_once(dirname(__FILE__) . '/../../../../stubs/functions.php');
        include_once(dirname(__FILE__) . '/../../../../stubs/ShopgateTestClass.php');
        
        $this->SUT = new Shopgate_Helper_Logging_Stack_Trace_NamedParameterProviderReflection();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $this->SUT = null;
    }
    
    public function testUndefinedFunction()
    {
        $functionName = 'shopgateTestFunctionUndefined';
        
        $this->assertFalse(function_exists($functionName));
        
        $this->assertEquals(
            array(123, 456),
            $this->SUT->get('', $functionName, array(123, 456))
        );
    }
    
    public function testUndefinedClass()
    {
        $className = 'ShopgateTestClassUndefined';
        
        $this->assertFalse(class_exists($className));
        
        $this->assertEquals(
            array(123, 456),
            $this->SUT->get($className, '', array(123, 456))
        );
    }
    
    public function testUndefinedMethod()
    {
        $className  = 'ShopgateTestClass';
        $methodName = 'methodUndefined';
        
        $this->assertTrue(class_exists($className));
        $this->assertFalse(method_exists($className, $methodName));
        
        $this->assertEquals(
            array(123, 456),
            $this->SUT->get($className, $methodName, array(123, 456))
        );
    }
    
    public function testPrivateMethod()
    {
        $className  = 'ShopgateTestClass';
        $methodName = 'methodPrivate';
        
        $this->assertTrue(class_exists($className));
        $this->assertTrue(method_exists($className, $methodName));
        
        $this->assertEquals(
            array('one' => 123),
            $this->SUT->get($className, $methodName, array(123))
        );
    }
    
    public function testProtectedMethod()
    {
        $className  = 'ShopgateTestClass';
        $methodName = 'methodProtected';
        
        $this->assertTrue(class_exists($className));
        $this->assertTrue(method_exists($className, $methodName));
        
        $this->assertEquals(
            array('one' => 123),
            $this->SUT->get($className, $methodName, array(123))
        );
    }
    
    public function testDefinedFunction()
    {
        $this->assertEquals(
            array(),
            $this->SUT->get('', 'shopgateTestFunctionWithNoParameters', array())
        );
        
        $this->assertEquals(
            array('unnamed argument 0' => 123, 'unnamed argument 1' => 456),
            $this->SUT->get('', 'shopgateTestFunctionWithNoParameters', array(123, 456))
        );
        
        $this->assertEquals(
            array('one' => 123),
            $this->SUT->get('', 'shopgateTestFunctionWithOneParameter', array(123))
        );
        
        $this->assertEquals(
            array('one' => 123, 'unnamed argument 1' => 456),
            $this->SUT->get('', 'shopgateTestFunctionWithOneParameter', array(123, 456))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => '[defaultValue:optional]'),
            $this->SUT->get('', 'shopgateTestFunctionWithTwoParameters', array(123))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => 'test'),
            $this->SUT->get('', 'shopgateTestFunctionWithTwoParameters', array(123, 'test'))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => 456, 'unnamed argument 2' => 'test'),
            $this->SUT->get('', 'shopgateTestFunctionWithTwoParameters', array(123, 456, 'test'))
        );
    }
    
    public function testDefinedMethod()
    {
        $this->assertEquals(
            array(),
            $this->SUT->get('ShopgateTestClass', 'methodWithNoParameters', array())
        );
        
        $this->assertEquals(
            array('unnamed argument 0' => 123, 'unnamed argument 1' => 456),
            $this->SUT->get('ShopgateTestClass', 'methodWithNoParameters', array(123, 456))
        );
        
        $this->assertEquals(
            array('one' => 123),
            $this->SUT->get('ShopgateTestClass', 'methodWithOneParameter', array(123))
        );
        
        $this->assertEquals(
            array('one' => 123, 'unnamed argument 1' => 456),
            $this->SUT->get('ShopgateTestClass', 'methodWithOneParameter', array(123, 456))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => '[defaultValue:optional]'),
            $this->SUT->get('ShopgateTestClass', 'methodWithTwoParameters', array(123))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => 'test'),
            $this->SUT->get('ShopgateTestClass', 'methodWithTwoParameters', array(123, 'test'))
        );
        
        $this->assertEquals(
            array('one' => 123, 'two' => 456, 'unnamed argument 2' => 'test'),
            $this->SUT->get('ShopgateTestClass', 'methodWithTwoParameters', array(123, 456, 'test'))
        );
    }
}