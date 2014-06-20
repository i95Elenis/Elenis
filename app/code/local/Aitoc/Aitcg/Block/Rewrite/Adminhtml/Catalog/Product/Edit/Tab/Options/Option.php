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
class Aitoc_Aitcg_Block_Rewrite_Adminhtml_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitcommonfiles/design--adminhtml--default--default--template--catalog--product--edit--options--option.phtml');
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('aitcg_option_type',
            $this->getLayout()->createBlock(
                'aitcg/adminhtml_catalog_product_edit_tab_options_type_cgfile'
            )->setProduct( $this->getProduct() )
        );
        return parent::_prepareLayout();
    }

    public function getTemplatesHtml()
    {
        $templates = parent::getTemplatesHtml();
        return $templates . "\n" . $this->getChildHtml('aitcg_option_type');
    }

    public function getOptionValues()
    {
        $values = parent::getOptionValues();
        $optionArr = array_reverse($this->getProduct()->getOptions(), true);
        if(!$optionArr) {
            return $values;
        }
        foreach ($values as &$value) {
            $option = $optionArr [$value->getId()];
            /** @var $option Mage_Catalog_Model_Product_Option */
            if($option && Mage::helper('aitcg/options')->checkAitOption( $option ) ) 
            {
                $value['image_template_id'] = $option->getImageTemplateId();
                $value['area_size_x'] = $option->getAreaSizeX();
                $value['area_size_y'] = $option->getAreaSizeY();                        
                $value['area_offset_x'] = $option->getAreaOffsetX();
                $value['area_offset_y'] = $option->getAreaOffsetY();
                $value['use_text'] = $option->getUseText();                
                $value['use_user_image'] = $option->getUseUserImage();  
                $value['use_predefined_image'] = $option->getUsePredefinedImage();  
                $value['predefined_cats'] = $option->getPredefinedCats();  
                $value['use_masks'] = $option->getUseMasks();  
                $value['masks_cat_id'] = $option->getMasksCatId();  
                $value['mask_location'] = $option->getMaskLocation();  
                $value['allow_colorpick'] = $option->getAllowColorpick();  
                $value['text_length'] = $option->getTextLength();
                $value['allow_text_distortion'] = $option->getAllowTextDistortion();
                $value['allow_predefined_colors'] = $option->getAllowPredefinedColors();
                $value['color_set_id'] = $this->_getColorSetId($option);
            }
        }
        $this->_values = $values;
        
        return $this->_values;
    }
    
    protected function _getColorSetId($option)
    {
        $currentId = $option->getColorSetId();
        if(Mage::getModel('aitcg/font_color_set')->hasId($currentId))
        {
            return $currentId;
        }
        return Aitoc_Aitcg_Helper_Font_Color_Set::XPATH_CONFIG_AITCG_FONT_COLOR_SET_DFLT ;          
    }
}