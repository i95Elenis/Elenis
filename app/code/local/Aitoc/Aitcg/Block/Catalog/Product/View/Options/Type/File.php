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
class Aitoc_Aitcg_Block_Catalog_Product_View_Options_Type_File extends Mage_Catalog_Block_Product_View_Options_Type_File 
{
    
    protected function _beforeToHtml()
    {
        $model = 'image_temp';
        $option = $this->getOption();
        $info = $this->getProduct()->getPreconfiguredValues();
        
        if($info) {
            $info = $info->getData('options/' . $option->getId());
        }

        $template_id = 0;
        if(isset($info) && $info['template_id'] > 0 ) {
            $template_id = $info['template_id'];
            $model = 'image';
        } else {
            $sessionData = Mage::getSingleton('aitcg/session')
                ->getData('options_' . $option->getId() . '_file');
            $template_id = intval($sessionData['template_id']);
            $model = 'image_temp';
        }
        
        $data = $this->_getDataTemplate($model, $option, $template_id);
       
        $this->setImage($data["image"]);
        $this->setPreview($data['preview']);
        
        return parent::_beforeToHtml();
    }
    
    protected function _getDataTemplate($model, $option, $template_id)
    {
        $data = array('image' => false, 'preview' => false); 
        $model = Mage::getModel('aitcg/'.$model);
        if($template_id > 0) {
            $model->load( $template_id );
            if($model->hasData()) {
                $data['preview'] = new Varien_Object($model->getFullData( ));
                if($data['preview']['id'] == 0) {
                    $data['preview'] = false;
                }
            }
        }
                    
        $data["image"] = $model->getMediaImage( $option->getProductId(), $option->getImageTemplateId() ); 
        return $data;
        
    }
    
    public function getColorset()
    {
        $id = $this->getOption()->getColorSetId();
        $colorsetModel = Mage::getModel('aitcg/font_color_set');
        $hasId = $colorsetModel->hasId($id);
        if($hasId)
        {
            $colorsetModel  = $colorsetModel->load($id);
            $status = $colorsetModel->getStatus();
            if($status !== '0')
            {
                return $colorsetModel;
            }
        }
        return $colorsetModel->load(Aitoc_Aitcg_Helper_Font_Color_Set::XPATH_CONFIG_AITCG_FONT_COLOR_SET_DFLT);
    }
    
    public function getAllowPredefinedColors()
    {
        $value = $this->getOption()->getAllowPredefinedColors();
        if($value == null)
        {
            return Mage::getStoreConfig('catalog/aitcg/aitcg_font_color_predefine'); 
        }
        return $value;
    }
    
    public function getTextObjAspectRatio()
    {
        $optValue = $this->getOption()->getAllowTextDistortion();
        if($optValue == null)
        {
            $optValue = Mage::getStoreConfig('catalog/aitcg/aitcg_allow_text_distortion'); 
        }
        if($optValue == '0')
        {
            return 'xMidYMid';
        }    
        return 'none';
    }

    //for social networks buttons functionality
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }
    public function getSavePdfUrl()
    {
        if (Mage::app()->getStore()->getConfig('catalog/aitcg/aitcg_enable_svg_to_pdf') == 1)
        {
            return Mage::getUrl('aitcg/index/pdf');
        }
        return false;
    }
}