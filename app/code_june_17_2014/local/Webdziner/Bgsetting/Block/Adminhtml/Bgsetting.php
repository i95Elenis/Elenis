<?php
class Webdziner_Bgsetting_Block_Adminhtml_Bgsetting extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_bgsetting';
    $this->_blockGroup = 'bgsetting';
    $this->_headerText = Mage::helper('bgsetting')->__('Manage Background Setting');
    parent::__construct();
  }
}