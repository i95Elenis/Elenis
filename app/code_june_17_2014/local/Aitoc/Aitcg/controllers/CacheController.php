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
class Aitoc_Aitcg_CacheController extends Mage_Adminhtml_Controller_Action
{
    public function cleanAction() {
        try {
            $days =  Mage::app()->getRequest()->get('daysamount');
            $days = max(1, $days);
            
            $model = Mage::getModel('aitcg/observer');
            $model->deleteImages($days);
            $this->_getSession()->addSuccess( Mage::helper('aitcg')->__('Custom Product Preview Images has been flushed.') );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/cache/index');
    }
    
    /**
     * Check if cache management is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }
                            
}