<?php
class Elenis_CustomCheckoutMultipleAddress_Model_Resource_Multiship extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init("customcheckoutmultipleaddress/multiship", "id");
    }
}