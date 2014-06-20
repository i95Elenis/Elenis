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
class Aitoc_Aitcg_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Type_Cgfile extends
    Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    public function _construct()
    {
        parent::_construct();
	}
    
    protected function  _beforeToHtml() {
        /* {#AITOC_COMMENT_END#}  
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitcg')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        if($ruler->checkRuleAdd( $this->getProduct() )) {
        {#AITOC_COMMENT_START#} */
            $this->setTemplate('aitcg/catalog/product/edit/options/type/cgfile.phtml');
        /* {#AITOC_COMMENT_END#}  
        } else {
            $this->setTemplate('aitcg/catalog/product/edit/options/type/cgfile_closed.phtml');
        }
        {#AITOC_COMMENT_START#} */
        parent::_beforeToHtml();
    }
    
    
	protected function _prepareLayout()
    {
        $this->setChild('aitcg_printarea_window',
            $this->getLayout()->createBlock('adminhtml/template')
            	->setTemplate('aitcg/window/printarea.phtml')
        );
        /*$this->setChild('aitcg_optconfig_template',
            $this->getLayout()->createBlock('aitcg/adminhtml_catalog_product_edit_tab_options_type_config_template')
        );*/
        
        $this->_prepareAllowTextDistortionSelect();
        $this->_prepareAllowPredefinedColorsSelect();
        $this->_prepareColorSetSelect();
        
        return parent::_prepareLayout();
    }
    
    public function getSelectImageOptions() {
        $images = $this->getProduct()->getAitcgMediaGalleryImages();
        if(!$images) {
            return '';
        }
        $html = '';
        foreach ($images as $attribute) 
        {
            if (!file_exists($attribute->getPath())) {
                continue;
            }
            $html .= '\'<option value="'.$attribute->getValueId().'">'.$attribute->getFile().'</option>\'+'."\n";
        }
        return $html;
    }
    
    public function getSelectImageIds() {
        $mediaAttributes = $this->getProduct()->getAitcgMediaGalleryImages();
        $return = array();
        if($mediaAttributes) {
            foreach ($mediaAttributes as $attribute) 
            {
                $value = array(
                    'attribute_id' => $attribute->getValueId(),
                    'frontend_label' => $attribute->getFile(),
                );
                $image = Mage::helper('aitcg/image')->loadLite( $value['frontend_label'] );
                /** @var $image Aitoc_Aitcg_Helper_Image */
                if (!file_exists($image->getBaseDir().$value['frontend_label'])) {
                    continue;
                }

                $default_size_x = $default_size_y = 165;
                $image->keepFrame(false)->constrainOnly(true)->resize($default_size_x,$default_size_y);
                $value['thumbnail_url'] = $image->__toString();
                $value['img_size'] = $image->getOriginalSize();
                $value['thumbnail_size'] = Mage::helper('aitcg/options')
                    ->calculateThumbnailDimension( $default_size_x, $default_size_y, $value['img_size'][0], $value['img_size'][1] );
                $value['full_image'] = Mage::getSingleton('catalog/product_media_config')->getMediaUrl( $value['frontend_label'] );
                $return[ $attribute->getValueId() ] = $value;            
            }
        }
        $return = new Varien_Object($return);
        return $return;        
    }
    
    protected function _prepareAllowTextDistortionSelect()
    {
        $this->setChild('aitcg_option_allow_text_distortion',
            $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => 'aitcg_option_allow_text_distortion_{{option_id}}',
                    'class' => 'select'
                ))
        );

        $this->getChild('aitcg_option_allow_text_distortion')->setName('product[options][{{option_id}}][allow_text_distortion]')
            ->setOptions(Mage::getSingleton('aitcg/system_config_source_product_options_boolean')
            ->toOptionArray());
    }
    
    public function getAllowTextDistortionSelectHtml()
    {
        return $this->getChildHtml('aitcg_option_allow_text_distortion');
    }
    
    protected function _prepareAllowPredefinedColorsSelect()
    {
        $this->setChild('aitcg_option_allow_predefined_colors',
            $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => 'aitcg_option_allow_predefined_colors_{{option_id}}',
                    'class' => 'select'
                ))
        );

        $this->getChild('aitcg_option_allow_predefined_colors')->setName('product[options][{{option_id}}][allow_predefined_colors]')
            ->setOptions(Mage::getSingleton('aitcg/system_config_source_product_options_boolean')
            ->toOptionArray());
    }
    
    public function getAllowPredefinedColorsSelectHtml()
    {
        return $this->getChildHtml('aitcg_option_allow_predefined_colors');
    }
    
    protected function _prepareColorSetSelect()
    {
        $this->setChild('aitcg_option_color_set_id',
            $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => 'aitcg_option_color_set_id_{{option_id}}',
                    'class' => 'select'
                ))
        );

        $this->getChild('aitcg_option_color_set_id')->setName('product[options][{{option_id}}][color_set_id]')
            ->setOptions(Mage::getSingleton('aitcg/system_config_source_product_options_text_color_set')
            ->toOptionArray());
    }
    
    public function getColorSetSelectHtml()
    {
        return $this->getChildHtml('aitcg_option_color_set_id');
    }
    
}