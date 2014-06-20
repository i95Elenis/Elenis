<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_CalendarBase
 * User         Karen Baker
 * Date         2nd May 2013
 * Time         3pm
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Calendarbase_Model_Calendarbase_Source_Numofdatesatcheckout  {
	
    public function toOptionArray()  {

        $arr = array();
        for ($i=1;$i<=Webshopapps_Calendarbase_Model_Calendarbase::MAX_NUM_DATES_AT_CHECKOUT;$i++) {
            $arr[] = array('value'=>$i, 'label'=>$i);
        }
        return $arr;
    }
}
?>