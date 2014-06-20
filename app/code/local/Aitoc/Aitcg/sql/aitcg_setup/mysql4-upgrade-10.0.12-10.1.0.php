<?php
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     AJifvLXz2Jhov40GDpSzkNqfs4dkmEPJtRhHJxJI2y
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('aitcg/font')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('aitcg/font')}` (
  `font_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`font_id`),
  KEY `name` (`name`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('All Star Resort', 'allstarresort.ttf', '1');
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('Amsterdam', 'amsterdam.ttf', '1');
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('Capture It', 'captureit.ttf', '1');
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('Capture It 2', 'captureit2.ttf', '1');
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('Old London', 'oldlondon.ttf', '1');
INSERT INTO `{$this->getTable('aitcg/font')}` (`name`, `filename`, `status`) VALUES ('Sketch Block', 'sketchblock.ttf', '1');
");
$installer->run("
ALTER TABLE {$this->getTable('catalog/product_option_aitimage')} ADD `use_text` TINYINT( 1 ) DEFAULT '0';
");
$installer->run("
ALTER TABLE {$this->getTable('aitcg/image')} ADD `img_data` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
");
       
$installer->run("
-- DROP TABLE IF EXISTS `{$this->getTable('aitcg/image_temp')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('aitcg/image_temp')}` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`create_time` DATETIME NOT NULL ,
`file_name` VARCHAR( 128 ) NOT NULL ,
PRIMARY KEY (`id`),
INDEX ( `create_time` )
) ENGINE = InnoDB;
");   
Mage::helper('aitcg')->recursiveDelete(Mage::getBaseDir('media').DS.'custom_product_preview'.DS.'temp'.DS);
$installer->endSetup();