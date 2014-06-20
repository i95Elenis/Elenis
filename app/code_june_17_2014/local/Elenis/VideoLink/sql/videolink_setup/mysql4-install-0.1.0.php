<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
DROP TABLE IF EXISTS `mage_elenis_static_video`;
CREATE TABLE `mage_elenis_static_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100)  NOT NULL,
  `video_link` text NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mage_elenis_static_video_category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 