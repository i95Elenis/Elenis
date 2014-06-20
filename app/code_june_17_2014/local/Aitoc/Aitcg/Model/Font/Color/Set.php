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
class Aitoc_Aitcg_Model_Font_Color_Set extends Mage_Core_Model_Abstract
{   
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitcg/font_color_set');
    }
    
    public function hasId($colorsetId)
    {
        $ids = $this->getCollection()->getAllIds();
        foreach($ids as $id)
        {
            if($id == $colorsetId )
            {
                return true;
            }    
        }    
        return false;        
    }
    
    public function setStatus($status)
    {
        $itemId = $this->getId();
        if($itemId == Aitoc_Aitcg_Helper_Font_Color_Set::XPATH_CONFIG_AITCG_FONT_COLOR_SET_DFLT)
        {
            $status = 1;
        }  
        parent::setStatus($status);
    }
    
    public function delete()
    {
        $itemId = $this->getId();
        if($itemId == Aitoc_Aitcg_Helper_Font_Color_Set::XPATH_CONFIG_AITCG_FONT_COLOR_SET_DFLT)
        {
            return;
        }
        parent::delete();
    }
    
}