<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer_address", "gp_address_id",  array(
    "type"     => "varchar",
    "backend"  => "",
    "label"    => "Gp Address Id",
    "input"    => "text",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => "Gp Address Id"

	));

        $attribute   = Mage::getSingleton("eav/config")->getAttribute("customer_address", "gp_address_id");

        
$used_in_forms=array();

$used_in_forms[]="adminhtml_customer_address";
$used_in_forms[]="customer_register_address";
$used_in_forms[]="customer_address_edit";
        $attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 0)
		->setData("sort_order", 100)
		;
        $attribute->save();
	
	
	
$installer->endSetup();
	 