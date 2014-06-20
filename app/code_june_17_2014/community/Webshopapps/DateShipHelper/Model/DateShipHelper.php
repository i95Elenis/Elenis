<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_DateShipHelper
 * User         karen
 * Date         18/05/2013
 * Time         23:51
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_DateShipHelper_Model_DateShipHelper {

    const MAX_NUM_WEEKS                 = 52;
    const MAX_NUM_DATES_AT_CHECKOUT     = 30;
    const MAX_NUM_TIMESLOTS             = 6;

    public function getCode($type, $code='')
    {
        $codes = array(


            'days'=>array(
                '1'         => Mage::helper('webshopapps_dateshiphelper')->__('MONDAY'),
                '2'         => Mage::helper('webshopapps_dateshiphelper')->__('TUESDAY'),
                '3'         => Mage::helper('webshopapps_dateshiphelper')->__('WEDNESDAY'),
                '4'         => Mage::helper('webshopapps_dateshiphelper')->__('THURSDAY'),
                '5'         => Mage::helper('webshopapps_dateshiphelper')->__('FRIDAY'),
                '6'         => Mage::helper('webshopapps_dateshiphelper')->__('SATURDAY'),
                '7'         => Mage::helper('webshopapps_dateshiphelper')->__('SUNDAY'),
                '99'        => Mage::helper('webshopapps_dateshiphelper')->__('NONE'),

            ),
            'short_days'=>array(
                '1'         => Mage::helper('webshopapps_dateshiphelper')->__('Mon'),
                '2'         => Mage::helper('webshopapps_dateshiphelper')->__('Tue'),
                '3'         => Mage::helper('webshopapps_dateshiphelper')->__('Wed'),
                '4'         => Mage::helper('webshopapps_dateshiphelper')->__('Thur'),
                '5'         => Mage::helper('webshopapps_dateshiphelper')->__('Fri'),
                '6'         => Mage::helper('webshopapps_dateshiphelper')->__('Sat'),
                '7'         => Mage::helper('webshopapps_dateshiphelper')->__('Sun'),
            ),
            'date_format'   =>array(
                '1'    	    => 'd-m-Y',
                '2'    	    => 'm/d/Y',
     //           '3'		    => 'D d-m-Y',
                //  '3'    	=> 'j, n, Y',  //TODO Get these working with strtotime
                //  '4'   	=> 'm.d.y',
                //  '5'  	=> 'm/d/Y',
                //  '6'    	=> 'd.m.y',
                //  '7'   	=> 'F j, Y',
                //	'8'   	=> 'D M j Y',
            ),
            'short_date_format'   =>array(
                '1'    	    => 'd-m-y',
                '2'    	    => 'm/d/y',
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Date Ship Helper code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Date Ship Helper code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

}