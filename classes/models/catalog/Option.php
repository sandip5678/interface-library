<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2014 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
*/


class Shopgate_Model_Catalog_Option extends Shopgate_Model_AbstractExport {

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Uid',
		'Label',
		'Value',
		'AdditionalPrice');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $optionNode
		 */
		$optionNode = $itemNode->addChild('option');
		$optionNode->addAttribute('additional_price', $this->getAdditionalPrice());
		$optionNode->addAttribute('uid', $this->getUid());
		$optionNode->addChildWithCDATA('label', $this->getLabel());
		$optionNode->addChildWithCDATA('value', $this->getValue());

		return $itemNode;
	}
}