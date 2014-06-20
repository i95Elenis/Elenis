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
/* @var $installer Mage_Core_Model_Resource_Setup  */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('aitcg/category')}` ADD `store_labels` TEXT NOT NULL;
    ALTER TABLE `{$this->getTable('aitcg/mask_category')}` ADD `store_labels` TEXT NOT NULL;
");

$installer->endSetup();