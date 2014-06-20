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
class Aitoc_Aitcg_Block_Sharedimage_Content extends Aitoc_Aitcg_Block_Sharedimage_Abstract
    {
        protected function _prepareLayout()
        {
//            $id = (string) $this->getRequest()->getParam('id');
//
//            $sharedImgModel = Mage::getModel('aitcg/sharedimage');
//            $sharedImgModel->load($id);
//
//            $sharedImgFullSizeUrl = $sharedImgModel->getUrlFullSizeSharedImg();
//            $sharedImgSmallSizeUrl = $sharedImgModel->getUrlSmallSizeSharedImg();
//
//            $productId = $sharedImgModel->getProductId();
//            $product = Mage::getModel('catalog/product')->load($productId);
//            $productUrl = $product->getProductUrl();
//            $productName = $product->getName();
//            $currentUrl = $this->helper('aitcg')->getSharedImgUrl($id);

//            $this->assign('productUrl', $productUrl);
//            $this->assign('sharedImgFullSizeUrl', $sharedImgFullSizeUrl);
//            $this->assign('sharedImgSmallSizeUrl', $sharedImgSmallSizeUrl);
//            $this->assign('productName', $productName);
//            $this->assign('currentUrl', $currentUrl);
//            $this->assign('sharedImgId', $id);
//            $this->assign('product', $product);
            
            parent::_prepareLayout();
        }       
}