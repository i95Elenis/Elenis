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
class Aitoc_Aitcg_Model_Resource_Template extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('catalog/product_entity_varchar', 'value_id');
    }
    
    public function getValueTable($entityName, $valueType)
    {
        if(version_compare(Mage::getVersion(), '1.4.0.0', '>='))
        {
            return parent::getValueTable($entityName, $valueType);
        }
        
        return $this->getTable($entityName) . '_' . $valueType;
    }

    protected function _getTemplateImageDB($product_id, $template_id)
    {
        $select = $this->_getReadAdapter()->select()
            //->from( $this->getValueTable('catalog/product', 'varchar') )
            ->from( $this->getValueTable('catalog/product', 'media_gallery') )
            ->where('entity_id=?', $product_id)
            //->where('attribute_id=?', $template_id);
            ->where('value_id=?', $template_id);
        $mediaImage = $this->_getReadAdapter()->fetchRow($select);
        if(!is_array($mediaImage) || sizeof($mediaImage)==0 || $mediaImage['value']=="") {
            return false;
        }

        return $mediaImage;
    }
    
    public function getTemplateImage( $product_id, $template_id )
    {
        $mediaImage = $this->_getTemplateImageDB($product_id, $template_id);
        if(!$mediaImage)
        {
            return false;
        }
        
        $img = Mage::helper('aitcg/image')->loadLite( $mediaImage["value"] );
        /** @var $img Aitoc_Aitcg_Helper_Image */
        
        $default_size_x = $default_size_y = 200;
        
        $mediaImage['thumbnail_url'] = $img->keepFrame(false)
            ->constrainOnly(true)
            ->resize($default_size_x,$default_size_y)
            ->__toString();
        $mediaImage['default_size'] = $img->getOriginalSize();
        $mediaImage['thumbnail_file'] = $img->getNewFile();
        
        $mediaImage['thumbnail_size'] = Mage::helper('aitcg/options')
            ->calculateThumbnailDimension( $default_size_x, $default_size_y, $mediaImage['default_size'][0], $mediaImage['default_size'][1] );
        
        $mediaImage['full_image'] = Mage::getSingleton('catalog/product_media_config')
            ->getMediaUrl($mediaImage['value']);
        return $mediaImage;
    }

}