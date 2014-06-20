<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/18/13
 * Time   : 1:47 PM
 * File   : Collection.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_Resource_Tokencard_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ebizmarts_sagepaymentspro/tokencard');
    }
    public function addCustomerFilter($customer) {

        if (is_string($customer)) {
            $this->addFieldToFilter('visitor_session_id', $customer);
        } else if ($customer instanceof Mage_Customer_Model_Customer) {
            $this->addFieldToFilter('customer_id', $customer->getId());
        } elseif (is_numeric($customer)) {
            $this->addFieldToFilter('customer_id', $customer);
        } elseif (is_array($customer)) {
            $this->addFieldToFilter('customer_id', $customer);
        }

        return $this;
    }

}