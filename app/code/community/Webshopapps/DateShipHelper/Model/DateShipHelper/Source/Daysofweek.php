<?php
/** WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Exampleextension
 * User         karen baker
 * Date         18/05/2013
 * Time         23:41
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.webshopapps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */


class  Webshopapps_DateShipHelper_Model_DateShipHelper_Source_Daysofweek  {
	
    public function toOptionArray()  {

        $dateShipHelper = Mage::getSingleton('webshopapps_dateshiphelper/dateShipHelper');
        $arr = array();
        foreach ($dateShipHelper->getCode('days') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
?>