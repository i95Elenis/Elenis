<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 10:15 AM
 * File   : mysql4-install-2.0.0.php
 * Module : Ebizmarts_SagePaymentsPro
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

	-- ------------------------------------------------------------------
	-- MC is MasterCard, UKE is Visa Electron. MAESTRO should be
	-- used for both UK and International Maestro.
	-- AMEX and DC (DINERS) can only be accepted
	-- if you have additional merchant accounts with those acquirers.
	-- ------------------------------------------------------------------
	CREATE TABLE IF NOT EXISTS `{$this->getTable('sagepaymentspro_tokencard')}` (
	  `id` int(10) unsigned NOT NULL auto_increment,
      `customer_id` int(10) unsigned NOT NULL,
	  `token` varchar(38),
	  `status` varchar(15),
	  `card_type` varchar(255),
	  `last_four` varchar(4),
	  `expiry_date` varchar(4),
	  `status_detail` varchar(255),
      `vendor` varchar(255),
      `protocol` enum('server', 'direct'),
      `is_default` tinyint(1) unsigned NOT NULL default '0',
      `visitor_session_id` varchar(255),
	  PRIMARY KEY  (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

   -- -----------------------------------------------------
    -- Table `{$this->getTable('sagepaysuite_transaction')}`
    -- -----------------------------------------------------
    CREATE TABLE IF NOT EXISTS `{$this->getTable('sagepaymentspro_transaction')}` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
      `order_id` INT(11) UNSIGNED NULL,
      `response_status` VARCHAR(255) NOT NULL,
      `post_code_result` VARCHAR(255) NOT NULL,
      `response_status_detail` VARCHAR(255) NOT NULL,
      `cvv_indicator` VARCHAR(255) NOT NULL,
      `risk_indicator` VARCHAR(255) NOT NULL,
      `trn_securitykey` VARCHAR(255) NOT NULL,
      `amount` DECIMAL(12,4),
      `type` enum('capture','authorize','refund','void','release'),
      `transaction_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`) )
    ENGINE = InnoDB DEFAULT CHARSET=utf8;
");




$installer->endSetup();