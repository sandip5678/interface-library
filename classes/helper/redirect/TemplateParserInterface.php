<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Redirect_TemplateParserInterface
{
	const FUNCTION_NAME_HEX        = 'hex';
	const FUNCTION_NAME_URLENCODED = 'urlencoded';
	const FUNCTION_NAME_BASE64     = 'base64';
	const FUNCTION_NAME_ESCAPED    = 'escaped';
	
	/**
	 * @param $template
	 *
	 * @return Shopgate_Model_Redirect_HtmlTagVariable[]
	 */
	public function getVariables($template);
	
	/**
	 * @param string                                  $template
	 * @param Shopgate_Model_Redirect_HtmlTagVariable $variable
	 * @param string                                  $replacement
	 *
	 * @return string
	 */
	public function process($template, $variable, $replacement);
	
	/**
	 * @param string $value
	 * @param string $functionName
	 *
	 * @return string
	 */
	public function filterVariableValue($value, $functionName = '');
}