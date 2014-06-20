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
class Aitoc_Aitcg_Block_Adminhtml_Mask_Image_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'imgid';
        $this->_blockGroup = 'aitcg';
        $this->_controller = 'adminhtml_mask_image';
 
        $this->_updateButton('save', 'label', Mage::helper('aitcg')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('aitcg')->__('Delete Item'));
       
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('image_data') && Mage::registry('image_data')->getId() ) {
            return Mage::helper('aitcg')->__('Category: ').Mage::getModel('aitcg/mask_category')->load(Mage::app()->getRequest()->getParam('id'))->getName().' - '.Mage::helper('aitcg')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('image_data')->getName()));
        } else {
            return Mage::helper('aitcg')->__('Category: ').Mage::getModel('aitcg/mask_category')->load(Mage::app()->getRequest()->getParam('id'))->getName().' - '.Mage::helper('aitcg')->__('Add Item');
        }
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array('id'=>Mage::app()->getRequest()->getParam('id')));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('id'=>Mage::app()->getRequest()->getParam('id'), 'imgid' => $this->getRequest()->getParam('imgid')));
    }    
}