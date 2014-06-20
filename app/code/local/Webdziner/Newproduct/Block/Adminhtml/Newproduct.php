<?php 
class Webdziner_Newproduct_Block_Adminhtml_Newproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_newproduct';
    $this->_blockGroup = 'newproduct';
    $this->_headerText = Mage::helper('newproduct')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('newproduct')->__('Add Item');
    parent::__construct();
  }
}