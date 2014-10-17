<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/



class Shopgate_Model_Catalog_TierPrice extends Shopgate_Model_AbstractExport {

	const DEFAULT_TIER_PRICE_TYPE_PERCENT = 'percent';
	const DEFAULT_TIER_PRICE_TYPE_FIXED = 'fixed';
	const DEFAULT_TIER_PRICE_TYPE_DIFFERENCE = 'difference';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'FromQuantity',
		'ReductionType',
		'Reduction',
		'CustomerGroupUid',
		'ToQuantity',
		'AggregateChildren');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $tierPriceNode
		 */
		$tierPriceNode = $itemNode->addChild('tier_price', $this->getReduction());
		$tierPriceNode->addAttribute('aggregate_children', $this->getAggregateChildren());
		$tierPriceNode->addAttribute('threshold', $this->getFromQuantity());
		$tierPriceNode->addAttribute('max_quantity', $this->getToQuantity());
		$tierPriceNode->addAttribute('type', $this->getReductionType());
		$tierPriceNode->addAttribute('customer_group_uid', $this->getCustomerGroupUid());

		return $itemNode;
	}
}