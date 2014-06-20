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
class Aitoc_Aitcg_Block_Adminhtml_Image extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
        public function __construct()
        {
            $this->_controller = 'adminhtml_image';
            $this->_blockGroup = 'aitcg';
            $this->_headerText = Mage::helper('aitcg')->__('Category: ').Mage::getModel('aitcg/category')->load(Mage::app()->getRequest()->getParam('id'))->getName();
            $this->_addButtonLabel = Mage::helper('aitcg')->__('Add Item');
            $this->_backButtonLabel = Mage::helper('aitcg')->__('Back');
            $this->_addBackButton();
            
            parent::__construct();
        }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array('id' => Mage::app()->getRequest()->getParam('id')));
    }  
    
    public function getBackUrl()
    {
        return $this->getUrl('*/category/index', array('id' => Mage::app()->getRequest()->getParam('id')));
    } 
        
}