<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Abstract_Adminhtml_Controller extends Mage_Adminhtml_Controller_Action
implements Aitoc_Aitsys_Abstract_Model_Interface
{
    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function tool()
    {
        return Aitoc_Aitsys_Abstract_Service::get();
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Helper
     */
    protected function _aithelper($type = 'Data')
    {
        return $this->tool()->getHelper($type);
    }
    
    /**
     * @param string|array $title
     * @return Aitoc_Aitsys_Abstract_Adminhtml_Controller
     */
    protected function _setTitle($title)
    {
        $title = (array)$title;
        $titleBlock = $this->getLayout()->getBlock('head');
        $defaultTitle = $titleBlock->getTitle();
        if($defaultTitle)
        {
            $title[] = $defaultTitle;
        }
        $titleBlock->setTitle(implode(' / ', $title));
        return $this;
    }
    
    public function preDispatch()
    {
        $result = parent::preDispatch();
        $this->tool()->setInteractiveSession($this->_getSession());
        if ($this->tool()->platform()->isBlocked() && 'error' != $this->getRequest()->getActionName())
        {
            $this->_forward('error', 'index');
        }
        return $result;
    }
    
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/aitsys');
    }
}