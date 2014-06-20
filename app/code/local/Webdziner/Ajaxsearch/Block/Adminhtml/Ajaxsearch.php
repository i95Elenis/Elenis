<?php
class Webdziner_Ajaxsearch_Block_Adminhtml_Ajaxsearch extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_ajaxsearch';
    $this->_blockGroup = 'ajaxsearch';
    $this->_headerText = Mage::helper('ajaxsearch')->__('Ajax search setting');
    $this->_addButtonLabel = Mage::helper('ajaxsearch')->__('Add Item');
    parent::__construct();
  }
}