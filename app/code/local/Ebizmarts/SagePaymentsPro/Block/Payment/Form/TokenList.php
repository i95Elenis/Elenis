<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/10/13
 * Time   : 1:01 PM
 * File   : TokenList.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Payment_Form_TokenList extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebizmarts/sagepaymentspro/payment/form/tokenlist.phtml');
    }
    protected function _toHtml()
    {
        if(!$this->getCanUseToken())
        {
            return '';
        }
        return parent::_toHtml();
    }
    public function getMaxTokens()
    {
        return Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN_MAX);
    }

}