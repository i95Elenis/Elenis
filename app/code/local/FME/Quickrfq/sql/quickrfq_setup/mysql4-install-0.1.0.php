<?php

 /**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('quickrfq')};
CREATE TABLE {$this->getTable('quickrfq')} (
  `quickrfq_id` int(11) unsigned NOT NULL auto_increment,
  `company` varchar(255) NULL,
  `contact_name` varchar(255) NOT NULL default '',
  `phone` varchar(25) NULL,
  `email`  varchar(255) NOT NULL default '',
   `project_title`  varchar(255) NULL,
   prefered_methods varchar(100) NOT NULL default '',
   occasion text not null default '',
  `date` date NULL,
   `budget` varchar(255) NOT NULL default 'Approved',
    `overview`  text NOT NULL default '',
    `prd`  varchar(255) NULL,
    `status`  varchar(255) NOT NULL default 'No',
    `create_date` date NOT NULL,
    `update_date` date NULL,
  PRIMARY KEY (`quickrfq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
$installer->setConfigData('quickrfq/email/recipient','someone@example.com');
$installer->setConfigData('quickrfq/option/enable','1');
$installer->setConfigData('quickrfq/upload/allow','jpg');
$installer->setConfigData('quickrfq/option/date','5');


$installer->endSetup(); 