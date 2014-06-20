<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/10/13
 * Time   : 1:12 PM
 * File   : Token.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Helper_Token extends Mage_Core_Helper_Abstract
{
    protected $_tokenCards = null;

    public function loadCustomerCards()
    {
        $this->_tokenCards = new Varien_Object;

        if (!$this->_tokenCards->getSize()) {

            $_id = $this->getCustomerQuoteId();

            if(is_numeric($_id)) {
                if($_id === 0) {
                    return $this->_tokenCards;
                }
            }
            $this->_tokenCards = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->getCollection()
                ->setOrder('id', 'DESC')
                ->addCustomerFilter($_id)
                ->load();
        }
        return $this->_tokenCards;

    }
    public function getDefaultToken()
    {
        $_id = $this->getCustomerQuoteId();
        $card = $this->_tokenCards = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->getCollection()
            ->addFieldToFilter('is_default', (int) 1)
            ->addCustomerFilter($_id)
            ->load()->getFirstItem();
        if ($card->getId()) {
            return $card;
        }
        return new Varien_Object;
    }
    public function getCustomerQuoteId() {
        $id = null;

        if (Mage::getSingleton('adminhtml/session_quote')->getQuoteId()) { #Admin
            $id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        } else if (Mage::getSingleton('customer/session')->getCustomerId()) { #Logged in frontend
            $id = Mage::getSingleton('customer/session')->getCustomerId();
        } else { #Guest/Register
            $vdata = Mage::getSingleton('core/session')->getVisitorData();
            return (string) $vdata['session_id'];
        }

        return (int) $id;
    }
}