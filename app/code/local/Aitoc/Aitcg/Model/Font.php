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
class Aitoc_Aitcg_Model_Font extends Mage_Core_Model_Abstract
{   
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitcg/font');
    }
    
    public function getFontsPath()
    {
        return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'fonts' . DS;
    }
    
    public function setFilename($filename)
    {
        if($this->getFilename() && $this->getFilename() !=  $filename) {
            $fullPath = $this->getFontsPath() . $this->getFilename();
            unlink($fullPath);                    
        }                    
        $this->setData('filename', $filename);
    }
}