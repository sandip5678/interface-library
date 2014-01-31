<?php
/*
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
*  @author Shopgate GmbH <interfaces@shopgate.com>
*/

/**
 * Class ShopgateCategory
 *
 * @method string getCategoryNumber()
 * @method ShopgateCategory setCategoryNumber(string $value)
 * @method string getParentCategoryNumber()
 * @method ShopgateCategory setParentCategoryNumber(string $value)
 * @method string getName()
 * @method ShopgateCategory setName(string $value)
 * @method string getUrlImage()
 * @method ShopgateCategory setUrlImage(string $value)
 * @method bool getIsActive()
 * @method ShopgateCategory setIsActive(bool $value)
 * @method int getOrderIndex()
 * @method ShopgateCategory setOrderIndex(int $value) Use this like "priority". Highest value gets displayed closest to the top.
 */
class ShopgateCategory extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitCategory($this);
	}
}

/**
 * Class ShopgateItem
 *
 * @method string getItemNumber()
 * @method ShopgateItem setItemNumber(string $value)
 * @method string getName()
 * @method ShopgateItem setName(string $value)
 * @method string getCurrency()
 * @method ShopgateItem setCurrency(string $value)
 * @method ShopgateItem setTaxPercent(float $value)
 * @method string getTaxClassKey()
 * @method ShopgateItem setTaxClassKey(string $value)
 * @method string getTaxClassId()
 * @method ShopgateItem setTaxClassId(string $value)
 * @method float getOldUnitAmountWithTax()
 * @method ShopgateItem setOldUnitAmountWithTax(float $value)
 * @method ShopgateItem setCategoryNumbers(array $value)
 * @method string getItemNumberPublic()
 * @method ShopgateItem setItemNumberPublic(string $value)
 * @method string getParentItemNumber()
 * @method ShopgateItem setParentItemNumber(string $value)
 * @method string getManufacturer()
 * @method ShopgateItem setManufacturer(string $value)
 * @method string getManufacturerNumber()
 * @method ShopgateItem setManufacturerNumber(string $value)
 * @method string getDescription()
 * @method ShopgateItem setDescription(string $value)
 * @method float getShippingCostsPerOrder()
 * @method ShopgateItem setShippingCostsPerOrder(float $value)
 * @method float getShippingCostsPerUnit()
 * @method ShopgateItem setShippingCostsPerUnit(float $value)
 * @method bool getIsFreeShipping()
 * @method ShopgateItem setIsFreeShipping(bool $value)
 * @method float getMsrp()
 * @method ShopgateItem setMsrp(float $value)
 * @method string getTags()
 * @method ShopgateItem setTags(string $value)
 * @method int getAgeRating()
 * @method ShopgateItem setAgeRating(int $value)
 * @method int getWeight()
 * @method ShopgateItem setWeight(int $value)
 * @method string getEan()
 * @method ShopgateItem setEan(string $value)
 * @method string getIsbn()
 * @method ShopgateItem setIsbn(string $value)
 * @method string getPzn()
 * @method ShopgateItem setPzn(string $value)
 * @method string getAmountInfoText()
 * @method ShopgateItem setAmountInfoText(string $value)
 * @method string getInternalOrderInfo()
 * @method ShopgateItem setInternalOrderInfo(string $value)
 * @method bool getUseStock()
 * @method ShopgateItem setUseStock(bool $value)
 * @method int getStockQuantity()
 * @method ShopgateItem setStockQuantity(int $value)
 * @method bool getIsHighlight()
 * @method ShopgateItem setIsHighlight(bool $value)
 * @method int getHighlightOrderIndex()
 * @method ShopgateItem setHighlightOrderIndex(int $value)
 * @method bool getIsAvailable()
 * @method ShopgateItem setIsAvailable(bool $value)
 * @method string getAvailableText()
 * @method ShopgateItem setAvailableText(string $value)
 * @method bool getHasImage()
 * @method ShopgateItem setHasImage(bool $value)
 * @method int getImageCount()
 * @method ShopgateItem setImageCount(int $value)
 * @method bool getIsNotOrderable()
 * @method ShopgateItem setIsNotOrderable(bool $value)
 * @method bool getIsMarketplace()
 * @method ShopgateItem setIsMarketplace(bool $value)
 * @method bool getIsActive()
 * @method ShopgateItem setIsActive(bool $value)
 * @method bool getIsAutoUpdate()
 * @method ShopgateItem setIsAutoUpdate(bool $value)
 * @method string getAttribute1()
 * @method ShopgateItem setAttribute1(string $value)
 * @method string getAttribute2()
 * @method ShopgateItem setAttribute2(string $value)
 * @method string getAttribute3()
 * @method ShopgateItem setAttribute3(string $value)
 * @method string getAttribute4()
 * @method ShopgateItem setAttribute4(string $value)
 * @method string getAttribute5()
 * @method ShopgateItem setAttribute5(string $value)
 * @method string getAttribute6()
 * @method ShopgateItem setAttribute6(string $value)
 * @method string getAttribute7()
 * @method ShopgateItem setAttribute67(string $value)
 * @method string getAttribute8()
 * @method ShopgateItem setAttribute8(string $value)
 * @method string getAttribute9()
 * @method ShopgateItem setAttribute9(string $value)
 * @method string getAttribute10()
 * @method ShopgateItem setAttribute10(string $value)
 * @method ShopgateItem setProperties(array $value) array <string, string> $value Array with key-value-pairs.
 * @method string getDeeplinkOnlineshop()
 * @method ShopgateItem setDeeplinkOnlineshop(string $value)
 * @method ShopgateItem setRelatedItemNumbers(array $value)
 */
class ShopgateItem extends ShopgateContainer
{
	/**
	 * @deprecated
	 *
	 * @param float $value
	 */
	public function setUnitAmountWithTax($value)
	{
		$this->setData('unit_amount_with_tax', $value);
	}

	/**
	 * @deprecated
	 *
	 * @return float
	 */
	public function getUnitAmountWithTax()
	{
		return $this->getData('unit_amount_with_tax');
	}

	/**
	 * @param ShopgateItemOption[] $value
	 */
	public function setOptions($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('options', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateItemOption)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateItemOption($element);
			}
		}
		$this->setData('options', $value);
	}

	/**
	 * @param ShopgateItemInput[] $value
	 */
	public function setInputs($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('inputs', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateItemInput)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateItemInput($element);
			}
		}
		$this->setData('inputs', $value);
	}

	/**
	 * @deprecated
	 * @return float
	 */
	public function getTaxPercent()
	{
		return $this->getData('tax_percent');
	}

	/**
	 * @return string[]
	 */
	public function getCategoryNumbers()
	{
		$categoryNumbers = $this->getData('category_numbers');
		return !empty($categoryNumbers)
			? $categoryNumbers
			: array();
	}

	/**
	 * @return string[]
	 */
	public function getProperties()
	{
		$properties = $this->getData('properties');
		return (!empty($properties))
			? $properties
			: array();
	}

	/**
	 * @return string[]
	 */
	public function getRelatedItemNumbers()
	{
		$relatedItemNumbers = $this->getData('related_item_numbers');
		return (!empty($relatedItemNumbers))
			? $relatedItemNumbers
			: array();
	}

	/**
	 * @return ShopgateItemOption[]
	 */
	public function getOptions()
	{
		$options = $this->getData('options');
		return (!empty($options))
			? $options
			: array();
	}

	/**
	 * @return ShopgateItemInput[]
	 */
	public function getInputs()
	{
		$inputs = $this->getData('inputs');
		return (!empty($inputs))
			? $inputs
			: array();
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitItem($this);
	}
}

/**
 * Class ShopgateItemOption
 *
 * @method string getOptionNumber()
 * @method ShopgateItemOption setOptionNumber(string $value)
 * @method string getName()
 * @method ShopgateItemOption setName(string $value)
 * @method int getOrderIndex()
 * @method ShopgateItemOption setOrderIndex(int $value)
 */
class ShopgateItemOption extends ShopgateContainer
{
	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setData('option_values', array());
		parent::__construct($data);
	}

	/**
	 * @param ShopgateItemOptionValue[] $value
	 */
	public function setOptionValues($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('option_values', null);
			return;
		}


		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateItemOptionValue)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateItemOptionValue($element);
			}
		}
		$this->setData('option_values', $value);
	}

	/**
	 * @return ShopgateItemOptionValue[]
	 */
	public function getOptionValues()
	{
		$optionValues = $this->getData('option_values');
		return (!empty($optionValues))
			? $optionValues
			: array();
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitItemOption($this);
	}
}

/**
 * Class ShopgateItemOptionValue
 *
 * @method string getValueNumber()
 * @method ShopgateItemOptionValue setValueNumber(string $value)
 * @method string getValue()
 * @method ShopgateItemOptionValue setValue(string $value)
 * @method int getOrderIndex()
 * @method ShopgateItemOptionValue setOrderIndex(int $value)
 * @method float getAdditionalAmountWithTax()
 * @method ShopgateItemOptionValue setAdditionalAmountWithTax(float $value)
 */
class ShopgateItemOptionValue extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitItemOptionValue($this);
	}
}

/**
 * Class ShopgateItemInput
 *
 * @method string getInputNumber()
 * @method ShopgateItemInput setInputNumber(string $value)
 * @method string getType()
 * @method ShopgateItemInput setType(string $value) Must be "text" or "image"
 * @method float getAdditionalAmountWithTax()
 * @method ShopgateItemInput setAdditionalAmountWithTax(float $value)
 * @method string getLabel()
 * @method ShopgateItemInput setLabel(string $value)
 * @method string getInfoText()
 * @method ShopgateItemInput setInfoText(string $value)
 * @method bool getIsRequired()
 * @method ShopgateItemInput setIsRequired(bool $value)
 */
class ShopgateItemInput extends ShopgateContainer
{
	const INPUT_TYPE_TEXT  = "text";
	const INPUT_TYPE_IMAGE = "image";

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitItemInput($this);
	}
}