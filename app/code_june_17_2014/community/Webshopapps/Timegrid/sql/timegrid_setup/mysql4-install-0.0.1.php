<?php

$installer = $this;

$installer->startSetup();

// // `date_from` 		date  NOT NULL,
 // `date_to` 		date  NOT NULL,
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('wsa_timegrid')};
CREATE TABLE {$this->getTable('wsa_timegrid')} (
  `timegrid_id` int(11) unsigned NOT NULL auto_increment,

  `time_slot_id` 	varchar(30) NOT NULL default '',
  `1_price` 		decimal(12,2)  NULL,
  `2_price` 		decimal(12,2)  NULL,
  `3_price` 		decimal(12,2)  NULL,
  `4_price` 		decimal(12,2) NULL,
  `5_price` 		decimal(12,2)  NULL,
  `6_price` 		decimal(12,2)  NULL,
  `7_price` 		decimal(12,2)  NULL,
  `1_slots` 		int (5)  NULL,
  `2_slots` 		int (5)  NULL,
  `3_slots` 		int (5)  NULL,
  `4_slots` 		int (5)  NULL,
  `5_slots` 		int (5)  NULL,
  `6_slots` 		int (5)  NULL,
  `7_slots` 		int (5)  NULL,
   PRIMARY KEY (`timegrid_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();


