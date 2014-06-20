<?php
class Elenis_VideoLink_Block_Adminhtml_Videolink_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("videolink_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("videolink")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("videolink")->__("Item Information"),
				"title" => Mage::helper("videolink")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("videolink/adminhtml_videolink_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
