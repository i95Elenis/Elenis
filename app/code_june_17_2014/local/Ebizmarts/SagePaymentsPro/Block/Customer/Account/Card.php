<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/22/13
 * Time   : 1:00 AM
 * File   : Card.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Customer_Account_Card extends Mage_Core_Block_Template
{
    protected $_cards = null;

    public function getCustomerCards()
{
    if (is_null($this->_cards)) {

        $this->_cards = $this->helper('ebizmarts_sagepaymentspro/token')->loadCustomerCards();

    }

    return $this->_cards;
}
}