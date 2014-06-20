<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/18/13
 * Time   : 1:31 PM
 * File   : Tokencard.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Tokencard extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('ebizmarts_sagepaymentspro/tokencard');
    }
    public function getCcNumber() {
        return '***********' . $this->getLastFour();
    }
    public function getExpireDate() {
        return Mage::helper('ebizmarts_sagepaymentspro')->getCardNiceDate($this->getExpiryDate());
    }
    public function getLabel($withImage = true) {
        return Mage::helper('ebizmarts_sagepaymentspro')->getCardLabel($this->getCardType(), $withImage);
    }
    public function customerCanAddCard($customerId)
    {
        $cards = Mage::helper('ebizmarts_sagepaymentspro/token')->loadCustomerCards();
        $maxtokens = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN_MAX);
        if($cards->getSize() < $maxtokens) {
            return true;
        }
        return false;
    }

}