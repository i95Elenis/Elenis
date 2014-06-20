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
class Aitoc_Aitcg_TipsController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
       $this->loadLayout()
            ->_setActiveMenu('catalog/aitcg/tips')
               
            ->_addBreadcrumb(Mage::helper('aitcg')->__('Aitoc Custom Product Preview Font Color'), Mage::helper('aitcg')->__('Aitoc Custom Product Preview Font Color'));
        return $this;
    }   

    public function indexAction() 
    {
        //$this->loadLayout();
        $this->_initAction();       
        
        //$this->_addContent($this->getLayout()->getBlock());
        $this->renderLayout();
    }
    

}