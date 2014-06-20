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
class Aitoc_Aitcg_Block_Sharedimage_Abstract extends Mage_Core_Block_Template
    {
        protected $sharedImgModel = null;
        protected $product = null;

        protected function _getSharedImgModel()
        {
            if ($this->sharedImgModel == null)
            {
                $id = (string) $this->getRequest()->getParam('id');

                $sharedImgModel = Mage::getModel('aitcg/sharedimage');
                $sharedImgModel->load($id);
                $this->sharedImgModel = $sharedImgModel;
            }
            return $this->sharedImgModel;
        }
        
        
        protected function _getProductModel()
        {
            if ($this->product == null)
            {
                $sharedImgModel = $this->_getSharedImgModel();
                $productId = $sharedImgModel->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $this->product = $product;
            }
            return $this->product;
        }
        
        
        protected function _getProductName()
        {
            $product = $this->_getProductModel();
            return $product->getName();
        }
        
        protected function _getCurrentUrl()
        {
            $id = (string) $this->getRequest()->getParam('id');
            $currentUrl = $this->helper('aitcg')->getSharedImgUrl($id);
            return $currentUrl;
        }
        
        protected function _getSharedImgId()
        {
            return (string) $this->getRequest()->getParam('id');
        }
        
        
        protected function _prepareLayout()
        {
            parent::_prepareLayout();
        } 
      
}