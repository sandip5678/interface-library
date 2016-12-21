<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Redirect_Type_Js implements Shopgate_Helper_Redirect_Type_TypeInterface
{
	/** @var Shopgate_Helper_Redirect_JsScriptBuilderInterface */
	private $jsBuilder;

	/**
	 * @param Shopgate_Helper_Redirect_JsScriptBuilderInterface $jsBuilder
	 */
	public function __construct(Shopgate_Helper_Redirect_JsScriptBuilderInterface $jsBuilder)
	{
		$this->jsBuilder = $jsBuilder;
	}

	/**
	 * @return Shopgate_Helper_Redirect_JsScriptBuilderInterface
	 */
	public function getBuilder()
	{
		return $this->jsBuilder;
	}

	/**
	 * @param string $manufacturer
	 *
	 * @return string
	 */
	public function loadBrand($manufacturer)
	{
		return $this->jsBuilder->buildTags(
			Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_BRAND,
			array('brand_name' => $manufacturer)
		);
	}

	/**
	 * @param int|string $categoryId
	 *
	 * @return string
	 */
	public function loadCategory($categoryId)
	{
		return $this->jsBuilder->buildTags(
			Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_CATEGORY,
			array('category_uid' => $categoryId)
		);
	}

	/**
	 * @param string $cmsPage
	 *
	 * @return string
	 */
	public function loadCms($cmsPage)
	{
		return $this->jsBuilder->buildTags(
			Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_CMS,
			array('page_uid' => $cmsPage)
		);
	}

	/**
	 * @return string
	 */
	public function loadDefault()
	{
		return $this->jsBuilder->buildTags(Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_DEFAULT);
	}

	/**
	 * @return string
	 */
	public function loadHome()
	{
		return $this->jsBuilder->buildTags(Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_HOME);
	}

	/**
	 * @param int|string $productId
	 *
	 * @return string
	 */
	public function loadProduct($productId)
	{
		return $this->jsBuilder->buildTags(
			Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_PRODUCT,
			array('product_uid' => $productId)
		);
	}

	/**
	 * @param string $query
	 *
	 * @return string
	 */
	public function loadSearch($query)
	{
		return $this->jsBuilder->buildTags(
			Shopgate_Helper_Redirect_TagsGeneratorInterface::PAGE_TYPE_SEARCH,
			array('search_query' => $query)
		);
	}
}
