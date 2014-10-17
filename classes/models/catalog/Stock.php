<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/


class Shopgate_Model_Catalog_Stock extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'IsSaleable',
		'Backorders',
		'UseStock',
		'StockQuantity',
		'MinimumOrderQuantity',
		'MaximumOrderQuantity',
		'AvailabilityText');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $stockNode
		 */
		$stockNode = $itemNode->addChild('stock');
		$stockNode->addChild('is_saleable', $this->getIsSaleable());
		$stockNode->addChild('backorders', $this->getBackorders());
		$stockNode->addChild('use_stock', $this->getUseStock());
		$stockNode->addChild('stock_quantity', $this->getStockQuantity());
		$stockNode->addChild('minimum_order_quantity', $this->getMinimumOrderQuantity());
		$stockNode->addChild('maximum_order_quantity', $this->getMaximumOrderQuantity());
		$stockNode->addChildWithCDATA('availability_text', $this->getAvailabilityText());

		return $itemNode;
	}

	/**
	 * @return array|null
	 */
	public function asArray() {
		$stockResult = new Shopgate_Model_Abstract();

		$stockResult->setData('is_saleable', $this->getIsSaleable());
		$stockResult->setData('backorders', $this->getBackorders());
		$stockResult->setData('use_stock', $this->getUseStock());
		$stockResult->setData('stock_quantity', $this->getStockQuantity());
		$stockResult->setData('minimum_order_quantity', $this->getMinimumOrderQuantity());
		$stockResult->setData('maximum_order_quantity', $this->getMaximumOrderQuantity());
		$stockResult->setData('availability_text', $this->getAvailabilityText());

		return $stockResult->getData();
	}
}