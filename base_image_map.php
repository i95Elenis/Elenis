<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();

//$productId = 5034;
//load the product
//$product = Mage::getModel('catalog/product')->load($productId);
//get all images
$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');

$i = 0;
foreach ($products as $productdata) {
    $product = Mage::getModel('catalog/product')->load($productdata->getData('entity_id'));
   echo $i."=".$productdata->getData('entity_id')."=".$productdata->getData('sku')."<br/>";
    // if ($product->getSku() == '1129') {
  //  echo $i . "=" . $product->getSku() . "=" . $product->getId() . "<br/>";
    $mediaGallery = $product->getMediaGallery();
   // echo "<pre>";print_r($mediaGallery);exit;
//if there are images
   
        if (isset($mediaGallery['images'])) {
            //loop through the images
            foreach ($mediaGallery['images'] as $image) {
                // echo "<pre>";print_r($image);exit;
                //set the first image as the base image
                Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('image' => $image['file'], 'small_image' => $image['file'], 'thumbnail' => $image['file']), 0);
                $product->save();
                //stop
                break;
            }
        }
        $i++;
    }
//}
?>