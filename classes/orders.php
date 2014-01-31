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
 * Class ShopgateOrder
 *
 * @method string getOrderNumber()
 * @method ShopgateOrder setOrderNumber(string $value)
 * @method string getConfirmShippingUrl()
 * @method ShopgateOrder setConfirmShippingUrl(string $value)
 * @method ShopgateOrder setCreatedTime(string $value) http://www.php.net/manual/de/function.date.php
 * @method bool getIsPaid()
 * @method ShopgateOrder setIsPaid(bool $value)
 * @method ShopgateOrder setPaymentTime(string $value) http://www.php.net/manual/de/function.date.php
 * @method string getPaymentTransactionNumber()
 * @method ShopgateOrder setPaymentTransactionNumber(string $value)
 * @method string getPaymentInfos()
 * @method ShopgateOrder setPaymentInfos(string $value) http://wiki.shopgate.com/Merchant_API_payment_infos/
 * @method bool getIsShippingBlocked()
 * @method ShopgateOrder setIsShippingBlocked(bool $value)
 * @method bool getIsShippingCompleted()
 * @method ShopgateOrder setIsShippingCompleted(bool $value)
 * @method ShopgateOrder setShippingCompletedTime(string $value) http://www.php.net/manual/de/function.date.php
 * @method bool getIsTest()
 * @method ShopgateOrder setIsTest(bool $value)
 * @method bool getIsStorno()
 * @method ShopgateOrder setIsStorno(bool $value)
 * @method bool getIsCustomerInvoiceBlocked()
 * @method ShopgateOrder setIsCustomerInvoiceBlocked(bool $value)
 * @method bool getUpdatePayment()
 * @method ShopgateOrder setUpdatePayment(bool $value)
 * @method bool getUpdateShipping()
 * @method ShopgateOrder setUpdateShipping(bool $value)
 */
class ShopgateOrder extends ShopgateCartBase
{
	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setData(array('update_shipping' => false, 'update_payment' => false, 'delivery_notes' => array()));
		parent::__construct($data);
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrder($this);
	}

	/**
	 * @param ShopgateDeliveryNote []|mixed[][] $value
	 */
	public function setDeliveryNotes($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('delivery_notes', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateDeliveryNote)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateDeliveryNote($element);
			}
		}
		$this->setData('delivery_notes', $value);
	}


	/**
	 * @see http://www.php.net/manual/de/function.date.php
	 * @see http://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getCreatedTime($format = "")
	{
		$time = $this->getData('created_time');
		if (!empty($format)) {
			$timestamp = strtotime($time);
			$time      = date($format, $timestamp);
		}

		return $time;
	}

	/**
	 * @see http://www.php.net/manual/de/function.date.php
	 * @see http://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getPaymentTime($format = "")
	{
		$time = $this->getData('payment_time');
		if (!empty($format)) {
			$timestamp = strtotime($time);
			$time      = date($format, $timestamp);
		}

		return $time;
	}

	/**
	 * @see http://www.php.net/manual/de/function.date.php
	 * @see http://en.wikipedia.org/wiki/ISO_8601
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function getShippingCompletedTime($format = '')
	{
		$time = $this->getData('shipping_completed_time');
		if (!empty($format)) {
			$timestamp = strtotime($time);
			$time      = date($format, $timestamp);
		}

		return $time;
	}
}

/**
 * Class ShopgateOrderItem
 *
 * @method string getName()
 * @method ShopgateOrderItem setName(string $value)
 * @method string getItemNumber()
 * @method ShopgateOrderItem setItemNumber(string $value)
 * @method string getItemNumberPublic()
 * @method ShopgateOrderItem setItemNumberPublic(string $value)
 * @method float getUnitAmount()
 * @method ShopgateOrderItem setUnitAmount(float $value)
 * @method float getUnitAmountWithTax()
 * @method ShopgateOrderItem setUnitAmountWithTax(float $value)
 * @method int getQuantity()
 * @method ShopgateOrderItem setQuantity(int $value)
 * @method float getTaxPercent()
 * @method ShopgateOrderItem setTaxPercent(float $value)
 * @method string getTaxClassKey()
 * @method ShopgateOrderItem setTaxClassKey(string $value)
 * @method string getTaxClassId()
 * @method ShopgateOrderItem setTaxClassId(string $value)
 * @method string getCurrency()
 * @method ShopgateOrderItem setCurrency(string $value)
 * @method string getInternalOrderInfo()
 * @method ShopgateOrderItem setInternalOrderInfo(string $value)
 * @method array getOptions()
 * @method array getInputs()
 * @method array getAttributes()
 */
class ShopgateOrderItem extends ShopgateContainer
{
	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setData(array('options' => array(), 'inputs' => array(), 'attributes' => array()));
		parent::__construct($data);
	}

	/**
	 * @param ShopgateOrderItemOption []|mixed[][] $value
	 */
	public function setOptions($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('options', array());
			return;
		}

		// convert sub-arrays into ShopgateOrderItemOption objects if necessary
		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderItemOption)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderItemOption($element);
			}
		}

		$this->setData('options', $value);
	}

	/**
	 * @param ShopgateOrderItemInput []|mixed[][] $value
	 */
	public function setInputs($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('inputs', array());
			return;
		}

		// convert sub-arrays into ShopgateOrderItemInput objects if necessary
		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderItemInput)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderItemInput($element);
			}
		}

		$this->setData('inputs', $value);
	}

	/**
	 * @param ShopgateOrderItemAttribute []|mixed[][] $value
	 */
	public function setAttributes($value)
	{
		if (empty($value) || !is_array($value)) {
			$this->setData('attributes', array());
			return;
		}

		// convert sub-arrays into ShopgateOrderItemAttribute objects if necessary
		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderItemAttribute)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderItemAttribute($element);
			}
		}

		$this->setData('attributes', $value);
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderItem($this);
	}
}

/**
 * Class ShopgateOrderItemOption
 *
 * @method string getName()
 * @method ShopgateOrderItemOption setName(string $value)
 * @method string getValue()
 * @method ShopgateOrderItemOption setValue(string $value)
 * @method string getAdditionalAmountWithTax()
 * @method ShopgateOrderItemOption setAdditionalAmountWithTax(string $value)
 * @method string getValueNumber()
 * @method ShopgateOrderItemOption setValueNumber(string $value)
 * @method string getOptionNumber()
 * @method ShopgateOrderItemOption setOptionNumber(string $value)
 */
class ShopgateOrderItemOption extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderItemOption($this);
	}
}

/**
 * Class ShopgateOrderItemInput
 *
 * @method string getInputNumber()
 * @method ShopgateOrderItemInput setInputNumber(string $value)
 * @method string getType()
 * @method ShopgateOrderItemInput setType(string $value)
 * @method float getAdditionalAmountWithTax()
 * @method ShopgateOrderItemInput setAdditionalAmountWithTax(float $value)
 * @method string getLabel()
 * @method ShopgateOrderItemInput setLabel(string $value)
 * @method string getUserInput()
 * @method ShopgateOrderItemInput setUserInput(string $value)
 * @method string getInfoText()
 * @method ShopgateOrderItemInput setInfoText(string $value)
 */
class ShopgateOrderItemInput extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderItemInput($this);
	}
}

/**
 * Class ShopgateOrderItemAttribute
 *
 * @method string getName()
 * @method ShopgateOrderItemAttribute setName(string $value)
 * @method string getValue()
 * @method ShopgateOrderItemAttribute setValue(string $value)
 */
class ShopgateOrderItemAttribute extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderItemAttribute($this);
	}
}

/**
 * Class ShopgateShippingInfo
 *
 * @method string getName()
 * @method ShopgateShippingInfo setName(string $value)
 * @method string getDescription()
 * @method ShopgateShippingInfo setDescription(string $value)
 * @method float getAmount()
 * @method ShopgateShippingInfo setAmount(float $value)
 * @method int getWeight()
 * @method ShopgateShippingInfo setWeight(int $value)
 * @method string getApiResponse()
 */
class ShopgateShippingInfo extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitShippingInfo($this);
	}

	/**
	 * @param string|mixed[] $value
	 */
	public function setApiResponse($value)
	{
		if (is_string($value)) {
			$value = $this->jsonDecode($value, true);
		}

		$this->setData('api_response', $value);
	}
}

/**
 * Class ShopgateDeliveryNote
 *
 * @method string getShippingServiceId()
 * @method ShopgateCoupon setShippingServiceId(string $value)
 * @method string getTrackingNumber()
 * @method ShopgateCoupon setTrackingNumber(string $value)
 * @method string getShippingTime()
 * @method ShopgateCoupon setShippingTime(string $value)
 *
 */
class ShopgateDeliveryNote extends ShopgateContainer
{
	// shipping groups
	const DHL = "DHL"; // DHL
	const DHLEXPRESS = "DHLEXPRESS"; // DHLEXPRESS
	const DP = "DP"; // Deutsche Post
	const DPD = "DPD"; // Deutscher Paket Dienst
	const FEDEX = "FEDEX"; // FedEx
	const GLS = "GLS"; // GLS
	const HLG = "HLG"; // Hermes
	const OTHER = "OTHER"; // Anderer Lieferant
	const TNT = "TNT"; // TNT
	const TOF = "TOF"; // Trnas-o-Flex
	const UPS = "UPS"; // UPS
	const USPS = "USPS"; // USPS

	// shipping types
	const MANUAL      = "MANUAL";
	const USPS_API_V1 = "USPS_API_V1";
	const UPS_API_V1  = "UPS_API_V1";

	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setShippingServiceId(ShopgateDeliveryNote::DHL);
		$this->setTrackingNumber('');
		parent::__construct($data);
	}

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderDeliveryNote($this);
	}
}

/**
 * Class ShopgateCoupon
 *
 * @method int getOrderIndex()
 * @method ShopgateCoupon setOrderIndex(int $value)
 * @method string getCode()
 * @method ShopgateCoupon setCode(string $value)
 * @method string getName()
 * @method ShopgateCoupon setName(string $value)
 * @method string getDescription()
 * @method ShopgateCoupon setDescription(string $value)
 * @method float getAmount()
 * @method ShopgateCoupon setAmount(float $value)
 * @method float getAmountNet()
 * @method ShopgateCoupon setAmountNet(float $value)
 * @method float getAmountGross()
 * @method ShopgateCoupon setAmountGross(float $value)
 * @method string getTaxType()
 * @method ShopgateCoupon setTaxType(string $value)
 * @method string getCurrency()
 * @method ShopgateCoupon setCurrency(string $value)
 * @method bool getIsFreeShipping()
 * @method ShopgateCoupon setIsFreeShipping(bool $value)
 * @method string getInternalInfo()
 * @method ShopgateCoupon setInternalInfo(string $value)
 */
abstract class ShopgateCoupon extends ShopgateContainer
{
	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setTaxType('auto');
		parent::__construct($data);
	}
}

/**
 * Class ShopgateExternalCoupon
 *
 * @method bool getIsValid()
 * @method ShopgateExternalCoupon setIsValid(bool $value)
 * @method string getNotValidMessage()
 * @method ShopgateExternalCoupon setNotValidMessage(string $value)
 */
class ShopgateExternalCoupon extends ShopgateCoupon
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitExternalCoupon($this);
	}
}

/**
 * Class ShopgateShopgateCoupon
 */
class ShopgateShopgateCoupon extends ShopgateCoupon
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitShopgateCoupon($this);
	}
}

/**
 * Class ShopgateOrderCustomField
 *
 * @method string getLabel()
 * @method ShopgateOrderCustomField setLabel(string $value)
 * @method string getInternalFieldName()
 * @method ShopgateOrderCustomField setInternalFieldName(string $value)
 * @method mixed getValue()
 * @method ShopgateOrderCustomField setValue(mixed $value)
 */
class ShopgateOrderCustomField extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitOrderCustomField($this);
	}
}
