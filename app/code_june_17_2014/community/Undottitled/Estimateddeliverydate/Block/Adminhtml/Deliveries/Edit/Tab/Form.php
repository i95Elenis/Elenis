<?php
 
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
    { 
		parent::__construct();
	}
	
    public function initForm()
    {
       	$form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('deliveries_form', array('legend'=>Mage::helper('estimateddeliverydate')->__('General Information')));
       
        $fieldset->addField('id', 'hidden', array(
            'name'      => 'id'	
        ));
	   
		$fieldset->addField('qty', 'text', array(
            'label'     => Mage::helper('estimateddeliverydate')->__('Quantity Due'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'qty',
        ));
        
		$fieldset->addField('date', 'date', array(
            'name'   => 'date',
            'class' => 'required-entry',
            'label'  => $this->__('Due date'),
            'title'  => $this->__('Due date'),
            'time'      =>	true,
            'required'  =>  true,
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format'       => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),            
        ));
		
 		
 		$fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('estimateddeliverydate')->__('Delivery Status'),
			'class'		=> 'required_entry',
			'required'  => true,
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('estimateddeliverydate')->__('Pending'),
                ),
 
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('estimateddeliverydate')->__('Delivered'),
                )
            ),
        ));
 		
 		if (Mage::registry('deliveries_data')) {
            $form->setValues(Mage::registry('deliveries_data')->getData());
        }
        return parent::_prepareForm();
    }
}