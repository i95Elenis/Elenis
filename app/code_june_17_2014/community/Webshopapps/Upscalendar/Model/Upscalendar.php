<?php
/* Calendar
 *
 * @category   Webshopapps
 * @package    Webshopapps_calendar
 * @copyright  Copyright (c) 2013 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */


class Webshopapps_Upscalendar_Model_Upscalendar {


    public function getCode($type, $code='')
    {
        $codes = array(

            'transit_map_code'=>array(
                '1DM'    => '14',
                '1DML'   => '14',
                '1DA'    => '01',
                '1DAL'   => '01',
                '1DAPI'  => '01',
                '1DP'    => '13',
                '1DPL'   => '13',
                '2DM'    => '02',
                '2DML'   => '02',
                '2DA'    => '02',
                '2DAL'   => '02',
                '3DS'    => '12',
                'GND'    => '03',
                'GNDCOM' => '03',
                'GNDRES' => '03',
                'STD'    => '11',
                'XPR'    => '07',
                'WXS'    => '65',
                'XPRL'   => '07',
                'XDM'    => '54',
                'XDML'   => '54',
                'XPD'    => '08',
                '2DAS'	 => '02',
                '1DAS'   => '01',  // Saturday delivery codes
                '1DMS'   => '14',  // Saturday delivery codes

                '01' => '07', // - UPS Worldwide Express;
                '02' => '02', // 02 - UPS 2nd Day Air;
                '03' => '11', // 03 - UPS Standard;
                '05' => '08', // 05 - UPS Worldwide Expedited;
                '06' => '07', // 06 - UPS Worldwide Express;
                '08' => '11', // 08 - UPS Standard;
                '09' => '07', // 09 - UPS Worldwide Express;
                '10' => '07', // 10 - UPS Express;
                '18' => '65', // 18 - UPS Saver;
                '19' => '08', // 19 - UPS Worldwide Expedited;
                '20' => '65', // 20 - UPS Saver;
                '21' => '54', // 21 - UPS Worldwide Express Plus;
                '22' => '54', // 22 - UPS Express Plus;
                '23' => '54', // 23 - UPS Express Plus;
                '24' => '07', // 24 - UPS Express;
                '25' => '11', // 25 - UPS Standard;
                '26' => '65', // 26 - UPS Saver;
                '28' => '65',
                '33' => '12', // 33 - UPS 3 Day Select;
                '82' => '82', // 82 - UPS Today Standard;
                '83' => '83', // 83 - UPS Today Dedicated Courier;
                '84' => '84', // 84 - UPS Today Intercity;
                '85' => '85', // 85 - UPS Today Express;
                '86' => '86', // 86 - UPS Today Express Saver
            ),

            'method'=>array(
                '1DM'    => Mage::helper('usa')->__('UPS Next Day Air Early AM'),
                '1DML'   => Mage::helper('usa')->__('UPS Next Day Air Early AM Letter'),
                '1DA'    => Mage::helper('usa')->__('UPS Next Day Air'),
                '1DAL'   => Mage::helper('usa')->__('UPS Next Day Air Letter'),
                '1DAPI'  => Mage::helper('usa')->__('UPS Next Day Air Intra (Puerto Rico)'),
                '1DP'    => Mage::helper('usa')->__('UPS Next Day Air Saver'),
                '1DPL'   => Mage::helper('usa')->__('UPS Next Day Air Saver Letter'),
                '2DM'    => Mage::helper('usa')->__('UPS Second Day Air AM'),
                '2DML'   => Mage::helper('usa')->__('UPS Second Day Air AM Letter'),
                '2DA'    => Mage::helper('usa')->__('UPS Second Day Air'),
                '2DAL'   => Mage::helper('usa')->__('UPS Second Day Air Letter'),
                '3DS'    => Mage::helper('usa')->__('UPS Three-Day Select'),
                'GND'    => Mage::helper('usa')->__('UPS Ground'),
                'GNDCOM' => Mage::helper('usa')->__('UPS Ground Commercial'),
                'GNDRES' => Mage::helper('usa')->__('UPS Ground Residential'),
                'STD'    => Mage::helper('usa')->__('UPS Canada Standard'),
                'XPR'    => Mage::helper('usa')->__('UPS Worldwide Express'),
                'WXS'    => Mage::helper('usa')->__('UPS Worldwide Express Saver'),
                'XPRL'   => Mage::helper('usa')->__('UPS Worldwide Express Letter'),
                'XDM'    => Mage::helper('usa')->__('UPS Worldwide Express Plus'),
                'XDML'   => Mage::helper('usa')->__('UPS Worldwide Express Plus Letter'),
                'XPD'    => Mage::helper('usa')->__('UPS Worldwide Expedited'),
                '2DAS'	 => Mage::helper('usa')->__('UPS Second Day Air Saturday'),  // Added in Saturday
                '1DAS'   => Mage::helper('usa')->__('UPS Next Day Air Saturday'), // Added in Saturday
                '1DMS'   => Mage::helper('usa')->__('UPS Next Day Air Early AM Saturday'), // Added in Saturday

                '01' => Mage::helper('usa')->__('UPS Worldwide Express'),
                '02' => Mage::helper('usa')->__('UPS Second Day Air'), // 02 - UPS Second Day Air;
                '03' => Mage::helper('usa')->__('UPS Standard'), // 03 - UPS Standard;
                '05' => Mage::helper('usa')->__('UPS Worldwide Expedited'), // 05 - UPS Worldwide Expedited;
                '06' => Mage::helper('usa')->__('UPS Worldwide Express'), // 06 - UPS Worldwide Express;
                '08' => Mage::helper('usa')->__('UPS Standard'), // 08 - UPS Standard;
                '09' => Mage::helper('usa')->__('UPS Worldwide Express'), // 09 - UPS Worldwide Express;
                '10' => Mage::helper('usa')->__('UPS Express'), // 10 - UPS Express;
                '18' => Mage::helper('usa')->__('UPS Saver'), // 18 - UPS Saver;
                '19' => Mage::helper('usa')->__('UPS Worldwide Expedited'), // 19 - UPS Worldwide Expedited;
                '20' => Mage::helper('usa')->__('UPS Saver'), // 20 - UPS Saver;
                '21' => Mage::helper('usa')->__('UPS Worldwide Express Plus'), // 21 - UPS Worldwide Express Plus;
                '22' => Mage::helper('usa')->__('UPS Express Plus'), // 22 - UPS Express Plus;
                '23' => Mage::helper('usa')->__('UPS Express Plus'), // 23 - UPS Express Plus;
                '24' => Mage::helper('usa')->__('UPS Express'), // 24 - UPS Express;
                '25' => Mage::helper('usa')->__('UPS Standard'), // 25 - UPS Standard;
                '26' => Mage::helper('usa')->__('UPS Saver'), // 26 - UPS Saver;
                '28' => Mage::helper('usa')->__('UPS Worldwide Saver'), // 26 - UPS Saver;
                '33' => Mage::helper('usa')->__('UPS Three-Day Select'), // 33 - UPS 3 Day Select;
                '82' => Mage::helper('usa')->__('UPS Today Standard'), // 82 - UPS Today Standard;
                '83' => Mage::helper('usa')->__('UPS Today Dedicated Courier'), // 83 - UPS Today Dedicated Courier;
                '84' => Mage::helper('usa')->__('UPS Today Intercity'), // 84 - UPS Today Intercity;
                '85' => Mage::helper('usa')->__('UPS Today Express') , // 85 - UPS Today Express;
                '86' => Mage::helper('usa')->__('UPS Today Express Saver'), // 86 - UPS Today Express Saver
            ),
        );

        if (!isset($codes[$type])) {
            Mage::log( Mage::helper('shipping')->__('Invalid UPS Calendar code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            Mage::log( Mage::helper('shipping')->__('Invalid UPS Calendar code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }
}