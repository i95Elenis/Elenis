<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
$resource = Mage::getSingleton('core/resource');
$writeConnection = $resource->getConnection('core_write');

try {

    //$file = 'test-exclude-image.csv';
    $file = 'main-exclude-images.csv';
    $csv = new Varien_File_Csv();
    $data = $csv->getData($file);
    for ($i = 1; $i < count($data); $i++) {

        //echo "<pre>"; print_r($data[$i]);
        $productData = Mage::getModel('catalog/product')->loadByAttribute('sku', $data[$i][0]);
        $product = Mage::getModel('catalog/product')->load($productData->getData('entity_id'));
        
        //echo "<pre>";print_r($product->getData());
        $mediaGallery = $product->getMediaGallery();
        //echo "<pre>";      print_r($mediaGallery);
        if (isset($mediaGallery['images'])) {
            foreach ($mediaGallery['images'] as $image) {
               // echo "inner loop=" . "<pre>";print_r($image);

                $imageSplit = explode("/", $image['file']);
                //echo "<pre>"; print_r($imageSplit);
                if ($imageSplit[3] == $data[$i][1]) {
                   // echo "found";
                    if ($product->getId()) {

                        $query = "update mage_catalog_product_entity_media_gallery_value set disabled=1 WHERE value_id=" . (int) $image['value_id'];
                       // echo "<br/>" . $query;
                        echo $product->getId()."==".$product->getSku()."<br/>";
                            $writeConnection->query($query);
                    }
                }
            }
        }
       
    }
} catch (Exception $e) {
    echo $e->getMessage();
}



//$product->addImageToMediaGallery ($fullImagePath, null, false, false); 

?>