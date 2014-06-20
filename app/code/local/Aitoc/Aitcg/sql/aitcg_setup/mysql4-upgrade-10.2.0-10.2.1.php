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
ALTER TABLE {$this->getTable('catalog/product_option_aitimage')} ADD `text_length` INT( 11 ) DEFAULT '80';
");

$installer->run("
ALTER TABLE {$this->getTable('catalog/product_option_aitimage')} ADD `allow_colorpick` TINYINT( 1 ) DEFAULT '1';
");

$installer->endSetup();