<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("quote_address_item", "check_multiple", array("type"=>"int"));

$sql=<<<SQLTEXT
DROP TABLE IF EXISTS mage_elenis_multiship;
CREATE TABLE IF NOT EXISTS mage_elenis_multiship (
id INT AUTO_INCREMENT PRIMARY KEY,
quote_id INT,
address_id INT,
addresses varchar(100),
item_id INT,
product_id INT,
parent_item_id INT,
quote_address_id INT,
quote_item_id INT,
address_item_id INT,
qty INT,
customer_id INT,
numrecipients INT,
actualqty INT,
is_deleted ENUM('true', 'false') default 'false',
number_splits int default 1,
total_qty INT default 0
)
SQLTEXT;

$sql=<<<SQLTEXT
DROP TABLE IF EXISTS mage_elenis_multiship;
CREATE TABLE IF NOT EXISTS mage_elenis_multiship (
id INT AUTO_INCREMENT PRIMARY KEY,
quote_id INT,
address_id INT,
addresses varchar(100),
item_id INT,
product_id INT,
parent_item_id INT,
quote_address_id INT,
quote_item_id INT,
address_item_id INT,
qty INT,
customer_id INT,
numrecipients INT,
actualqty INT,
is_deleted ENUM('true', 'false') default 'false',
number_splits int default 1,
total_qty INT default 0
)
SQLTEXT;
$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 