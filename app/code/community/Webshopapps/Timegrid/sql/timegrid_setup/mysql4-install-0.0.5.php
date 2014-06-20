<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_$(PROJECT_NAME)
 * User         joshstewart
 * Date         05/06/2013
 * Time         14:33
 * @copyright   Copyright (c) $(YEAR) Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, $(YEAR), Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('wsa_timegrid')};
CREATE TABLE {$this->getTable('wsa_timegrid')} (
  `timegrid_id` int(11) unsigned NOT NULL auto_increment,
  `week_commencing` 	date NULL,
  `time_slot_id` 		varchar(30) NOT NULL default '',
  `1_price` 			decimal(12,2)  NULL,
  `2_price` 			decimal(12,2)  NULL,
  `3_price` 			decimal(12,2)  NULL,
  `4_price` 			decimal(12,2) NULL,
  `5_price` 			decimal(12,2)  NULL,
  `6_price` 			decimal(12,2)  NULL,
  `7_price` 			decimal(12,2)  NULL,
  `1_slots` 			int (5)  NULL,
  `2_slots` 			int (5)  NULL,
  `3_slots` 			int (5)  NULL,
  `4_slots` 			int (5)  NULL,
  `5_slots` 			int (5)  NULL,
  `6_slots` 			int (5)  NULL,
  `7_slots` 			int (5)  NULL,
  `1_dispatch` 			int (5)  NULL,
  `2_dispatch` 			int (5)  NULL,
  `3_dispatch` 			int (5)  NULL,
  `4_dispatch` 			int (5)  NULL,
  `5_dispatch` 			int (5)  NULL,
  `6_dispatch` 			int (5)  NULL,
  `7_dispatch` 			int (5)  NULL,
   PRIMARY KEY (`timegrid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
