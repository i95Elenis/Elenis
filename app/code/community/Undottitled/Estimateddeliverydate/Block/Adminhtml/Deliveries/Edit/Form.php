<?php
 
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        
		 $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => str_replace("adminhtml_deliveries/","deliveries/",$this->getData('action')),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $retailer = Mage::registry('deliveries_data');
        if ($retailer && $retailer->getId()) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'id',
            ));
            $form->setValues($retailer->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
		
	}
}