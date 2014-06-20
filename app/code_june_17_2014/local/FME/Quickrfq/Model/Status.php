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
 
class FME_Quickrfq_Model_Status extends Varien_Object
{
    const STATUS_YES    	= 'Yes';
    const STATUS_NO     	= 'No';
     const STATUS_PENDING	= 'pending';
    const STATUS_UNDERPROCESS	= 'process';

    static public function getOptionArray()
    {
        return array(
           
            self::STATUS_NO   => Mage::helper('quickrfq')->__('New'),
            self::STATUS_UNDERPROCESS    => Mage::helper('quickrfq')->__('Under Process'),
            self::STATUS_PENDING    => Mage::helper('quickrfq')->__('Pending'),
            self::STATUS_YES    => Mage::helper('quickrfq')->__('Done')
        );
    }
}