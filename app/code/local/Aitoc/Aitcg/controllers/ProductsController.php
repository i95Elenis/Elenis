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
class Aitoc_Aitcg_ProductsController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction() 
    {
        if( $this->getRequest()->getParam('id') > 0 )
        {
            try {
                $order_item = Mage::getModel('sales/order_item')->load($this->getRequest()->getParam('id'));

                //$arrayImage = Mage::helper('aitcg/data')->createMergedImage($order_item);
                $arrayImage = Mage::getModel('aitcg/image')->createMergedImage($order_item);
                $modelProduct = Mage::getModel('aitcg/product');
                $duplicateProduct = $modelProduct->duplicateProduct($order_item->getProductId());
                $duplicateProduct = $modelProduct->deleteAitcgOptions($duplicateProduct);
                $duplicateProduct = $modelProduct->deleteImages($duplicateProduct);

                // if use $duplicateProduct, string ->save() not work, and images not add
                $product = Mage::getModel('catalog/product')->load($duplicateProduct->getId());
                $product = $modelProduct->addNewImages($product, $arrayImage);
                $product->save();/**/

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully created'));
                
                Mage::app()->getResponse()->setRedirect(Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getId())));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::app()->getResponse()->setRedirect(Mage::getModel('adminhtml/url')->getUrl('adminhtml/sales_order/view', array('order_id' => $this->getRequest()->getParam('id'))));
                
            }
        }
        //$this->_redirect('*/*/');
    }
}