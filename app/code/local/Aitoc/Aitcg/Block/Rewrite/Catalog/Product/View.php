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
class Aitoc_Aitcg_Block_Rewrite_Catalog_Product_View extends Mage_Catalog_Block_Product_View {

    protected function _toHtml() {
        $result = parent::_toHtml();
        $result = preg_replace('|\<form action="([^"]*)" method="post" id="product_addtocart_form"\>|Uis', '<form action="$1" method="post" id="product_addtocart_form" enctype="multipart/form-data">', $result);
        return $result;
    }

    public function displayPersonalizeButton($prodId) {
        $product = Mage::getModel('catalog/product')->load($prodId);
        // echo "<pre>";print_r($product->getOptions());exit;
        //$options = $product->getOptions();

        foreach ($product->getOptions() as $o) {
            $optionType = $o->getType();
            //echo 'Type = ' . $optionType."<br/>";
            $customOptionTypes = Mage::getStoreConfig('elenissec/elenisgrp/custom_options_handler');
            $customOptionTypes = explode(",", $customOptionTypes);
            //echo "<pre>";print_r($customOptionTypes);exit;
            //if ($optionType == 'field') {
            if (in_array($optionType, $customOptionTypes)) {
                return true;
            }
        }
    }

    public function displayConfigurableProductOptions($_product) {
        $optionsArray=array();
        $conf = Mage::getModel('catalog/product_type_configurable')->setProduct($_product);
        $col = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
        foreach ($col as $simple_product) {
            //var_dump($simple_product->getId());
             return $this->displayPersonalizeButton($simple_product->getId());
        }
        
    }

}