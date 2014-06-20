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
 
class Webshopapps_Calendarbase_Model_Calendarbase_Source_Numofweeks  {
	
    public function toOptionArray()  {
    	
        $arr = array();
        for ($i=1;$i<=Webshopapps_Calendarbase_Model_Calendarbase::MAX_NUM_WEEKS;$i++) {
            $arr[] = array('value'=>$i, 'label'=>$i);
        }
        return $arr;
    }   
}
?>