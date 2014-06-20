<?php
/**
 * WebShopApps.com
 *
[[[WEBSHOPAPPS_COPYRIGHT_TEXT]]]
 *
 * @category   WebShopApps
 * @package    WebShopApps_Invoicing
[[[WEBSHOPAPPS_COPYRIGHT]]]
 */
 
class  Webshopapps_Calendarbase_Model_Calendarbase_Source_Daysofweek  {
	
    public function toOptionArray()  {
    	
        $calendarbase = Mage::getSingleton('calendarbase/calendarbase');
        $arr = array();
        foreach ($calendarbase->getCode('days') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
?>