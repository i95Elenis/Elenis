<?php


class Elenis_VideoLink_Block_Adminhtml_Videolink extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_videolink";
	$this->_blockGroup = "videolink";
	$this->_headerText = Mage::helper("videolink")->__("Videolink Manager");
	$this->_addButtonLabel = Mage::helper("videolink")->__("Add New Item");
	parent::__construct();
	
	}

}