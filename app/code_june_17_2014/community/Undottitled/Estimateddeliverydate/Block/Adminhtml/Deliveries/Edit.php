<?php
 
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
 
        $this->_objectId = 'id';
        $this->_blockGroup = 'estimateddeliverydate';
        $this->_controller = 'adminhtml_deliveries';
        $this->_mode = 'edit';
 
        $this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                  'onclick' => 'saveAndContinueEdit()',
                  'class' => 'save',
        ), -100);
        $this->_updateButton('save', 'label', Mage::helper('estimateddeliverydate')->__('Save Delivery'));
 
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
 
    public function getHeaderText()
    {
        if (Mage::registry('deliveries_data') && Mage::registry('deliveries_data')->getId())
        {
            return Mage::helper('estimateddeliverydate')->__('Edit Delivery ', $this->htmlEscape(Mage::registry('deliveries_data')->getName()));
        } else {
            return Mage::helper('estimateddeliverydate')->__('New Delivery');
        }
    }
 
}