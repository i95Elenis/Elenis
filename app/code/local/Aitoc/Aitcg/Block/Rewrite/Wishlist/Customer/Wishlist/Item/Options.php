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
class Aitoc_Aitcg_Block_Rewrite_Wishlist_Customer_Wishlist_Item_Options extends Mage_Wishlist_Block_Customer_Wishlist_Item_Options
{
    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $html = Mage::helper('aitcg')->removeSocialWidgetsFromHtml($html);

        return $html;
    }
}