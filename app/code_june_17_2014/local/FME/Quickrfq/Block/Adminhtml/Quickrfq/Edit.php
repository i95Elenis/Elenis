<?php

 /**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Quickrfq_Block_Adminhtml_Quickrfq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'quickrfq';
        $this->_controller = 'adminhtml_quickrfq';
        
        $this->_updateButton('save', 'label', Mage::helper('quickrfq')->__('Save Record'));
        $this->_updateButton('delete', 'label', Mage::helper('quickrfq')->__('Delete Record'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('quickrfq_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'quickrfq_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'quickrfq_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('quickrfq_data') && Mage::registry('quickrfq_data')->getId() ) {
            return Mage::helper('quickrfq')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('quickrfq_data')->getTitle()));
        } else {
            return Mage::helper('quickrfq')->__('Add New RFQ');
        }
    }
}