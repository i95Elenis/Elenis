<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();


$attr = array(
    'input'    => 'date',
    'type'     => 'date',
    'grid'     => true,
    'label'    => 'Delivery Date',
);
if(version_compare(Mage::getVersion(), '1.11.0.0','>=')) {
    $attr['type'] = 'datetime'; //check Mage_Sales_Model_Mysql4_Setup::_getAttributeColumnDefinition for more details
}
$installer->addAttribute('order', 'delivery_date', $attr);


$installer->run("

DROP TABLE IF EXISTS {$this->getTable('adjholiday')};
CREATE TABLE `{$this->getTable('adjholiday')}` (
  `holiday_id` mediumint(8) unsigned NOT NULL auto_increment,
  `y` smallint(4) NOT NULL default '0',
  `m` tinyint(3) NOT NULL default '0',
  `d` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");


/*
$typeId = Mage::getModel('eav/entity_type')->loadByCode('order')->getEntityTypeId();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('adjholiday')};
CREATE TABLE `{$this->getTable('adjholiday')}` (
  `holiday_id` mediumint(8) unsigned NOT NULL auto_increment,
  `y` smallint(4) NOT NULL default '0',
  `m` tinyint(3) NOT NULL default '0',
  `d` tinyint(3) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM `{$this->getTable('eav/attribute')}` WHERE `attribute_code`='delivery_date' limit 1;

INSERT INTO `{$this->getTable('eav/attribute')}` (entity_type_id, attribute_code, backend_type, frontend_input, frontend_label) 
                            VALUES ($typeId, 'delivery_date', 'datetime', 'date', 'Delivery Date');

");
*/

$installer->endSetup();