<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/10/13
 * Time   : 12:09 PM
 * File   : SagePaymentsProToken.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Payment_Form_SagePaymentsProToken extends Mage_Payment_Block_Form_Cc
{
    public function getTokenCards()
    {
        return $this->helper('ebizmarts_sagepaymentspro/token')->loadCustomerCards();
    }

    public function canUseToken()
    {
        $ret = Mage::getModel('ebizmarts_sagepaymentspro/token')->isEnabled();

        if(!$this->helper('ebizmarts_sagepaymentspro')->creatingAdminOrder()) {
            $ret = $ret && (Mage::getModel('checkout/type_onepage')->getCheckoutMethod() != 'guest');
        }

        return $ret;
    }
    public function getMaxTokens()
    {
        return Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN_MAX);
    }

}