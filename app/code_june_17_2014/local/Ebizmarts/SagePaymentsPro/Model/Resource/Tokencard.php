<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/18/13
 * Time   : 1:32 PM
 * File   : Tokencard.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Resource_Tokencard extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ebizmarts_sagepaymentspro/tokencard','id');
    }

}