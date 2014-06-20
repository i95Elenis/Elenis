<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Custom Options extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomOptions
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

/* @var $installer MageWorx_CustomOptions_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$installer->getTable('customoptions/option_description')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('customoptions/option_description')} (
  `option_description_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `description` text,
  PRIMARY KEY  (`option_description_id`),
  KEY `MAGEWORX_CUSTOM_OPTIONS_DESCRIPTION_OPTION` (`option_id`),
  KEY `MAGEWORX_CUSTOM_OPTIONS_DESCRIPTION_STORE` (`store_id`),
  CONSTRAINT `FK_MAGEWORX_CUSTOM_OPTIONS_DESCRIPTION_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$installer->getTable('catalog/product_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_MAGEWORX_CUSTOM_OPTIONS_DESCRIPTION_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();
