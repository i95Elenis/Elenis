<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

try {

    $file = 'testtwo.csv';
    $csv = new Varien_File_Csv();
    $data = $csv->getData($file);
    for ($i = 1; $i < count($data); $i++) {

       // echo "kk1".$data[$i][0];
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $data[$i][0]); 
        //$product = Mage::getModel('catalog/product')->load($data[$i][0], 'sku');
       // echo "kk".$product->getId();exit;
        
        
        if ($product->getId()) {
            
            $query = "update mage_catalog_product_entity_media_gallery_value set cgimage=1 WHERE value_id  IN(SELECT value_id FROM mage_catalog_product_entity_media_gallery WHERE entity_id=".(int)$product->getId()." ORDER BY POSITION) LIMIT 1";
            $writeConnection->query($query);
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
