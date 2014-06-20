<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
//$resource = Mage::getSingleton('core/resource');
//$writeConnection = $resource->getConnection('core_write');

try {

    //$file = 'test-exclude-image.csv';
    $file = 'gift_packaging_photo-2721.csv';
    $csv = new Varien_File_Csv();
    $data = $csv->getData($file);
    for ($i = 1; $i < count($data); $i++) {

        //echo "<pre>"; print_r($data[$i]);
        $productData = Mage::getModel('catalog/product')->loadByAttribute('sku', $data[$i][0]);
        $product = Mage::getModel('catalog/product')->load($productData->getData('entity_id'));
        //echo $data[$i][1];exit;
        //echo Mage :: getBaseDir( 'media' ) . DS . 'import' .$data[$i][1];exit;
        $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
        $product->addImageToMediaGallery(Mage :: getBaseDir('media') . DS . 'import' . $data[$i][1], null, false, false);
        
    }
} catch (Exception $e) {
    echo $e->getMessage();
}



//$product->addImageToMediaGallery ($fullImagePath, null, false, false); 
?>