<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create table 'catalog/product'
 */
 
/* CODE FOR USE WITH Magento CE 1.7+ */
/*$table = $installer->getConnection()
	->newTable($installer->getTable('estimateddeliverydate/deliveries'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
	->addColumn('pid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        ), 'Product Entity ID')
	->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
        ), 'Product SKU')
	->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
		), 'Delivery Date')
	->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
		'unsigned'  => true,
		'nullable'  => true,
        ), 'Delivery Qty')
	->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'	=> '0'
        ), 'Delivery Status')
	->setComment('Catalog Product Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();*/


$installer->run("

DROP TABLE IF EXISTS {$this->getTable('estimateddeliverydate/deliveries')};
CREATE TABLE `{$this->getTable('estimateddeliverydate/deliveries')}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity ID',
  `pid` int(10) unsigned DEFAULT NULL COMMENT 'Product Entity ID',
  `sku` varchar(255) NOT NULL COMMENT 'Product SKU',
  `date` datetime DEFAULT NULL COMMENT 'Delivery Date',
  `qty` decimal(12,2) DEFAULT NULL COMMENT 'Delivery Qty',
  `status` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Delivery Status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Catalog Product Datetime Attribute Backend Table';");

$installer->endSetup();