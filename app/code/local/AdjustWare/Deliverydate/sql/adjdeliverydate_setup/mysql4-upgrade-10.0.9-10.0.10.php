<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
$installer = $this;
/* @var $installer AdjustWare_Deliverydate_Model_Mysql4_Setup */

$installer->startSetup();

$attribute = 'delivery_date';
if(version_compare(Mage::getVersion(), '1.11.0.0','>=')) {
$attr = array(
    'input'    => 'date',
    'type'     => 'datetime',
    'grid'     => true,
    'label'    => 'Delivery Date',
    'comment'=> 'Delivery Date'
);
} else {
    $attr = "datetime NULL DEFAULT NULL COMMENT 'Delivery Date';";
}

/* Using own flat table attribute update function */
$installer->replaceFlatAttribute('order',$attribute,$attr);

$installer->endSetup();