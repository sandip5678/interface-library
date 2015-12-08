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
 * Class FakeMapper
 *
 * @author: Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class FakeMapper
{
    protected $_paymentMap = array(
        ShopgateCartBase::PAYONE_PP      => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_PP',
            'payment_group'  => 'PAYPAL',
            'customer_name'  => 'PayonePP',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'PayPal (PAYONE)',
                'clearing_type'         => 'wlt',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => null,
                'status'                => 'APPROVED',
                'txid'                  => '168170687',
                'settleaccount'         => 'yes',
                'request_type'          => 'authorization'
            ),
        ),
        ShopgateCartBase::PAYONE_PRP     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_PRP',
            'payment_group'  => 'PREPAY',
            'customer_name'  => 'PayonePrepay',
            'payment_infos'  => array(
                'shopgate_payment_name'      => 'Vorkasse (PAYONE)',
                'clearing_type'              => 'vor',
                'mode'                       => 'test',
                'mid'                        => '24906',
                'aid'                        => '25775',
                'portalid'                   => '2017714',
                'userid'                     => '70718094',
                'status'                     => 'APPROVED',
                'txid'                       => '168170673',
                'clearing_bankaccount'       => '0022520120',
                'clearing_bankcode'          => '21070020',
                'clearing_bankcountry'       => 'DE',
                'clearing_bankname'          => 'Deutsche Bank AG',
                'clearing_bankaccountholder' => 'PAYONE GmbH',
                'clearing_bankcity'          => 'Kiel',
                'clearing_bankiban'          => 'DE87210700200022520120',
                'clearing_bankbic'           => 'DEUTDEHH210',
                'request_type'               => 'authorization'
            ),
        ),
        ShopgateCartBase::PAYONE_CC      => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_CC',
            'payment_group'  => 'CC',
            'customer_name'  => 'PayoneCC',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Credit Card (PAYONE)',
                'clearing_type'         => 'cc',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => '70607606',
                'status'                => 'APPROVED',
                'settleaccount'         => 'yes',
                'txid'                  => '168170659',
                'request_type'          => 'authorization',
                'credit_card'           => array(
                    'holder'        => "Max Mutsermann",
                    'masked_number' => '************1111',
                    'type'          => 'visa'
                )
            ),
        ),
        ShopgateCartBase::PAYONE_KLV     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_KLV',
            'payment_group'  => 'INVOICE',
            'customer_name'  => 'Payone Invoice Klarna',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Rechnung (PAYONE)',
                'clearing_type'         => 'fnc',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2019115',
                'userid'                => '67435410',
                'status'                => 'APPROVED',
                'txid'                  => '168170704',
                'request_type'          => 'authorization',
            ),
        ),
        ShopgateCartBase::PAYONE_INV     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_INV',
            'payment_group'  => 'INVOICE',
            'customer_name'  => 'Payone Invoice Other',
            'payment_infos'  => array(
                'shopgate_payment_name'      => 'Rechnung (PAYONE)',
                'clearing_type'              => 'rec',
                'mode'                       => 'test',
                'mid'                        => '24906',
                'aid'                        => '25775',
                'portalid'                   => '2017714',
                'userid'                     => '70727732',
                'status'                     => 'APPROVED',
                'txid'                       => '168170716',
                'clearing_bankaccount'       => '0022520120',
                'clearing_bankcode'          => '21070020',
                'clearing_bankcountry'       => 'DE',
                'clearing_bankname'          => 'Deutsche Bank AG',
                'clearing_bankaccountholder' => 'PAYONE GmbH',
                'clearing_bankcity'          => 'Kiel',
                'clearing_bankiban'          => 'DE87210700200022520120',
                'clearing_bankbic'           => 'DEUTDEHH210',
                'request_type'               => 'authorization',
            ),
        ),
        ShopgateCartBase::PAYONE_GP      => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_GP',
            'payment_group'  => 'GIROPAY',
            'customer_name'  => 'Payone GiroPay',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Giropay (PAYONE)',
                'clearing_type'         => 'sb',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => null,
                'txid'                  => '168170731',
                'status'                => 'APPROVED',
                'request_type'          => 'authorization',
                'bank_account'          => array(
                    'iban' => 'DE81800393907672170911',
                    'bic'  => 'PBNKDEFF',
                )
            ),
        ),
        ShopgateCartBase::PAYONE_IDL     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_IDL',
            'payment_group'  => 'IDEAL',
            'customer_name'  => 'Payone IDEAL',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'iDEAL (PAYONE)',
                'clearing_type'         => 'sb',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => null,
                'status'                => 'APPROVED',
                'txid'                  => '168170745',
                'settleaccount'         => 'no',
                'request_type'          => 'authorization',
                'bank_account'          => array(
                    'ideal_bank' => 'ABN_AMRO_BANK',
                )
            ),
        ),
        ShopgateCartBase::PAYONE_SUE     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_SUE',
            'payment_group'  => 'SUE',
            'customer_name'  => 'Payone Sofort',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Sofort¸berweisung (PAYONE)',
                'clearing_type'         => 'sb',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => null,
                'txid'                  => '168170765',
                'status'                => 'APPROVED',
                'request_type'          => 'authorization',
                'bank_account'          => array(
                    'iban' => 'DE81800393907672170911',
                    'bic'  => 'PBNKDEFF',
                )
            ),
        ),
        ShopgateCartBase::PAYONE_DBT     => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYONE_DBT',
            'payment_group'  => 'DEBIT',
            'customer_name'  => 'Payone Debit',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Bankkonto (Lastschrift) (PAYONE)',
                'clearing_type'         => 'elv',
                'mode'                  => 'test',
                'mid'                   => '24906',
                'aid'                   => '25775',
                'portalid'              => '2017714',
                'userid'                => '63672544',
                'request_type'          => 'authorization',
                'bank_account'          =>
                    array(
                        'bank_account_holder' => 'Liam Barker',
                        'bank_account_number' => '12345',
                        'bank_code'           => '80000000',
                        'iban'                => '',
                        'bic'                 => '',
                    ),
                'status'                => 'APPROVED',
                'txid'                  => '168170775',
            ),
        ),
        ShopgateCartBase::AUTHN_CC       => array(
            'payment_method' => 'AUTHN_CC',
            'payment_group'  => 'CC',
            'customer_name'  => 'AuthorizeNet',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Credit card (Authorize.net)',
                'transaction_id'        => '2232308291',
                'response_code'         => 1,
                'response_reason_code'  => 1,
                'response_reason_text'  => 'This transaction has been approved.',
                'md5_hash'              => '8A33D31BAAF1553F2D17ABC41FED6DDD',
                'authorization_code'    => 'GVQ87U',
                'transaction_type'      => 'auth_only', //between auth_capture & auth_only
                'avs_response'          => 'Y',
                'card_code_response'    => 'M',
                'credit_card'           =>
                    array(
                        'holder'        => 'Tester',
                        'masked_number' => '1111', //matters for CIM existing customer card check
                        'type'          => 'visa',
                        'expiry_year'   => '2018',
                        'expiry_month'  => '7',
                    ),
            ),
        ),
        ShopgateCartBase::USAEPAY_CC     => array(
            'is_paid'        => 1,
            'payment_method' => 'USAEPAY_CC',
            'payment_group'  => 'CC',
            'customer_name'  => 'USA ePay',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Kreditkarte (USA ePay)',
                'source_key'            => '_OzGo8QwftswrN4CQ94649V0226kHJEW',
                'authorization_number'  => 'TESTMD',
                'transaction_type'      => 'authonly',
                'reference_number'      => '0',
                'credit_card'           =>
                    array(
                        'holder'        => 'Test Namel',
                        'masked_number' => '***********5006',
                        'type'          => 'american_express',
                        'expiry_year'   => '2018',
                        'expiry_month'  => '7',
                    ),
            ),
        ),
        ShopgateCartBase::AMAZON_PAYMENT => array(
            'is_paid'        => 1,
            'payment_method' => 'MWS',
            'payment_group'  => 'MWS',
            'customer_name'  => 'Amazon',
            'payment_infos'  => array(
                'shopgate_payment_name'  => 'Amazon Payments (Ihr Amazon-Payment account)',
                'payment_transaction_id' => '10357',
                'mws_order_id'           => 'S02-5734774-9765978',
                'mws_auth_id'            => 'S02-5734774-9765978-A036728',
                'mws_capture_id'         => 'S02-1352651-9441604',
                'mws_merchant_id'        => 'A2ZQNLLUCP0P8L',
                'mws_payment_date'       => '2015-04-08 07:04:07',
                'mws_refund_ids'         => array(),
            ),
        ),
        ShopgateCartBase::PAYPAL         => array(
            'is_paid'        => 1,      //matters in status setting
            'payment_method' => 'PAYPAL',
            'payment_group'  => 'PAYPAL',
            'customer_name'  => 'Paypal Express',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'PayPal (Your PayPal account)',
                'transaction_id'        => '02D283506N5873144', //important for refunding
                'token'                 => null,
                'payer_id'              => 'JV6B8C97F53RA',
                'payer_email'           => 'left_leg@hotmail.com',
                'receiver_email'        => 'timnilson@fpvmanuals.com',
                'receiver_id'           => 'YA8MLF56Q57M4',
                'invnum'                => '5501015850',
                'payer_status'          => 'verified',
                'payment_type'          => 'instant',
                //'payment_status'        => 'Completed',
                'payment_status'        => 'Pending',
                'payment_date'          => '07:49:29 Apr 23, 2015 PDT',
                'mc_shipping'           => '3.32',
                'mc_currency'           => 'USD',
                'mc_fee'                => '0.42',
                'mc_gross'              => '6.25',
                'num_cart_items'        => '1',
                'first_name'            => 'Aaron',
                'last_name'             => 'Martin',
                'address_name'          => 'aaron martin',
                'address_street'        => '2000 E Williams Field Rd',
                'address_city'          => 'Gilbert',
                'address_state'         => 'AZ',
                'address_zip'           => '85295',
                'address_country'       => 'United States',
                'address_status'        => 'confirmed',
                'txn_type'              => 'cart',
                'residence_country'     => 'US',
                'address_country_code'  => 'US',
                'pending_reason'        => null,
                'reason_code'           => null,
                'business'              => null,
            ),
        ),
        ShopgateCartBase::PP_WSPP_CC     => array(
            'is_paid'        => 1,      //doesn't matter, see payment_status
            'payment_method' => 'PP_WSPP_CC',
            'payment_group'  => 'CC',
            'customer_name'  => 'Paypal WPP',
            'payment_infos'  => array(
                'shopgate_payment_name' => 'Kreditkarte (PayPal Website Payments Pro)',
                'paypal_transaction_id' => '55P978957M513884X',
                'paypal_payer_id'       => '',
                'paypal_payer_email'    => '',
                'paypal_receiver_id'    => '',
                'paypal_receiver_email' => '',
                'paypal_txn_id'         => '',
                'paypal_ipn_track_id'   => '',
                'paypal_ipn_data'       => array(
                    'payment_status' => 'Pending',
                    'txn_id' => '55P978957M513884X',
                    'payer_id' => 'RADZRFMHXVGTA',
                    'receiver_id' => '28BFM6WRTF46S',
                    'verify_sign' => 'AUEfhqAqAHlSqPeWXpLjIRqawSIDAcPDcRf2yMUR0.OAurhUgihywh0O',
                    'ipn_track_id' => 'f4d71d4945618',
                    'invoice' => '100000016',
                    'protection_eligibility' => 'Eligible',
                    'mc_gross' => '272.22',
                    'address_status' => 'unconfirmed',
                    'tax' => '0.00',
                    'address_street' => 'blubbstra\u00dfe 10',
                    'payment_date' => '01:42:59 Mar 10, 2015 PDT',
                    'payer_status' => 'verified',
                    'txn_type' => 'web_accept'
                ),
                'credit_card'           =>
                    array(
                        'holder'        => 'Gustav diFolt',
                        'masked_number' => '************6829',
                        'type'          => 'mastercard',
                    ),
            ),
        ),
        ShopgateCartBase::SUE            => array(
            'is_paid'        => 0,
            'payment_method' => 'SUE',
            'payment_group'  => 'SUE',
            'customer_name'  => 'Sofort Uber Weisung',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Sofortüberweisung (Ihren Sofortüberweisung-Zugang)',
                    'configuration_key'     => '29443:170688:***',
                    'transaction_id'        => '29443-170688-55808DF4-8A02'
                ),
        ),
        ShopgateCartBase::PREPAY         => array(
            'is_paid'        => 0,
            'payment_method' => 'PREPAY',
            'payment_group'  => 'PREPAY',
            'customer_name'  => 'Bank',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Vorkasse (Eigene Abwicklung)',
                    'purpose'               => 'SG1501511499',
                ),
        ),
        ShopgateCartBase::COD            => array(
            'is_paid'        => 0,
            'payment_method' => 'COD',
            'payment_group'  => 'COD',
            'customer_name'  => 'Cash On Delivery',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Nachnahme (Eigene Abwicklung)',
                ),
        ),
        ShopgateCartBase::INVOICE        => array(
            'is_paid'        => 0,
            'payment_method' => 'INVOICE',
            'payment_group'  => 'INVOICE',
            'customer_name'  => 'Invoice',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Invoice Payment',
                ),
        ),
        ShopgateCartBase::SHOPGATE       => array(
            'is_paid'        => 0,
            'payment_method' => 'SHOPGATE',
            'payment_group'  => 'SHOPGATE',
            'customer_name'  => 'Shopgate',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Shopgate Payment',
                ),
        ),
        ShopgateCartBase::COLL_STORE     => array(
            'is_paid'        => 0,
            'payment_method' => 'COLL_STORE',
            'payment_group'  => 'COLL_STORE',
            'customer_name'  => 'Store Pickup OR On Site Pickup',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Abholung in einer Filiale (Eigene Abwicklung)',
                ),
        ),
        ShopgateCartBase::BILLSAFE       => array(
            'order_number'   => 1500004873,
            'is_paid'        => 0,
            'payment_method' => 'BILLSAFE',
            'payment_group'  => 'INVOICE',
            'customer_name'  => 'BillSAFE',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name'   => 'Rechnung (BillSAFE)',
                    'billsafe_transaction_id' => null,
                    'billsafe_token'          => '553a401a17e1a553a401a17e69',
                    'recipient'               => 'Test User',
                    'reference'               => 'SG1500003820',
                    'amount'                  => 25.489999999999998436805981327779591083526611328125,
                    'currency_code'           => 'EUR',
                    'billsafe_response'       => '{"ack":"OK", "status":"DECLINED", "declineReason":{ "code":"101", "message":"BillSAFE does not secure this transaction", "buyerMessage":"Thank you for your purchase."}}',
                ),
        ),
        ShopgateCartBase::PAYOL_INV      => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYOL_INV',
            'payment_group'  => 'INVOICE',
            'customer_name'  => 'Payolution Invoice',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Invoice (Payolution)',
                    'status'                => 'NEW',
                    'transaction_id'        => 'SG1500006765',
                    'unique_id'             => '8a82944a4f45ce6e014f463d02764178',
                    'preauth_id'            => '8a82944a4f45ce6e014f463d02764178',
                    'capture_id'            => '8a82944a4f45ce6e014f463d02764178',
                    'short_id'              => '1831.7649.5778',
                    'reference_id'          => null,
                    'ip'                    => '10.10.10.97',
                ),
        ),
        ShopgateCartBase::PAYOL_INS      => array(
            'is_paid'        => 0,
            'payment_method' => 'PAYOL_INS',
            'payment_group'  => 'INSTALLMENT',
            'customer_name'  => 'Payolution Installment',
            'payment_infos'  =>
                array(
                    'shopgate_payment_name' => 'Installment (Payolution)',
                    'status'                => 'NEW',
                    'transaction_id'        => 'SG1500006768',
                    'unique_id'             => '8a8294494f45c16f014f460002f62fc4',
                    'preauth_id'            => '8a8294494f45c16f014f460002f62fc4',
                    'capture_id'            => '8a8294494f45c16f014f460002f62fc4',
                    'short_id'              => '8854.0364.8674',
                    'reference_id'          => 'NZMR-XXHT-PDGB',
                    'ip'                    => '10.10.10.97',
                    'plan'                  =>
                        array(
                            'unique_id'       => 'Tx-rkz7q4jrirs',
                            'original_amount' => '288.00',
                            'total_amount'    => '296.73',
                            'currency'        => 'EUR',
                            'duration'        => '9',
                            'accepted'        => true,
                        ),
                    'bank_data'             =>
                        array(
                            'bank_holder'     => 'holder',
                            'bank_iban'       => 'iban',
                            'bank_bic'        => 'bic',
                            'bank_country_id' => 'de',
                        ),
                )

        )
    );

    /**
     * @param $request
     * @return array
     * @throws Exception
     */
    public function getOrderFromMethod($request)
    {
        if (isset($request['payment_method'])) {
            $paymentMethod = $request['payment_method'];
            $map           = $this->_paymentMap[$paymentMethod];

            //flip between unpaid/paid request
            if (isset($request['flip'])) {
                $this->isPaidFlip($map, $request['flip']);
            } else {
                $this->isPaidFlip($map, 1);
            }

            //authorize CC injection for AuthCIM
            if (isset($map['payment_infos']['credit_card']['masked_number'])) {
                $map['payment_infos']['credit_card']['masked_number'] = rand(0000, 9999); //randomize card #
            }

            //Transaction ID creators
            if (strpos($paymentMethod, 'PAYONE') !== false) {
                $helper  = new Payone_Handler();
                $transId = $helper->getTransactionId($paymentMethod);
                if ($transId) {
                    $map['payment_infos']['txid'] = $transId;
                }
            } elseif ($paymentMethod === 'AUTHN_CC') {
                $helper  = new Authorize_Handler();
                $trans = $helper->getTransaction($map['payment_infos']['transaction_type']);
                $map['payment_infos']['transaction_id'] = $trans->transaction_id;
                $map['payment_infos']['authorization_code'] = $trans->authorization_code;
                $map['payment_infos']['md5_hash'] = $trans->md5_hash;
            }
            
            /** @var ShopgateOrder $fakeOrder */
            $fakeOrder = $this->_getFakeOrder($map);
            $fakeOrder->setItems($this->productSwitcher($fakeOrder->getItems()));
            return array($fakeOrder);
        }
        throw new Exception('Payment method provided was empty');
    }

    /**
     * Helps flipping is_paid variables for different
     * payment methods
     *
     * @param     $map
     * @param int $paid
     * @return bool
     */
    protected function isPaidFlip(&$map, $paid = 0)
    {
        $flag  = false;
        $infos = $map['payment_infos'];
        if (isset($infos['transaction_type'])) {
            if ($map['payment_method'] === 'USAEPAY_CC') {
                $infos['transaction_type'] = $paid == 0 ? 'authonly' : 'sale';
                $flag                      = true;
            } else {
                $infos['transaction_type'] = $paid == 0 ? 'auth_only' : 'auth_capture';
                $flag                      = true;
            }

        }
        if (isset($infos['payment_status'])) {
            $infos['payment_status'] = $paid == 0 ? 'Pending' : 'Completed';
            $flag                    = true;
        }
        if (isset($infos['paypal_ipn_data'])) {
            $infos['paypal_ipn_data']['payment_status'] = $paid == 0 ? 'Pending' : 'Completed';
            $flag                                       = true;
        }
        if (isset($map['is_paid'])) {
            $map['is_paid'] = $paid;
            $flag           = true;
        }
        if (isset($infos['mws_order_id'])) {
            if ($paid == 0) {
                $infos['mws_capture_id'] = '';
                $flag                    = true;
            }
        }
        /**
         * PayOne flag
         */
        if (isset($infos['request_type'])) {
            $infos['request_type'] = $paid == 0 ? 'preauthorization' : 'authorization';
            $flag                  = true;
        }

        /**
         * Payolution flag
         */
        if (isset($infos['capture_id'])) {
            if ($paid == 0) {
                $infos['capture_id'] = '';
                $flag                = true;
            }
        }
        return $flag;
    }

    /**
     * @param $map
     * @return array
     */
    protected function _getFakeOrder($map)
    {
        return
            new ShopgateOrder(
                array(
                    'order_number'                => isset($map['order_number']) ? $map['order_number'] : rand(
                        1000000000,
                        9999999999
                    ),
                    'confirm_shipping_url'        => 'https://www.shopgate.com/clickcs/1013466122/4e30fb1be239c25f3c75dbe2456dfdbd',
                    'created_time'                => '2014-03-20T11:14:03+00:00',
                    'is_paid'                     => isset($map['is_paid']) ? $map['is_paid'] : 1,
                    'payment_time'                => null,
                    'payment_transaction_number'  => null,
                    'payment_infos'               => $map['payment_infos'],
                    'is_shipping_blocked'         => 0,
                    'is_shipping_completed'       => 1,
                    'shipping_completed_time'     => '2014-08-06T11:57:29+00:00',
                    'is_test'                     => 0,
                    'is_storno'                   => 0,
                    'is_customer_invoice_blocked' => '0',
                    'update_shipping'             => false,
                    'update_payment'              => false,
                    'delivery_notes'              => null,
                    'customer_number'             => '2059691',
                    'external_order_number'       => null,
                    'external_order_id'           => '8240',
                    'external_customer_number'    => null,
                    'external_customer_id'        => null,
                    'custom_fields' => array(
                        array(
                            'label'               => 'Test Custom Field',
                            'internal_field_name' => 'test_field',
                            'value'               => 'test field value',
                        ),
                    ),
                    'mail'                        => 'test@test.com',
                    'phone'                       => null,
                    'mobile'                      => null,
                    'shipping_group'              => 'DHL',
                    'shipping_type'               => 'MANUAL',
                    'shipping_infos'              => array(
                        'name'         => 'DHL Deutschland',
                        'display_name' => 'DHL Pakte gogreen',
                        'description'  => '',
                        'amount'       => '4.90',
                        'amount_net'   => '4.90',
                        'weight'       => 0,
                        'api_response' => null,
                    ),
                    'payment_method'              => $map['payment_method'],
                    'payment_group'               => $map['payment_group'],
                    'amount_items'                => '139.95',
                    'amount_shipping'             => '4.90',
                    'amount_shop_payment'         => '5.00',
                    'payment_tax_percent'         => '20.00',
                    'shipping_tax_percent'        => '20.00',
                    'amount_shopgate_payment'     => '0.00',
                    'amount_complete'             => '509.07',
                    'currency'                    => 'USD',
                    'invoice_address'             => array(
                        'id'                  => null,
                        'is_invoice_address'  => true,
                        'is_delivery_address' => false,
                        'first_name'          => $map['customer_name'],
                        'last_name'           => 'Payment',
                        'gender'              => 'f',
                        'birthday'            => null,
                        'company'             => null,
                        'street_1'            => 'Zevener Straße 8',
                        'street_2'            => null,
                        'zipcode'             => '27404',
                        'city'                => 'Frankenbostel',
                        'country'             => 'DE',
                        'state'               => null,
                        'phone'               => null,
                        'mobile'              => null,
                        'mail'                => null,
                        'custom_fields' => array(
                            array(
                                'label'               => 'Is house?',
                                'internal_field_name' => 'is_house',
                                'value'               => 1,
                            )
                        ),
                    ),
                    'delivery_address'            => array(
                        'id'                  => null,
                        'is_invoice_address'  => false,
                        'is_delivery_address' => true,
                        'first_name'          => $map['customer_name'],
                        'last_name'           => 'Payment',
                        'gender'              => 'f',
                        'birthday'            => null,
                        'company'             => null,
                        'street_1'            => 'Zevener Straße 8',
                        'street_2'            => null,
                        'zipcode'             => '27404',
                        'city'                => 'Frankenbostel',
                        'country'             => 'DE',
                        'state'               => null,
                        'phone'               => null,
                        'mobile'              => null,
                        'mail'                => null,
                        'custom_fields' => array(
                            array(
                                'label'               => 'Is house?',
                                'internal_field_name' => 'is_house',
                                'value'               => 0,
                            )
                        ),
                    ),
                    'external_coupons'            =>
                        array(),
                    'shopgate_coupons'            =>
                        array(),
                    'items'                       =>
                        array(
                            array(
                                'item_number'          => '554',
                                'item_number_public'   => '554',
                                'parent_item_number'   => null,
                                'order_item_id'        => '4399298',
                                'quantity'             => 1,
                                'name'                 => 'Swiss Movement Sports Watch',
                                'unit_amount'          => '500.00',
                                'unit_amount_with_tax' => '595.00',
                                'tax_percent'          => '19.00',
                                'tax_class_key'        => null,
                                'tax_class_id'         => null,
                                'currency'             => 'USD',
                                'internal_order_info'  => '{"product_id":554}',
                                'options'              =>
                                    array(),
                                'inputs'               =>
                                    array(),
                                'attributes'           =>
                                    array(),
                            ),
                            /*array(
                                'item_number'          => '162',
                                'item_number_public'   => '162',
                                'parent_item_number'   => null,
                                'order_item_id'        => '4399299',
                                'quantity'             => 1,
                                'name'                 => 'Swiss Movement Sports Watch',
                                'unit_amount'          => '59.99',
                                'unit_amount_with_tax' => '59.99',
                                'tax_percent'          => '0.00',
                                'tax_class_key'        => null,
                                'tax_class_id'         => null,
                                'currency'             => 'USD',
                                'internal_order_info'  => '{"product_id":162}',
                                'options'              =>
                                    array(),
                                'inputs'               =>
                                    array(),
                                'attributes'           =>
                                    array(),
                            ),/*
                             /*array(
                                 'item_number'          => 'TOMTAILOR_COUPON',
                                 'item_number_public'   => 'TOMTAILOR_COUPON',
                                 'parent_item_number'   => null,
                                 'order_item_id'        => 0,
                                 'quantity'             => 1,
                                 'type'                 => ShopgateOrderItem::TYPE_SHOPGATE_COUPON,
                                 'name'                 => 'Coupon (Code: TOMTAILOR)',
                                 'unit_amount'          => '-5.00',
                                 'unit_amount_with_tax' => '0',
                                 'tax_percent'          => '0',
                                 'tax_class_key'        => null,
                                 'tax_class_id'         => null,
                                 'currency'             => 'USD',
                                 'internal_order_info'  => null,
                                 'options'              =>
                                     array(),
                                 'inputs'               =>
                                     array(),
                                 'attributes'           =>
                                     array(),
                             ),*/
                        )
                )
            );
    }

    /**
     * Not operational, for mass order creation
     * Can't get it to work, having issues with session
     *
     * @return array
     */
    protected function _runAllPaymentMethods()
    {
        $calls = array();
        foreach ($this->_paymentMap as $map) {
            if ($this->isPaidFlip($map, 0)) {
                $calls[] = $this->_getFakeOrder($map);
            }
            if ($this->isPaidFlip($map, 1)) {
                $calls[] = $this->_getFakeOrder($map);
            }
        }
        return $calls;
    }

    /**
     * Manages switching the first item's ID
     * based on magento version
     *
     * @param $items array
     * @return array
     */
    protected function productSwitcher($items)
    {
        /** @var ShopgateOrderItem $item */
        $item = $items[0]; //grab first item
        if ($item instanceof ShopgateOrderItem) {
            /** @var StdClass $decode */
            $decode               = json_decode($item->getInternalOrderInfo(), true);
            $decode['product_id'] = $this->getMageProduct();
            $item->setInternalOrderInfo(json_encode($decode));
            $items[0] = $item;
        }
        return $items;
    }

    /**
     * Gets the correct product id for the right store
     *
     * @return string
     */
    protected function getMageProduct()
    {
        $helper = Mage::helper('shopgate/config');
        if ($helper->getIsMagentoVersionLower16()) {
            return '1';
        } elseif ($helper->getIsMagentoVersionLower('1.9.0.0')) {
            return '16';
        }
        return '554';
    }
}