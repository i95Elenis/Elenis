<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
$products = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect(array('name', 'product_url', 'small_image', 'status'))
       // ->addAttributeToFilter('status', array('eq' => 1))
        ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
        // ->addAttributeToFilter('has_options', array('eq' => 1))
        ->load();
//->addAttributeToFilter('type_id',array('eq'=>'configurable'));
//echo "<pre>";
//print_r($products->getSelect()->__toString());
//exit;
//echo "<pre>";print_r($products->getData());
foreach ($products as $prod) {
    
    //echo "<pre>";print_r($prod->getId());
    $product=Mage::getModel('catalog/product')->load($prod->getId());
   // echo "<pre>";print_r($product->getSku());
    foreach ($product->getOptions() as $opt) {
        $opt->delete();
    }
    $product->save();
   
}
?>