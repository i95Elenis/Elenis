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
class Aitoc_Aitcg_Model_Product extends Mage_Core_Model_Abstract
{
   
  
    public function addNewImages($product, $arrayImage)
    {
        $importDir = '';
        $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
        $arrayImage = array_reverse($arrayImage);
        foreach($arrayImage as $mediaArray) 
        {
            foreach($mediaArray as $imageType => $fileName) 
            {
                    $filePath = $importDir.$fileName;
                    if ( file_exists($filePath) ) 
                    {
                            try 
                            {
                                    $product->addImageToMediaGallery($filePath, $imageType, false);
                            }
                            catch (Exception $e) 
                            {
                                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            }
                    } 
                  /*  else 
                    {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }*/
            }
        }
       // $product->save();
        return $product;

    }
    public function deleteImages($product)
    {
        $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
        $items = $mediaApi->items($product->getId());
        foreach($items as $item) 
        {
                $mediaApi->remove($product->getId(), $item['file']);
        }
        return $product;

    }
    public function deleteAitcgOptions($product)
    {
        foreach($product->getProductOptionsCollection() as $option)
        {
                if($option->getType() == 'aitcustomer_image')
                {
                        
                    $option->delete();
                }
        }
        return $product;

    }
    public function duplicateProduct($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $duplicate = $product->duplicate();
        return $duplicate;
    }

}