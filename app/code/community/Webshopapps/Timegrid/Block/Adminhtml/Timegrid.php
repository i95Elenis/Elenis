<?php
class Webshopapps_Timegrid_Block_Adminhtml_Timegrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_timegrid';
    $this->_blockGroup = 'timegrid';
    $this->_headerText = Mage::helper('timegrid')->__('WebShopApps Delivery Manager');
    $this->_addButtonLabel = Mage::helper('timegrid')->__('Add Timeslot');
    parent::__construct();
  }
}