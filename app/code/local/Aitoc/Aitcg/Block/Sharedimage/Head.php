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
class Aitoc_Aitcg_Block_Sharedimage_Head extends Aitoc_Aitcg_Block_Sharedimage_Abstract
    {
        protected function _prepareLayout()
        {
            $product = $this->_getProductModel();

            if ($headBlock = $this->getLayout()->getBlock('head')) 
            {
                if ($description = $product->getMetaDescription()) 
                {
                    $headBlock->setDescription( ($description) );
                } 
                else
                {
                    $headBlock->setDescription($product->getDescription());
                }
            }

            
            parent::_prepareLayout();
        } 
      
}