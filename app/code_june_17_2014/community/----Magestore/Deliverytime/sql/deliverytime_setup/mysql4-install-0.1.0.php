<?php

$installer = $this;

$installer->startSetup();

//get entity_type_id
$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
$entity_type_id = $entity_type->getId();


$attribute = Mage::getSingleton("eav/entity_attribute")->load("order_deliverydate","attribute_code");

$attribute->setData("entity_type_id",$entity_type_id);
$attribute->setData("attribute_code","order_deliverydate");
$attribute->setData("backend_type","text");
$attribute->setData("frontend_input","text");

$attribute->save();
 
$installer->endSetup(); 