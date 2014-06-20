<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/14/13
 * Time   : 2:15 PM
 * File   : Transaction.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Transaction extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('ebizmarts_sagepaymentspro/transaction');
    }
}