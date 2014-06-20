<?php
    /* Calendarbase
     *
     * @category   Webshopapps
     * @package    Webshopapps_calendarbase
     * @copyright  Copyright (c) 2013 Zowta Ltd (http://www.webshopapps.com)
     * @license    http://www.webshopapps.com/license/license.txt - Commercial license
     */


class Webshopapps_Calendarbase_Model_Calendarbase {

    const MAX_NUM_WEEKS                 = 52;
    const MAX_NUM_DATES_AT_CHECKOUT     = 30;
    const MAX_NUM_TIMESLOTS             = 6;

    public function getCode($type, $code='')
    {
        $codes = array(
            'days'          => array(
                '1'         => Mage::helper('calendarbase')->__('MONDAY'),
                '2'         => Mage::helper('calendarbase')->__('TUESDAY'),
                '3'         => Mage::helper('calendarbase')->__('WEDNESDAY'),
                '4'         => Mage::helper('calendarbase')->__('THURSDAY'),
                '5'         => Mage::helper('calendarbase')->__('FRIDAY'),
                '6'         => Mage::helper('calendarbase')->__('SATURDAY'),
                '7'         => Mage::helper('calendarbase')->__('SUNDAY'),
                '99'        => Mage::helper('calendarbase')->__('NONE'),

            ),
            'short_days'    => array(
                '1'         => Mage::helper('calendarbase')->__('Mon'),
                '2'         => Mage::helper('calendarbase')->__('Tue'),
                '3'         => Mage::helper('calendarbase')->__('Wed'),
                '4'         => Mage::helper('calendarbase')->__('Thur'),
                '5'         => Mage::helper('calendarbase')->__('Fri'),
                '6'         => Mage::helper('calendarbase')->__('Sat'),
                '7'         => Mage::helper('calendarbase')->__('Sun'),
            ),
            'date_format'   => array(
                '1'    	    => 'd-m-Y',
                '2'    	    => 'm/d/Y',
                '3'		    => 'D d-m-Y',
                //  '3'    	=> 'j, n, Y',  //TODO Get these working with strtotime
                //  '4'   	=> 'm.d.y',
                //  '5'  	=> 'm/d/Y',
                //  '6'    	=> 'd.m.y',
                //  '7'   	=> 'F j, Y',
                //	'8'   	=> 'D M j Y',
            ),
            'shipoptions'   => array(
                'show_information'  => Mage::helper('shipping')->__('Display Information Text 1 & 2'),
            ),
        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Date Shipping code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Date Shipping code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

}