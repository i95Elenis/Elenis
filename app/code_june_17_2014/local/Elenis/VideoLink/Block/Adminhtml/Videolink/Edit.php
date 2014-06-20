<?php
	
class Elenis_VideoLink_Block_Adminhtml_Videolink_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "videolink";
				$this->_controller = "adminhtml_videolink";
				$this->_updateButton("save", "label", Mage::helper("videolink")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("videolink")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("videolink")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("videolink_data") && Mage::registry("videolink_data")->getId() ){

				    return Mage::helper("videolink")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("videolink_data")->getTitle()));

				} 
				else{

				     return Mage::helper("videolink")->__("Add Item");

				}
		}
}