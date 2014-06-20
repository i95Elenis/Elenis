<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/10/13
 * Time   : 4:17 PM
 * File   : Token.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Token
{
    protected $_code = "sagepaymentspro";
    protected $_formBlockType = "ebizmarts_sagepaymentspro/payment_form_sagePaymentsProToken";

    public function isEnabled()
    {
        return (bool) (Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN)!= false);
    }
}