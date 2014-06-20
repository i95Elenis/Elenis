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
/**
 * Created by Aitoc.
 * User: Alexander Scraschuk <scraschuk@aitoc.com>
 * Date: 02.05.13
 * Time: 11:31
 * To change this template use File | Settings | File Templates.
 */

$installer = $this;

$installer->startSetup();

$attr = array(
    'input'    => 'date',
    'type'     => 'date',
    'grid'     => true,
    'label'    => 'Delivery Date',
);

if (version_compare(Mage::getVersion(), '1.6', '>='))
{
    $attr = array(
        'input'    => 'date',
        'type'     => 'datetime',
        'grid'     => true,
        'label'    => 'Delivery DT',
    );

}
$installer->addAttribute('quote_address', 'delivery_date', $attr);

$attr = array(
    'input'    => 'text',
    'type'     => 'text',
    'grid'     => true,
    'label'    => 'Delivery Comment',
);
$installer->addAttribute('quote_address', 'delivery_comment', $attr);