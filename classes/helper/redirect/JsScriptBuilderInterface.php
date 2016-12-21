<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Redirect_JsScriptBuilderInterface
{
	/**
	 * @param string $pageType
	 * @param array  $parameters [string, string]
	 *
	 * @return string
	 */
	public function buildTags($pageType, $parameters = array());

	/**
	 * Sets the file path of javascript template
	 * to use
	 *
	 * @param string $filePath
	 *
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function setJsTemplateFilePath($filePath);

	/**
	 * Helps set all parameters at once
	 *
	 * @param array $params - array(key => value)
	 *
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function setSiteParameters($params);

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function setSiteParameter($key, $value);

	/**
	 * @param string $file
	 *
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function setTemplateFile($file);

	/**
	 * Prints a value to JS script to prevent
	 * web app redirect
	 *
	 * @param bool $param
	 *
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function suppressWebAppRedirect($param);
}
