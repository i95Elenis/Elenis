<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 11:58 AM
 * File   : Config.php
 * Module : sagepaymentspro-new
 */
class Ebizmarts_SagePaymentsPro_Model_Config extends Mage_Payment_Model_Config
{
    const CONFIG_TOKEN      = 'payment/sagepaymentspro/token_integration';
    const CONFIG_MID        = 'payment/sagepaymentspro/m_id';
    const CONFIG_MKEY       = 'payment/sagepaymentspro/m_key';
    const CONFIG_LOG        = 'payment/sagepaymentspro/log';
    const CONFIG_TOKEN_URL  = 'payment/sagepaymentspro/token_url';
    const CONFIG_TOKEN_MAX  = 'payment/sagepaymentspro/max_token_card';
    const CONFIG_LICENSE    = 'payment/sagepaymentspro/license_key';
    const CONFIG_ENABLE     = 'payment/sagepaymentspro/active';
    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
    public function getCcTypesSagePayments()
    {
        $types = array();
        foreach (Mage::getConfig()->getNode('global/payment/sagepayments_cards/types')->asArray() as $data) {
            $types[$data['code']] = $data['name'];
        }
        return $types;
    }

}