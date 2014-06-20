<?php

$installer = $this;

$installer->startSetup();

$version = Mage::helper('wsalogger')->getNewVersion();


$dispatchDate =  array(
    'type'    	=> $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'	=> 20,
    'comment' 	=> 'Dispatch Date',
    'nullable' 	=> 'true');

$expectedDelivery =  array(
    'type'    	=> $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'	=> 20,
    'comment' 	=> 'Expected Delivery',
    'nullable' 	=> 'true');

$deliveryDescription = array(
    'type'    	=> $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'comment' 	=> 'Delivery Description',
    'nullable' 	=> 'true');

$deliveryNotes = array(
    'type'    	=> $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'comment' 	=> 'Delivery Notes',
    'nullable' 	=> 'true');

$earliest = array(
    'type'    	=> $version > 10 ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'length'	=> 20,
    'comment' 	=> 'Earliest Delivery',
    'nullable' 	=> 'true');

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dispatch_date', $dispatchDate );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'expected_delivery', $expectedDelivery );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'delivery_description', $deliveryDescription );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'delivery_notes', $deliveryNotes );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'earliest', $earliest );

$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'dispatch_date', $dispatchDate );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'expected_delivery', $expectedDelivery );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'delivery_description', $deliveryDescription );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'delivery_notes', $deliveryNotes );
$installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'earliest', $earliest );

$installer->getConnection()->addColumn($installer->getTable('sales/order'),'dispatch_date', $dispatchDate );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'expected_delivery', $expectedDelivery );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'delivery_description', $deliveryDescription );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'delivery_notes', $deliveryNotes );
$installer->getConnection()->addColumn($installer->getTable('sales/order'),'earliest', $earliest );

$dateShipHelper = $installer->_conn->fetchAll("select * from {$this->getTable('core_config_data')} where path in ('carriers/matrixdays/active','carriers/matrixdays/num_slots','carriers/matrixdays/default_avail_slots','carriers/matrixdays/slot_1','carriers/matrixdays/slot_2','carriers/matrixdays/slot_3','carriers/matrixdays/slot_4','carriers/matrixdays/slot_5','carriers/matrixdays/slot_6','carriers/matrixdays/ship_options','carriers/matrixdays/num_of_weeks','carriers/matrixdays/custom_text_1','carriers/matrixdays/custom_text_2')");
$customCalendar = $installer->_conn->fetchAll("select * from {$this->getTable('core_config_data')} where path in ('carriers/matrixdays/active','carriers/matrixdays/condition_name','carriers/matrixdays/star_include_all','carriers/matrixdays/title','carriers/matrixdays/zipcode_max_length','carriers/matrixdays/postcode_filter','carriers/matrixdays/default_ship_price','carriers/matrixdays/max_shipping_cost','carriers/matrixdays/free_shipping_text','carriers/matrixdays/parent_group')");

$search = array('carriers','matrixdays');
$replace = array ('shipping','webshopapps_dateshiphelper');

foreach ($dateShipHelper as $r) {
    $r['path'] = str_replace($search,$replace,$r['path']);
    $installer->_conn->update($this->getTable('core_config_data'), $r, 'config_id='.$r['config_id']);
}

$replace = array ('carriers','customcalendar');

foreach ($customCalendar as $r) {
    $r['path'] = str_replace($search,$replace,$r['path']);
    $installer->_conn->update($this->getTable('core_config_data'), $r, 'config_id='.$r['config_id']);
}

$installer->endSetup();