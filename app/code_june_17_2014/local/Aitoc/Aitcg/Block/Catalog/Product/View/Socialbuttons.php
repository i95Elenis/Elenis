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
class Aitoc_Aitcg_Block_Catalog_Product_View_Socialbuttons extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        if(!Mage::getStoreConfig('catalog/aitcg/aitcg_use_social_networks_sharing'))
        {
            return '';
        }
        
        return parent::_toHtml();
    }
}