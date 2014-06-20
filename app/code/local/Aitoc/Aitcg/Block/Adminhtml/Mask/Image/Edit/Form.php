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
class Aitoc_Aitcg_Block_Adminhtml_Mask_Image_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/save', array('id' => Mage::app()->getRequest()->getParam('id'), 'imgid' => $this->getRequest()->getParam('imgid'))),
                                        'method' => 'post',
                                        'enctype' => 'multipart/form-data',
                                     )
        );
        
        $fieldset = $form->addFieldset('image_form', array('legend'=>Mage::helper('aitcg')->__('Item information')));
       
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('aitcg')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name'
        ));
        
        $fieldset->addField('filename', 'file', array(
                  'label'     => Mage::helper('aitcg')->__('Image file'),
                  'required'  => false,
                  'name'      => 'filename'
        ));    

        $fieldset->addField('resize', 'select', array(
                  'label'     => Mage::helper('aitcg')->__('Resize type'),
                  'required'  => false,
                  'name'      => 'resize',
                  'values'    => array('0'=>'do not save proportions', '1'=>'save proportions, outer area non-transparent')
        ));    
        $form->setUseContainer(true);
        $this->setForm($form);
        
        if ( Mage::registry('image_data') ) {
            $form->setValues(Mage::registry('image_data')->getData());
        }

        
        return parent::_prepareForm();
    }
}