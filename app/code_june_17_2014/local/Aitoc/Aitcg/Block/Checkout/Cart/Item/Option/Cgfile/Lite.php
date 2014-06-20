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
class Aitoc_Aitcg_Block_Checkout_Cart_Item_Option_Cgfile_Lite extends Aitoc_Aitcg_Block_Checkout_Cart_Item_Option_Cgfile 
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('aitcg/checkout/cart/item/option/cgfile_lite.phtml');
    }
    
    
    public function getSavePdfUrl()
    {
        if (Mage::app()->getStore()->getConfig('catalog/aitcg/aitcg_enable_svg_to_pdf') == 1)
        {
            return Mage::getUrl('aitcg/index/pdf');
        }
        return false;
    }

}