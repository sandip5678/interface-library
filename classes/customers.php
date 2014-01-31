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
 * Class ShopgateCustomer
 *
 * @method string getCustomerId()
 * @method ShopgateCustomer setCustomerId(string $value)
 * @method string getCustomerNumber()
 * @method ShopgateCustomer setCustomerNumber(string $value)
 * @method string getCustomerGroup()
 * @method ShopgateCustomer setCustomerGroup(string $value)
 * @method string getCustomerGroupId()
 * @method ShopgateCustomer setCustomerGroupId(string $value)
 * @method string getTaxClassKey()
 * @method ShopgateCustomer setTaxClassKey(string $value)
 * @method string getTaxClassId()
 * @method ShopgateCustomer setTaxClassId(string $value)
 * @method string getFirstName()
 * @method ShopgateCustomer setFirstName(string $value)
 * @method string getLastName()
 * @method ShopgateCustomer setLastName(string $value)
 * @method string getPhone()
 * @method ShopgateCustomer setPhone(string $value)
 * @method string getMobile()
 * @method ShopgateCustomer setMobile(string $value)
 * @method string getMail()
 * @method ShopgateCustomer setMail(string $value)
 * @method ShopgateCustomer setNewsletterSubscription(bool $value)
 * @method string getGender()
 * @method string getBirthday()
 * @method array getCustomFields()
 */
class ShopgateCustomer extends ShopgateContainer
{
	const MALE   = "m";
	const FEMALE = "f";

	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitCustomer($this);
	}

	/**
	 * @param string $value <ul><li>"m" = Male</li><li>"f" = Female</li></ul>
	 */
	public function setGender($value)
	{
		if (empty($value)) {
			return;
		}

		if (($value != self::MALE) && ($value != self::FEMALE)) {
			$this->setData('gender', null);
		} else {
			$this->setData('gender', $value);
		}
	}

	/**
	 * @param string $value Format: yyyy-mm-dd (1983-02-17)
	 */
	public function setBirthday($value)
	{
		if (empty($value)) {
			$this->setData('birthday', null);
			return;
		}

		$matches = null;
		if (!preg_match('/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})/', $value, $matches)) {
			$this->setData('birthday', null);
		} else {
			$this->setData('birthday', $matches[1]);
		}
	}

	/**
	 * @param ShopgateOrderCustomField[] $value
	 */
	public function setCustomFields($value)
	{
		if (!is_array($value)) {
			$this->setData('custom_fields', null);
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderCustomField)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderCustomField($element);
			}
		}
		$this->setData('custom_fields', $value);
	}

	/**
	 * @param ShopgateAddress[] $value List of customer's addresses.
	 */
	public function setAddresses($value)
	{
		if (!is_array($value)) {
			$this->setData('addresses', null);
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateAddress)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateAddress($element);
			}
		}
		$this->setData('addresses', $value);
	}

	/**
	 * @return bool
	 */
	public function getNewsletterSubscription()
	{
		return (bool)$this->getData('newsletter_subscription');
	}

	/**
	 * @param int $type <ul><li>ShopgateAddress::BOTH</li><li>ShopgateAddress::INVOICE</li><li>ShopgateAddress::DELIVERY</li></ul>
	 *
	 * @return ShopgateAddress[] List of customer's addresses, filtered by $type.
	 */
	public function getAddresses($type = ShopgateAddress::BOTH)
	{
		$addresses = $this->getData('addresses');
		if (empty($addresses)) {
			return array();
		}

		$addresses = array();

		foreach ($this->getData('addresses') as $address) {
			/** @var $address ShopgateAddress */
			if (($address->getAddressType() & $type) == $address->getAddressType()) {
				$addresses[] = $address;
			}
		}

		return $addresses;
	}
}

/**
 * Class ShopgateAddress
 *
 * @method string getId()
 * @method ShopgateAddress setId(string $value)
 * @method string getFirstName()
 * @method ShopgateAddress setFirstName(string $value)
 * @method string getLastName()
 * @method ShopgateAddress setLastName(string $value)
 * @method string getCompany()
 * @method ShopgateAddress setCompany(string $value)
 * @method string getStreet1()
 * @method ShopgateAddress setStreet1(string $value)
 * @method string getStreet2()
 * @method ShopgateAddress setStreet2(string $value)
 * @method string getCity()
 * @method ShopgateAddress setCity(string $value)
 * @method string getZipcode()
 * @method ShopgateAddress setZipcode(string $value)
 * @method string getCountry()
 * @method ShopgateAddress setCountry(string $value) http: //en.wikipedia.org/wiki/ISO_3166-1#Current_codes
 * @method string getState()
 * @method ShopgateAddress setState(string $value) State as ISO-3166-2 http: //en.wikipedia.org/wiki/ISO_3166-2#Current_codes
 * @method string getPhone()
 * @method ShopgateAddress setPhone(string $value)
 * @method string getMobile()
 * @method ShopgateAddress setMobile(string $value)
 * @method string getMail()
 * @method ShopgateOrderCustomField[] getCustomFields()
 * @method string getBirthday() Format: yyyy-mm-dd (1983-02-17)
 * @method ShopgateAddress setMail(string $value)
 * @method string getGender() <ul><li>"m" = Male</li><li>"f" = Female</li></ul>
 */
class ShopgateAddress extends ShopgateContainer
{
	const MALE     = "m";
	const FEMALE   = "f";
	const INVOICE  = 0x01;
	const DELIVERY = 0x10;
	const BOTH     = 0x11;

	/**
	 * Checks if two ShopgateAddress objects are equal.
	 *
	 * Two addresses are equal when following fields contain the same value:
	 * 'gender','first_name','last_name','street_1','street_2','zipcode','city','country'
	 *
	 * @param ShopgateAddress $address
	 *
	 * @return bool
	 */
	public function equals(ShopgateAddress $address)
	{
		$whiteList = array('gender', 'first_name', 'last_name', 'street_1', 'street_2', 'zipcode', 'city', 'country');
		return $this->compare($this, $address, $whiteList);
	}

	/**
	 * @param int $value ShopgateAddress::BOTH or ShopgateAddress::INVOICE or ShopgateAddress::DELIVERY
	 */
	public function setAddressType($value)
	{
		$this->setData('is_invoice_address', (bool)($value & self::INVOICE));
		$this->setData('is_delivery_address', (bool)($value & self::DELIVERY));
	}

	/**
	 * @param bool $value
	 */
	public function setIsInvoiceAddress($value)
	{
		$this->setData('is_invoice_address', (bool)$value);
	}

	/**
	 * @param bool $value
	 */
	public function setIsDeliveryAddress($value)
	{
		$this->setData('is_delivery_address', (bool)$value);
	}

	/**
	 * @param string $value <ul><li>"m" = Male</li><li>"f" = Female</li></ul>
	 */
	public function setGender($value = null)
	{
		if (empty($value)) {
			return;
		}

		if (($value != self::MALE) && ($value != self::FEMALE)) {
			$this->setData('gender', null);
		} else {
			$this->setData('gender', $value);
		}
	}

	/**
	 * @param string $value Format: yyyy-mm-dd (1983-02-17)
	 */
	public function setBirthday($value)
	{
		if (empty($value)) {
			$this->setData('birthday', null);
			return;
		}

		$matches = null;
		if (!preg_match('/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})/', $value, $matches)) {
			$this->setData('birthday', null);
		} else {
			$this->setData('birthday', $matches[1]);
		}
	}

	/**
	 * @param ShopgateOrderCustomField[] $value
	 */
	public function setCustomFields($value)
	{
		if (!is_array($value)) {
			$this->setData('custom_fields', null);
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderCustomField)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderCustomField($element);
			}
		}
		$this->setData('custom_fields', $value);
	}

	/**
	 * @return bool
	 */
	public function getIsInvoiceAddress() { return (bool)$this->getData('is_invoice_address'); }

	/**
	 * @return bool
	 */
	public function getIsDeliveryAddress() { return (bool)$this->getData('is_delivery_address'); }

	/**
	 * @return int ShopgateAddress::BOTH or ShopgateAddress::INVOICE or ShopgateAddress::DELIVERY
	 */
	public function getAddressType()
	{
		return (int)(
			($this->getIsInvoiceAddress() ? self::INVOICE : 0) |
			($this->getIsDeliveryAddress() ? self::DELIVERY : 0)
		);
	}

	/**
	 * @return string
	 */
	public function getStreetName1()
	{
		return $this->splitStreetData($this->getStreet1(), "street");
	}

	/**
	 * @return string
	 */
	public function getStreetNumber1()
	{
		return $this->splitStreetData($this->getStreet1(), "number");
	}

	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitAddress($this);
	}

	/**
	 * @param string $street
	 * @param string $type [street|name]
	 *
	 * @return string
	 */
	protected function splitStreetData($street, $type = 'street')
	{
		$splittedArray = array();
		$street        = trim($street);
		$street        = str_replace("\n", '', $street);

		//contains only digits OR no digits at all --> don't split
		if (preg_match("/^[0-9]+$/i", $street) || preg_match("/^[^0-9]+$/i", $street)) {
			return ($type == 'street') ? $street : "";
		}

		//number at the end ("Schlossstr. 10", "Schlossstr. 10a", "Schlossstr. 10a+b"...)
		if (preg_match("/^([^0-9]+)([0-9]+ ?[a-z]?([ \-\&\+]+[a-z])?)$/i", $street, $matches)) {
			return trim(($type == 'street') ? $matches[1] : $matches[2]);
		}

		//number at the end ("Schlossstr. 10-12", "Schlossstr. 10 & 12"...)
		if (preg_match("/^([^0-9]+)([0-9]+([ \-\&\+]+[0-9]+)?)$/i", $street, $matches)) {
			return trim(($type == 'street') ? $matches[1] : $matches[2]);
		}

		//number at the beginning (e.g. "2225 E. Bayshore Road", "2225-2227 E. Bayshore Road")
		if (preg_match("/^([0-9]+([ \-\&\+]+[0-9]+)?)([^0-9]+.*)$/i", $street, $matches)) {
			return trim(($type == 'street') ? $matches[3] : $matches[1]);
		}

		if (!preg_match("/^(.+)\s(.*[0-9]+.*)$/is", $street, $splittedArray)) {
			// for "My-Little-Street123"
			preg_match("/^(.+)([0-9]+.*)$/isU", $street, $splittedArray);
		}

		$value = $street;
		switch ($type) {
			case 'street':
				if (isset($splittedArray[1])) {
					$value = $splittedArray[1];
				}
				break;
			case 'number':
				if (isset($splittedArray[2])) {
					$value = $splittedArray[2];
				} else {
					$value = "";
				}
				break;
		}

		return $value;
	}
}