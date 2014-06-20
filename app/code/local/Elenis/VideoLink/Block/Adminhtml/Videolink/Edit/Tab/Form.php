<?php
class Elenis_VideoLink_Block_Adminhtml_Videolink_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("videolink_form", array("legend"=>Mage::helper("videolink")->__("Item information")));

				
						$fieldset->addField("title", "text", array(
						"label" => Mage::helper("videolink")->__("Video Title"),
						"name" => "title",
						));
					
						$fieldset->addField("video_link", "textarea", array(
						"label" => Mage::helper("videolink")->__("Video Link"),
						"name" => "video_link",
						));
                        $fieldset->addField("width", "text", array(
						"label" => Mage::helper("videolink")->__("Video Width"),
						"name" => "width",
						));
                        
                        $fieldset->addField("height", "text", array(
						"label" => Mage::helper("videolink")->__("Video Height"),
						"name" => "height",
						));
						 $fieldset->addField('category_id', 'select', array(
						'label'     => Mage::helper('videolink')->__('Category Id'),
						'values'   => Elenis_VideoLink_Block_Adminhtml_Videolink_Grid::getCategoriesValue(),
						'name' => 'category_id',
						));
                         
                         
                       /*  $fieldset->addField("file_link","file",array(
                             'label'     => Mage::helper('videolink')->__('Upload'),
                             'value'=>'Upload',
                             'name'=>"file"
                         ));
                         */
                          
                             
                        // echo "<pre>";print_r($fieldset);exit;

				if (Mage::getSingleton("adminhtml/session")->getVideolinkData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getVideolinkData());
					Mage::getSingleton("adminhtml/session")->setVideolinkData(null);
				} 
				elseif(Mage::registry("videolink_data")) {
				    $form->setValues(Mage::registry("videolink_data")->getData());
				}
				return parent::_prepareForm();
		}
}
