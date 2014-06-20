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
class Aitoc_Aitcg_Model_Image_Temp extends Aitoc_Aitcg_Model_Image
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitcg/image_temp');
    }

    public function deleteData() {
        @unlink(Mage::getBaseDir('media').DS.'custom_product_preview'.DS.'quote'.DS.$this->getData('file_name'));
        return $this->delete();
    }
        
}