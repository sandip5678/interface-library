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
 *
 * @Developer: srecknagel
 * @Date     : 29.01.14
 * @Time     : 10:30
 * @Email    : mail@recknagel.io
 *
 */

/**
 * Class ShopgateCartBase
 *
 * @method string getCustomerNumber()
 * @method ShopgateCartBase setCustomerNumber(string $value)
 * @method string getExternalOrderNumber()
 * @method ShopgateCartBase setExternalOrderNumber(string $value)
 * @method string getExternalOrderId()
 * @method ShopgateCartBase setExternalOrderId(string $value)
 * @method string getExternalCustomerNumber()
 * @method ShopgateCartBase setExternalCustomerNumber(string $value)
 * @method string getExternalCustomerId()
 * @method ShopgateCartBase setExternalCustomerId(string $value)
 * @method string getMail()
 * @method ShopgateCartBase setMail(string $value)
 * @method string getPhone()
 * @method ShopgateCartBase setPhone(string $value)
 * @method string getMobile()
 * @method ShopgateCartBase setMobile(string $value)
 * @method array getCustomFields()
 * @method string getShippingGroup()
 * @method ShopgateCartBase setShippingGroup(string $value)
 * @method string getShippingType()
 * @method ShopgateCartBase setShippingType(string $value)
 * @method string getShippingInfos()
 * @method string getPaymentGroup() http: //wiki.shopgate.com/Merchant_API_payment_infos/
 * @method ShopgateCartBase setPaymentGroup(string $value) http: //wiki.shopgate.com/Merchant_API_payment_infos/
 * @method string getPaymentMethod() http: //wiki.shopgate.com/Merchant_API_payment_infos/
 * @method ShopgateCartBase setPaymentMethod(string $value) http: //wiki.shopgate.com/Merchant_API_payment_infos/
 * @method float getAmountItems()
 * @method ShopgateCartBase setAmountItems(float $value)
 * @method float getAmountShipping()
 * @method ShopgateCartBase setAmountShipping(float $value)
 * @method float getAmountShopPayment()
 * @method ShopgateCartBase setAmountShopPayment(float $value)
 * @method float getPaymentTaxPercent()
 * @method ShopgateCartBase setPaymentTaxPercent(float $value)
 * @method float getAmountShopgatePayment()
 * @method ShopgateCartBase setAmountShopgatePayment(float $value)
 * @method float getAmountComplete()
 * @method ShopgateCartBase setAmountComplete(float $value)
 * @method string getCurrency()
 * @method ShopgateCartBase setCurrency(string $value) http: //de.wikipedia.org/wiki/ISO_4217
 * @method array getInvoiceAddress()
 * @method array getDeliveryAddress()
 * @method array getExternalCoupons()
 * @method array getShopgateCoupons()
 * @method array getItems()
 * @method array getShippingMethods()
 * @method array getCartItems()
 * @method array getPaymentMethods()
 */
abstract class ShopgateCartBase extends ShopgateContainer
{
	const SHOPGATE  = "SHOPGATE";
	const PREPAY    = "PREPAY";
	const SG_PREPAY = "SG_PREPAY";

	const DEBIT      = "DEBIT";
	const COD        = "COD";
	const COLL_STORE = "COLL_STORE";

	const INVOICE    = "INVOICE";
	const KLARNA_INV = "KLARNA_INV";
	const BILLSAFE   = "BILLSAFE";
	const MSTPAY_INV = "MSTPAY_INV";
	const SG_INVOICE = "SG_INVOICE";

	const PAYPAL     = "PAYPAL";
	const CMPTOP_PP  = "CMPTOP_PP";
	const MASTPAY_PP = "MASTPAY_PP";
	const SAGEPAY_PP = "SAGEPAY_PP";
	const SG_PAYPAL  = "SG_PAYPAL";

	const CC         = "CC";
	const AUTHN_CC   = "AUTHN_CC";
	const BCLEPDQ_CC = "BCLEPDQ_CC";
	const BNSTRM_CC  = "BNSTRM_CC";
	const BRAINTR_CC = "BRAINTR_CC";
	const CHASE_CC   = "CHASE_CC";
	const CMPTOP_CC  = "CMPTOP_CC";
	const CRDSTRM_CC = "CRDSTRM_CC";
	const CREDITCARD = "CREDITCARD";
	const CYBRSRC_CC = "CYBRSRC_CC";
	const DRCPAY_CC  = "DRCPAY_CC";
	const DTCASH_CC  = "DTCASH_CC";
	const DT_CC      = "DT_CC";
	const EFSNET_CC  = "EFSNET_CC";
	const ELAVON_CC  = "ELAVON_CC";
	const EPAY_CC    = "EPAY_CC";
	const EWAY_CC    = "EWAY_CC";
	const EXACT_CC   = "EXACT_CC";
	const FRSTDAT_CC = "FRSTDAT_CC";
	const GAMEDAY_CC = "GAMEDAY_CC";
	const GARANTI_CC = "GARANTI_CC";
	const GESTPAY_CC = "GESTPAY_CC";
	const HITRUST_CC = "HITRUST_CC";
	const INSPIRE_CC = "INSPIRE_CC";
	const INSTAP_CC  = "INSTAP_CC";
	const INTUIT_CC  = "INTUIT_CC";
	const IRIDIUM_CC = "IRIDIUM_CC";
	const LITLE_CC   = "LITLE_CC";
	const MASTPAY_CC = "MASTPAY_CC";
	const MERESOL_CC = "MERESOL_CC";
	const MERWARE_CC = "MERWARE_CC";
	const MODRPAY_CC = "MODRPAY_CC";
	const MONERIS_CC = "MONERIS_CC";
	const MSTPAY_CC  = "MSTPAY_CC";
	const NELTRAX_CC = "NELTRAX_CC";
	const NETBILL_CC = "NETBILL_CC";
	const NETREGS_CC = "NETREGS_CC";
	const NOCHEX_CC  = "NOCHEX_CC";
	const OGONE_CC   = "OGONE_CC";
	const OPTIMAL_CC = "OPTIMAL_CC";
	const PAYBOX_CC  = "PAYBOX_CC";
	const PAYEXPR_CC = "PAYEXPR_CC";
	const PAYFAST_CC = "PAYFAST_CC";
	const PAYFLOW_CC = "PAYFLOW_CC";
	const PAYJUNC_CC = "PAYJUNC_CC";
	const PLUGNPL_CC = "PLUGNPL_CC";
	const PP_WSPP_CC = "PP_WSPP_CC";
	const PSIGATE_CC = "PSIGATE_CC";
	const PSL_CC     = "PSL_CC";
	const PXPAY_CC   = "PXPAY_CC";
	const QUIKPAY_CC = "QUIKPAY_CC";
	const REALEX_CC  = "REALEX_CC";
	const SAGEPAY_CC = "SAGEPAY_CC";
	const SAGE_CC    = "SAGE_CC";
	const SAMURAI_CC = "SAMURAI_CC";
	const SCPTECH_CC = "SCPTECH_CC";
	const SCP_AU_CC  = "SCP_AU_CC";
	const SECPAY_CC  = "SECPAY_CC";
	const SG_CC      = "SG_CC";
	const SKIPJCK_CC = "SKIPJCK_CC";
	const SKRILL_CC  = "SKRILL_CC";
	const STRIPE_CC  = "STRIPE_CC";
	const TRNSFST_CC = "TRNSFST_CC";
	const TRUSTCM_CC = "TRUSTCM_CC";
	const USAEPAY_CC = "USAEPAY_CC";
	const VALITOR_CC = "VALITOR_CC";
	const VERIFI_CC  = "VERIFI_CC";
	const VIAKLIX_CC = "VIAKLIX_CC";
	const WIRECRD_CC = "WIRECRD_CC";
	const WLDPDIR_CC = "WLDPDIR_CC";
	const WLDPOFF_CC = "WLDPOFF_CC";

	const CNB    = "CNB";
	const SG_CNB = "SG_CNB";

	const MCM       = "MCM";
	const UPAID_MCM = "UPAID_MCM";

	const PAYU = "PAYU";

	const REDIRECTCC = "REDIRECTCC";
	const WORLDLINE  = "WORLDLINE";

	const SUE        = "SUE";
	const MSTPAY_SUE = "MSTPAY_SUE";
	const SG_SUE     = "SG_SUE";

	/**
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->setData(array('external_coupons' => array(), 'shopgate_coupons' => array(), 'items' => array()));
		parent::__construct($data);
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
	 * @param ShopgateShippingInfo $value
	 */
	public function setShippingInfos($value)
	{
		if (!is_object($value) && !($value instanceof ShopgateShippingInfo) && !is_array($value)) {
			$this->setData('shipping_infos', null);
			return;
		}

		if (is_array($value)) {
			$value = new ShopgateShippingInfo($value);
		}

		$this->setData('shipping_infos', $value);
	}

	/**
	 * @param ShopgateAddress|mixed[] $value
	 */
	public function setInvoiceAddress($value)
	{
		if (!is_object($value) && !($value instanceof ShopgateAddress) && !is_array($value)) {
			$this->setData('invoice_address', null);
			return;
		}

		if (is_array($value)) {
			$value = new ShopgateAddress($value);
			$value->setIsDeliveryAddress(false);
			$value->setIsInvoiceAddress(true);
		}
		$this->setData('invoice_address', $value);
	}

	/**
	 * @param ShopgateAddress|mixed[] $value
	 */
	public function setDeliveryAddress($value)
	{
		if (!is_object($value) && !($value instanceof ShopgateAddress) && !is_array($value)) {
			$this->setData('delivery_address', null);
			return;
		}

		if (is_array($value)) {
			$value = new ShopgateAddress($value);
			$value->setIsDeliveryAddress(true);
			$value->setIsInvoiceAddress(false);
		}
		$this->setData('delivery_address', $value);
	}

	/**
	 * @param ShopgateExternalCoupon[] $value
	 */
	public function setExternalCoupons($value)
	{
		if (!is_array($value)) {
			$this->setData('external_coupons', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateExternalCoupon)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateExternalCoupon($element);
			}
		}
		$this->setData('external_coupons', $value);
	}

	/**
	 * @param ShopgateShopgateCoupon[] $value
	 */
	public function setShopgateCoupons($value)
	{
		if (!is_array($value)) {
			$this->setData('shopgate_coupons', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateShopgateCoupon)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateShopgateCoupon($element);
			}
		}
		$this->setData('shopgate_coupons', $value);
	}

	/**
	 * @param ShopgateOrderItem []|mixed[][] $value
	 */
	public function setItems($value)
	{
		if (!is_array($value)) {
			$this->setData('items', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateOrderItem)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateOrderItem($element);
			}
		}
		$this->setData('items', $value);
	}
	
	/**
	 * @param ShopgateShippingMethod []|mixed[][] $value
	 */
	public function setShippingMethods($value)
	{
		if (!is_array($value)) {
			$this->setData('shipping_methods', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateShippingMethod)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateShippingMethod($element);
			}
		}
		$this->setData('shipping_methods', $value);
	}	
	
	/**
	 * @param ShopgatePaymentMethod []|mixed[][] $value
	 */
	public function setPaymentMethods($value)
	{
		if (!is_array($value)) {
			$this->setData('payment_methods', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgatePaymentMethod)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgatePaymentMethod($element);
			}
		}
		$this->setData('payment_methods', $value);
	}
		
	/**
	 * @param ShopgateCartItem []|mixed[][] $value
	 */
	public function setCartItems($value)
	{
		if (!is_array($value)) {
			$this->setData('cart_items', null);
			return;
		}

		foreach ($value as $index => &$element) {
			if ((!is_object($element) || !($element instanceof ShopgateCartItem)) && !is_array($element)) {
				unset($value[$index]);
				continue;
			}

			if (is_array($element)) {
				$element = new ShopgateCartItem($element);
			}
		}
		$this->setData('cart_items', $value);
	}
}

/**
 * Class ShopgateCart
 */
class ShopgateCart extends ShopgateCartBase
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitCart($this);
	}
}

/**
 * Class ShopgateShippingMethod
 *
 * @method string getId()
 * @method ShopgateShippingMethod setId(string $value)
 * @method string getTitle()
 * @method ShopgateShippingMethod setTitle(string $value)
 * @method string getDescription()
 * @method ShopgateShippingMethod setDescription(string $value)
 * @method int getSortOrder()
 * @method ShopgateShippingMethod setSortOrder(int $value)
 * @method float getAmount()
 * @method ShopgateShippingMethod setAmount(float $value)
 * @method float getAmountWithTax()
 * @method ShopgateShippingMethod setAmountWithTax(float $value)
 * @method string getTaxClass()
 * @method ShopgateShippingMethod setTaxClass(string $value)
 * @method float getTaxPercent()
 * @method ShopgateShippingMethod setTaxPercent(float $value)
 * @method string getInternalShippingInfos()
 * @method ShopgateShippingMethod setInternalShippingInfos(string $value)
 */
class ShopgateShippingMethod extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitShippingMethod($this);
	}
}

/**
 * Class ShopgatePaymentMethod
 *
 * @method string getId()
 * @method ShopgatePaymentMethod setId(string $value)
 * @method float getAmount()
 * @method ShopgatePaymentMethod setAmount(float $value)
 * @method float getAmountWithTax()
 * @method ShopgatePaymentMethod setAmountWithTax(float $value)
 * @method string getTaxClass()
 * @method ShopgatePaymentMethod setTaxClass(string $value)
 * @method float getTaxPercent()
 * @method ShopgatePaymentMethod setTaxPercent(float $value)
 */
class ShopgatePaymentMethod extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitPaymentMethod($this);
	}
}

/**
 * Class ShopgateQuoteItem
 *
 * @method string getItemNumber()
 * @method ShopgateCartItem setItemNumber(string $value)
 * @method bool getIsBuyable()
 * @method ShopgateCartItem setIsBuyable(bool $value)
 * @method int getQtyBuyable()
 * @method ShopgateCartItem setQtyBuyable(int $value)
 * @method float getUnitAmount()
 * @method ShopgateCartItem setUnitAmount(float $value)
 * @method float getUnitAmountWithTax()
 * @method ShopgateCartItem setUnitAmountWithTax(float $value)
 */
class ShopgateCartItem extends ShopgateContainer
{
	/**
	 * @param ShopgateContainerVisitor $v
	 */
	public function accept(ShopgateContainerVisitor $v)
	{
		$v->visitCartItem($this);
	}
}