<?php

class Magestore_Deliverytime_Model_Deliverytime extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('deliverytime/deliverytime');
    }
	
	public function save_deliverytime($observer)
	{	
		if(!Mage::helper('deliverytime')->isModuleEnable())
		{
			return;
		}
		try{
			$observer_data = $observer->getData();		
			$order =  $observer_data['order'];
			$order_id = $order->getId();
			
			$deliverytime = $this->getDeliverydateSession()->getDeliverytime();
			
			$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
			$entity_type_id = $entity_type->getId();
			$attribute = Mage::getModel("eav/entity_attribute")->load("order_deliverydate","attribute_code");
			$attribute_id = $attribute->getId();
			
			$resource = Mage::getSingleton('core/resource');			
			$write = $resource->getConnection('core_write');
			
			//$table_prefix = $this->getTablePrefix();
			$write->beginTransaction();
			$write->insert("sales_order_entity_text",array("entity_type_id"=>$entity_type_id,"attribute_id"=>$attribute_id,"entity_id"=>$order_id,"value"=>$deliverytime));
			$write->commit();
			
			$this->getDeliverydateSession()->unsetAll();
			
		}
        catch (Mage_Core_Exception $e) {
            
        }	
					
	}
	
	
	
	
	public function getDeliverydateSession()
	{
		return Mage::getSingleton('deliverytime/session');
	}
	
	public function getTablePrefix()
	{
		$table = Mage::getResourceSingleton("eav/attribute")->getTable("eav/attribute");
		
		$prefix = str_replace("eav_attribute","",$table);
		
		return $prefix;
	}
	
	
	
	
	
	
}