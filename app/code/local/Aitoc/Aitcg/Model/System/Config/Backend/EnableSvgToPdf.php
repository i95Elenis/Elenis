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
class Aitoc_Aitcg_Model_System_Config_Backend_EnableSvgToPdf extends Mage_Core_Model_Config_Data
{
    public function _beforeSave() {
        parent::_beforeSave();
        if ($this->getValue() == 1)
        {
            exec("convert -version", $out, $rcode); //Try to get ImageMagick "convert" program version number.
            //echo "Version return code is $rcode <br>"; //Print the return code: 0 if OK, nonzero if error.
            if (!($rcode === 0))
                throw Mage::exception('Mage_Core', Mage::helper('aitcg')->__('Requires ImageMagick to be installed at your host and allowed php exec command;  check http://www.imagemagick.org/script/formats.php for possible format conversions'));

            // return $this->_getPromoBlock();

        }
        
    }
    
}