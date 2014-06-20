<?php
/**
 * @author Amasty
 */ 
class Amasty_Conf_Model_Mysql4_Product_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amconf/product_attribute');
    }
}