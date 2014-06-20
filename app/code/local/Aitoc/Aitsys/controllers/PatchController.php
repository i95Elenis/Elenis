<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_PatchController extends Aitoc_Aitsys_Abstract_Adminhtml_Controller
{
    public function instructionAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_setTitle(array(
                Mage::helper('aitsys')->__('Aitoc Manual Patch Instructions'),
                Mage::helper('aitsys')->__('Aitoc Modules Manager')
            ));
        $this->renderLayout();
    }
    
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_setTitle(array(
                Mage::helper('aitsys')->__('Customized Templates'),
                Mage::helper('aitsys')->__('Aitoc Modules Manager')
            ));
        $this->renderLayout();
    }
}