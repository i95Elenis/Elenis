<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_$(PROJECT_NAME)
 * User         joshstewart
 * Date         05/06/2013
 * Time         14:38
 * @copyright   Copyright (c) $(YEAR) Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, $(YEAR), Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '1_dispatch', 'int (5) NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '2_dispatch', 'int (5)  NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '3_dispatch', 'int (5)  NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '4_dispatch', 'int (5)  NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '5_dispatch', 'int (5)  NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '6_dispatch', 'int (5)  NULL DEFAULT -1');
$installer->getConnection()->addColumn($this->getTable('wsa_timegrid'), '7_dispatch', 'int (5)  NULL DEFAULT -1');

$this->endSetup();
