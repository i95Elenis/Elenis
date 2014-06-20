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
class Aitoc_Aitcg_Helper_Image extends Mage_Catalog_Helper_Image
{
    protected $baseDir = '';
    
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null) {
        return $this->load($attributeName, $imageFile);
    }
    
    public function loadLite($imageFile, $baseDir = 'catalog/product_media_config') {
        if($baseDir) {
            if($baseDir=='media') {
                $this->setBaseDir(Mage::getBaseDir('media'));
            } else {
                $this->setBaseDir(Mage::getSingleton($baseDir)->getBaseMediaPath());
            }
        }
        return $this->load(Aitoc_Aitcg_Helper_Options::OPTION_TYPE_AITCUSTOMER_IMAGE, $imageFile);
    }
    
    public function load($attributeName, $imageFile)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('aitcg/product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);

        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));

        $this->setImageFile($imageFile);
        return $this;
    }
    
    public function getBaseDir() {
        return $this->_baseDir;
    }

    public function setBaseDir($baseDir) {
        $this->_baseDir = $baseDir;
    }

    public function __toString()
    {
        try {
            if( $this->getImageFile() ) {
                $this->_getModel()->setBaseFile( $this->getImageFile(), $this->getBaseDir() );
            } else {
                $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
            }

            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl();
            } else {
                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if( $this->getWatermark() ) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
    
    public function getNewFile() {
        return $this->_getModel()->getNewFile();
    }
    
    public function getOriginalSize() {
        return $this->_getModel()->calcOriginalSize();
    }

}