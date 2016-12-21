<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

interface Shopgate_Helper_Redirect_Type_TypeInterface
{

	const HTTP = 'http';
	const JS   = 'js';

	/**
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface | Shopgate_Helper_Redirect_RedirectorInterface
	 */
	public function getBuilder();

	/**
	 * @param string $manufacturer
	 *
	 * @return string | void
	 */
	public function loadBrand($manufacturer);

	/**
	 * @param string | int $categoryId
	 *
	 * @return string | void
	 */
	public function loadCategory($categoryId);

	/**
	 * @param string $cmsPage
	 *
	 * @return string | void
	 */
	public function loadCms($cmsPage);

	/**
	 * @return string | void
	 */
	public function loadDefault();

	/**
	 * @return string | void
	 */
	public function loadHome();

	/**
	 * @param string | int $productId
	 *
	 * @return string | void
	 */
	public function loadProduct($productId);

	/**
	 * @param string $query
	 *
	 * @return string | void
	 */
	public function loadSearch($query);
}
