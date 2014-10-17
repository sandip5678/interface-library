<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/


class Shopgate_Model_Catalog_Price extends Shopgate_Model_AbstractExport {
	/**
	 * default price types
	 *
	 * gross
	 */
	const DEFAULT_PRICE_TYPE_GROSS = 'gross';

	/**
	 * net
	 */
	const DEFAULT_PRICE_TYPE_NET = 'net';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Type',
		'Price',
		'Cost',
		'SalePrice',
		'Msrp',
		'TierPricesGroup',
		'MinimumOrderAmount');

	/**
	 * init default object
	 */
	public function __construct() {
		$this->setTierPricesGroup(array());
	}

	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $tierPricesNode
		 * @var Shopgate_Model_Customer_Group  $customerGroupItem
		 */
		$pricesNode = $itemNode->addChild('prices');
		$pricesNode->addAttribute('type', $this->getType());
		$pricesNode->addChild('price', $this->getPrice());
		$pricesNode->addChild('cost', $this->getCost());
		$pricesNode->addChild('sale_price', $this->getSalePrice());
		$pricesNode->addChild('msrp', $this->getMsrp());
		$pricesNode->addChild('minimum_order_amount', $this->getMinimumOrderAmount());

		$tierPricesNode = $pricesNode->addChild('tier_prices');
		foreach ($this->getTierPricesGroup() as $customerGroupItem) {
			$customerGroupItem->asXml($tierPricesNode);
		}

		return $itemNode;
	}

	/**
	 * add tier price
	 *
	 * @param Shopgate_Model_Catalog_TierPrice $tierPrice
	 */
	public function addTierPriceGroup(Shopgate_Model_Catalog_TierPrice $tierPrice) {
		$tierPrices = $this->getTierPricesGroup();
		array_push($tierPrices, $tierPrice);
		$this->setTierPricesGroup($tierPrices);
	}
}