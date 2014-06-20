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
UPDATE {$this->getTable('catalog_product_option')} 
    SET `type` = 'aitcustomer_image' 
    WHERE `type`='file' AND option_id in (SELECT option_id FROM {$this->getTable('catalog_product_option_aitimage')});
");


$installer->endSetup();