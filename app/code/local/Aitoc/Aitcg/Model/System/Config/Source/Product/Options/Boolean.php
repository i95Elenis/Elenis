<?php
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     AJifvLXz2Jhov40GDpSzkNqfs4dkmEPJtRhHJxJI2y
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcg_Model_System_Config_Source_Product_Options_Boolean
{
    public function toOptionArray()
    {
        return array(
            array('value' => null, 'label' => Mage::helper('adminhtml')->__('Use config')),
            array('value' => '1', 'label' => Mage::helper('adminhtml')->__('Yes')),
            array('value' => '0', 'label' => Mage::helper('adminhtml')->__('No'))
        );
    }
}