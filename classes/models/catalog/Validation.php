<?php

/**
 * Shopgate GmbH
 *
 * URHEBERRECHTSHINWEIS
 *
 * Dieses Plugin ist urheberrechtlich geschützt. Es darf ausschließlich von Kunden der Shopgate GmbH
 * zum Zwecke der eigenen Kommunikation zwischen dem IT-System des Kunden mit dem IT-System der
 * Shopgate GmbH über www.shopgate.com verwendet werden. Eine darüber hinausgehende Vervielfältigung, Verbreitung,
 * öffentliche Zugänglichmachung, Bearbeitung oder Weitergabe an Dritte ist nur mit unserer vorherigen
 * schriftlichen Zustimmung zulässig. Die Regelungen der §§ 69 d Abs. 2, 3 und 69 e UrhG bleiben hiervon unberührt.
 *
 * COPYRIGHT NOTICE
 *
 * This plugin is the subject of copyright protection. It is only for the use of Shopgate GmbH customers,
 * for the purpose of facilitating communication between the IT system of the customer and the IT system
 * of Shopgate GmbH via www.shopgate.com. Any reproduction, dissemination, public propagation, processing or
 * transfer to third parties is only permitted where we previously consented thereto in writing. The provisions
 * of paragraph 69 d, sub-paragraphs 2, 3 and paragraph 69, sub-paragraph e of the German Copyright Act shall remain unaffected.
 *
 * @author Shopgate GmbH <interfaces@shopgate.com>
 */

/**
 * @class Shopgate_Model_Catalog_Validation
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 * @method        setType(string $value)
 * @method string getType()
 *
 * @method        setValue(string $value)
 * @method string getValue()
 *     
 * @method Shopgate_Model_Catalog_Validation_Rule getRule()
 * @method                                        setRule(Shopgate_Model_Catalog_Validation_Rule $value)
 *
 * @method Shopgate_Model_Catalog_Validation_Error getError()
 * @method                                         setError(Shopgate_Model_Catalog_Validation_Error $value)
 *
 */
class Shopgate_Model_Catalog_Validation extends Shopgate_Model_AbstractExport {
	/**
	 * types
	 */
	const TYPE_MIN_LENGTH = 'min-length';
	const TYPE_MAX_LENGTH = 'max-length';
	const TYPE_DATE = 'date';
	const TYPE_TIME = 'time';
	const TYPE_DATETIME = 'datetime';
	const TYPE_ALPHA = 'alpha';
	const TYPE_NUMERIC = 'numeric';
	const TYPE_ALPHA_NUMERIC = 'alpha-numeric';
	const TYPE_PHONE = 'phone';
	const TYPE_EMAIL = 'email';
	const TYPE_CONTAINS = 'contains';
	const TYPE_STARTS_WITH = 'starts-with';
	const TYPE_ENDS_WITH = 'ends-with';
	const TYPE_REGEX = 'regex';
	
	const DATE_AMERICAN = 'M/D/YY';
	const DATE_GERMAN = 'DD.MM.YYYY';
	const DATE_ISO = 'YYYY-MM-DD';
	
	const ALPHA_INTERNATIONAL = 'international';
	const ALPHA_EN_US = 'en-US';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Type',
		'Value',
		'Rule',
		'Error');

	/**
	 * @param Shopgate_Model_XmlResultObject $itemNode
	 *
	 * @return Shopgate_Model_XmlResultObject
	 */
	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $validationNode
		 */
		$validationNode = $itemNode->addChildWithCDATA('validation', $this->getValue());
		$validationNode->addAttribute('type', $this->getType());
		
		$this->getRule()->asXml($validationNode);
		$this->getError()->asXml($validationNode);

		return $itemNode;
	}

}