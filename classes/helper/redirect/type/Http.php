<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class Shopgate_Helper_Redirect_Type_Http implements Shopgate_Helper_Redirect_Type_TypeInterface
{

	/** @var Shopgate_Helper_Redirect_RedirectorInterface */
	private $redirector;

	/**
	 * @param Shopgate_Helper_Redirect_RedirectorInterface $redirector
	 */
	public function __construct(Shopgate_Helper_Redirect_RedirectorInterface $redirector)
	{
		$this->redirector = $redirector;
	}

	/**
	 * @return Shopgate_Helper_Redirect_RedirectorInterface
	 */
	public function getBuilder()
	{
		return $this->redirector;
	}

	/**
	 * @param string $manufacturer
	 *
	 * @return void
	 */
	public function loadBrand($manufacturer)
	{
		$this->redirector->redirectBrand($manufacturer);
	}

	/**
	 * @param int|string $categoryId
	 *
	 * @return void
	 */
	public function loadCategory($categoryId)
	{
		$this->redirector->redirectCategory($categoryId);
	}

	/**
	 * @param string $cmsPage
	 *
	 * @return void
	 */
	public function loadCms($cmsPage)
	{
		$this->redirector->redirectCms($cmsPage);
	}

	/**
	 * @return void
	 */
	public function loadDefault()
	{
		$this->redirector->redirectDefault();
	}

	/**
	 * @return void
	 */
	public function loadHome()
	{
		$this->redirector->redirectHome();
	}

	/**
	 * @param int|string $productId
	 *
	 * @return void
	 */
	public function loadProduct($productId)
	{
		$this->redirector->redirectProduct($productId);
	}

	/**
	 * @param string $query
	 *
	 * @return void
	 */
	public function loadSearch($query)
	{
		$this->redirector->redirectSearch($query);
	}
}
