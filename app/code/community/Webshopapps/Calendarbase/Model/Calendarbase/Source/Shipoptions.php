<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_$(PROJECT_NAME)
 * User         joshstewart
 * Date         05/06/2013
 * Time         12:27
 * @copyright   Copyright (c) $(YEAR) Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, $(YEAR), Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_Calendarbase_Model_Calendarbase_Source_Shipoptions {
    public function toOptionArray()
    {
        $arr = array();
        $customcalendar = Mage::getSingleton('calendarbase/calendarbase')->getCode('shipoptions');

        foreach ($customcalendar as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('shipping')->__($v));
        }

        return $arr;
    }
}