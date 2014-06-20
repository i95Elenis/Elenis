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
abstract class Aitoc_Aitcg_Block_Checkout_Cart_Item_Option_Abstract extends Mage_Core_Block_Template 
{
    protected $_isSecureConnection = false;
    
    public function _construct()
    {
        parent::_construct();
        if(Mage::app()->getStore()->isCurrentlySecure())
        {
            $this->_isSecureConnection = true;
        }    
    }
    
    public function getImgData()
    {
        $sImgData = parent::getImgData();
        $sImgData = Mage::helper('aitcg')->getSecureUnsecureUrl($sImgData);
        return $sImgData;
    }
    
    public function getSaveSvgUrl()
    {
        if($this->_isSecureConnection)
        {
            return Mage::getUrl('aitcg/index/svg',array('_secure'=>true));
        }    
        return Mage::getUrl('aitcg/index/svg');
    }   

    public function getReservedForSocialWidgetsImgId()
    {
        $reservedId = 0;

        $imgData = Mage::helper('core')->jsonDecode($this->getImgData());
        
        foreach($imgData as $key => $img)
        {
            foreach($img as $k => $val)
            {
                if(($k == 'social_widgets_reserved_img_id') && $val != 0)
                {
                    $reservedId = $val;
                    return $reservedId;
                }
            }
        }

        return false;
    }

    public function getSharedImgId($rand)
    {
        $id = $this->getReservedForSocialWidgetsImgId();
        if($id)
            return $id;

        return Mage::helper('aitcg')->getSharedImgId($rand);
    }

    public function canEmailToFriend()
    {
        return Mage::helper('sendfriend')->isEnabled();
    }     
}