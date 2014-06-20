<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_RewriterController extends Aitoc_Aitsys_Abstract_Adminhtml_Controller
{
    public function preDispatch()
    {
        $result = parent::preDispatch();

        if (true === $this->_getSession()->getData('aitsys_rewriter_require_rebuild'))
        {
            $this->_getSession()->unsetData('aitsys_rewriter_require_rebuild');
            Mage::getModel('aitsys/rewriter')->prepare();
        }

        return $result;
    }
    
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_setTitle(array(
                Mage::helper('aitsys')->__('Rewrites Manager'),
                Mage::helper('aitsys')->__('Aitoc Modules Manager')
            ));
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        $currentExtension = Mage::app()->getRequest()->getParam('extension');
        
        $classOrder = Mage::app()->getRequest()->getPost('rewrites');
        $excludeClasses = Mage::app()->getRequest()->getPost('exclude_classes');
        if (!$classOrder) {
            $classOrder = array();
        }
        
        $failed = false;
        foreach ($classOrder as $baseClass => $rewriteClasses)
        {
            $usedOrders = array();
            foreach ($rewriteClasses as $class => $order)
            {
                if (!is_numeric($order))
                {
                    $failed = true;
                    $this->_getSession()->addError($this->_aithelper()->__('Please make sure the numeric values only are entered.'));
                    break(2);
                }
                if (in_array($order, $usedOrders))
                {
                    $failed = true;
                    $this->_getSession()->addError($this->_aithelper()->__('Please make sure the order numbers are not duplicated.'));
                    break(2);
                }
                $usedOrders[] = $order;
            }
        }
        
        if (!$failed)
        {
            if (!$currentExtension)
            {
                $this->_aithelper('Rewriter')->saveOrderConfig($classOrder);
            } else 
            {
                $this->_aithelper('Rewriter')->mergeOrderConfig($classOrder);
            }

            /* Save excluded base classes */
            $this->_aithelper('Rewriter')->saveExcludeClassesConfig($excludeClasses);
            $this->_getSession()->setData('aitsys_rewriter_require_rebuild', true);
            $this->_getSession()->addSuccess($this->_aithelper()->__('Rewrites changes saved successfully.'));
        }
        $this->_redirect('*/*', array('_current'=>true));
    }
    
    public function resetAction()
    {
        $this->_aithelper('Rewriter')->removeOrderConfig();
        $this->_getSession()->setData('aitsys_rewriter_require_rebuild', true);
        $this->_getSession()->addSuccess($this->_aithelper()->__('Rewrites order resetted successfully.'));
        $this->_redirect('*/*');
    }
}