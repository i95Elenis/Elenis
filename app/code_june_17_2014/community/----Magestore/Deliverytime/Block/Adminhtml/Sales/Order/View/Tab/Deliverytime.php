<?php

class Magestore_Deliverytime_Block_Adminhtml_Sales_Order_View_Tab_Deliverytime extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('deliverytime/adminorder.phtml');
	}
	
	public function getDeliverytime()
	{
		$order = $this->getOrder();
		if (!$order) 
		{
			return "";
		}
		$order_id = $order->getId();
		$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
		$entity_type_id = $entity_type->getId();
		$attribute = Mage::getModel("eav/entity_attribute")->load("order_deliverydate","attribute_code");
		$attribute_id = $attribute->getId();
			
		$resource = Mage::getSingleton('core/resource');			
		$read = $resource->getConnection('core_read');
		
		$select = $read->select()
					   ->from("sales_order_entity_text",array('value'))
					   ->where("entity_type_id=?",$entity_type_id)
					   ->where("attribute_id=?",$attribute_id)
					   ->where("entity_id=?",$order_id);
		$attribute = $read->fetchRow($select);		
		$deliverytime = $attribute['value'];	
		return 	$deliverytime;		
	}
	
	public function getOrder()
    {       
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
       
    }
}