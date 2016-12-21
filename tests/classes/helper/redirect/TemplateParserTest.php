<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

/**
 * @author             Konstantin Kiritsenko <konstantin.kiritsenko@shopgate.com>
 * @group              Shopgate_Library
 * @group              Shopgate_Library_Helpers
 *
 * @coversDefaultClass Shopgate_Helper_Redirect_TemplateParser
 */
class Shopgate_Helper_Redirect_TemplateParserTest extends PHPUnit_Framework_TestCase
{
    /** @var Shopgate_Helper_Redirect_TemplateParser $class */
    protected $class;

    public function setUp()
    {
        $this->class = new Shopgate_Helper_Redirect_TemplateParser();
    }

    /**
     * Tests the most basic regex check, export {variable} name
     *
     * @uses   Shopgate_Model_Redirect_HtmlTagVariable::getData
     *
     * @covers ::getVariables
     */
    public function testGetVariablesSimple()
    {
        $input     = '_shopgate.shop_number = "{shop_number}";';
        $expected  = $this->initVariable('shop_number');
        $variables = $this->class->getVariables($input);
        $returned  = array_shift($variables);

        $this->assertEquals($returned->getData(), $expected->getData());
    }
    
    /**
     * Testing the more complex variable setter
     *
     * @uses   Shopgate_Model_Redirect_HtmlTagVariable::getData
     *
     * @covers ::getVariables
     */
    public function testGetVariablesColon()
    {
        $input     = '{baseUrl}/brand?q={brand_name:urlencoded}';
        $variables = $this->class->getVariables($input);

        $expectedOne = $this->initVariable('baseUrl');
        $returnedOne = array_shift($variables);

        $expectedTwo = $this->initVariable('brand_name', 'urlencoded');
        $returnedTwo = array_shift($variables);

        $this->assertEquals($returnedOne->getData(), $expectedOne->getData());
        $this->assertEquals($returnedTwo->getData(), $expectedTwo->getData());
    }
    
    /**
     * Simple empty array return check
     *
     * @covers ::getVariables
     */
    public function testEmpty()
    {
        $variables = $this->class->getVariables('test string');

        $this->assertEmpty($variables);

    }

    /**
     * Multi-line template test
     *
     * @covers ::getVariables
     */
    public function testGetVariableMultiline()
    {
        $template  = '{link_tags}
                      <script type="text/javascript">
                          _shopgate.shop_number = "{shop_number}";
                          _shopgate.redirect = "{redirect_code}";
                          _shopgate.host = (("https:" == document.location.protocol) ? "{ssl_url}" : "{non_ssl_url}");
                      </script>
                      <!-- END SHOPGATE -->';
        $variables = $this->class->getVariables($template);

        $this->assertCount(5, $variables);
    }
    
    public function testCurlyBracesNotAlwaysVariables() {
        // some invalid variable names that should not be detected as variables
        $this->assertCount(0, $this->class->getVariables('{a b}'));
        $this->assertCount(0, $this->class->getVariables('{ ab}'));
        $this->assertCount(0, $this->class->getVariables('{ab }'));
        $this->assertCount(0, $this->class->getVariables('{ ab }'));
        $this->assertCount(0, $this->class->getVariables('{ a b }'));
        $this->assertCount(0, $this->class->getVariables('{ ab }'));
        $this->assertCount(0, $this->class->getVariables('fdgdf {a b} gdfgdf'));
        
        // The dash is explicitly invalid for now as we don't have any such variables up until now. But this is only an
        // assumption that can change. If it changes, this assertion needs to be updated accordingly.
        $this->assertCount(0, $this->class->getVariables('{a-b}'));
        
        // curly braced JS blocks that should not be detected as variables
        $this->assertCount(0, $this->class->getVariables('function minified(a,b){ var a="test"; };'));
        $this->assertCount(0, $this->class->getVariables('function minified(a,b){var a="test"; };'));
        $this->assertCount(0, $this->class->getVariables('function minified(a,b){ var a="test";};'));
        $this->assertCount(0, $this->class->getVariables('function minified(a,b){var a="test";};'));
        
        // variable inside curly braced JS block
        $this->assertCount(1, $this->class->getVariables('function minified(a,b){var {abc}="test";};'));
    }
    
    /**
     * Helps initializing the variable model
     *
     * @param string $name     - name to set
     * @param string $function - function name to set
     *
     * @return Shopgate_Model_Redirect_HtmlTagVariable
     */
    private function initVariable($name, $function = '')
    {
        $expected = new Shopgate_Model_Redirect_HtmlTagVariable();
        $expected->setName($name);

        if ($function) {
            $expected->setFunctionName($function);
        }

        return $expected;
    }

    /**
     * Prep for garbage collect
     */
    public function tearDown()
    {
        unset($this->class);
    }
}
