<?php
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('deliveries_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('estimateddeliverydate')->__('Deliveries Management'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('estimateddeliverydate')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit_tab_form')->initForm()->toHtml(),
            'active'    => $this->getRequest()->getParam('id') ? false : true
        ));
        
        $this->addTab('product', array(
            'label'     => Mage::helper('estimateddeliverydate')->__('Products'),
            'content'   => $this->getLayout()->createBlock('estimateddeliverydate/adminhtml_deliveries_edit_tab_products')->toHtml()
        ));
        
        $this->_updateActiveTab();
        Varien_Profiler::stop('estimateddeliverydate/tabs');
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
