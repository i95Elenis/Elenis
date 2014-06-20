<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Customcalender
 * User         joshstewart
 * Date         19/06/2013
 * Time         17:24
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Calendarbase_Model_Quote_Address_Total_Shipping extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        $method = $address->getShippingMethod();

        if ($method) {
            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode() == $method) {
                    if ($rate->getExpectedDelivery()) {
                        $address->setExpectedDelivery($rate->getExpectedDelivery());
                    } else {
                        $address->setExpectedDelivery('');
                    }
                    if ($rate->getDispatchDate()) {
                        $address->setDispatchDate($rate->getDispatchDate());
                    } else {
                        $address->setDispatchDate('');
                    }
                    if ($rate->getEarliest()) {
                        $address->setEarliest($rate->getEarliest());
                    } else {
                        $address->setEarliest('');
                    }
                    if ($rate->getDeliveryDescription()) {
                        $address->setDeliveryDescription($rate->getDeliveryDescription());
                    } else {
                        $address->setDeliveryDescription('');
                    }
                    if ($rate->getDeliveryNotes()) {
                        $address->setDeliveryNotes($rate->getDeliveryNotes());
                    } else {
                        $address->setDeliveryNotes('');
                    }
                    break;
                }
            }
        }
        return $this;
    }
}