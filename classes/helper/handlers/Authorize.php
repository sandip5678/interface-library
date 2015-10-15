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

require_once('AuthorizeAPI/autoload.php');

class Authorize_Handler
{
    protected $sandbox = true;
    protected $login_id = '2dC47NtHUv';
    protected $trans_key = '36K4j7tEdU849Hse';

    /**
     * Retrieves the transaction if from Authorize
     *
     * @param string $type - auth_only or auth_capture
     *
     * @return AuthorizeNetResponse
     * @throws Exception
     */
    public function getTransaction($type)
    {
        $sale = new AuthorizeNetAIM($this->login_id, $this->trans_key);
        $sale->setSandbox($this->sandbox);
        $sale->amount   = '509.07';
        $sale->card_num = '6011000000000012';
        $sale->exp_date = '04/' . rand(16, 30);
        if ($type == 'auth_only') {
            $response = $sale->authorizeOnly();
        } else {
            $response = $sale->authorizeAndCapture();
        }

        if (empty($response->transaction_id)) {
            throw new Exception($response->error_message);
        }
        return $response;
    }
}