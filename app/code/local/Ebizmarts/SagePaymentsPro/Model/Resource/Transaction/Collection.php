<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/14/13
 * Time   : 4:14 PM
 * File   : Collection.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Resource_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ebizmarts_sagepaymentspro/transaction');
    }
}