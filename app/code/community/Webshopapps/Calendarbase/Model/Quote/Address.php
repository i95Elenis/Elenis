<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Calendarbase
 * User         joshstewart
 * Date         19/06/2013
 * Time         17:25
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_Calendarbase_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Collecting shipping rates by address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function collectShippingRates()
    {
        if (!$this->getCollectShippingRates()) {
            return $this;
        }

        $this->setCollectShippingRates(false);

        $this->removeAllShippingRates();

        if (!$this->getCountryId()) {
            return $this;
        }

        $request = Mage::getModel('shipping/rate_request');
        $request->setAllItems($this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        /**
         * need to call getStreet with -1
         * to get data in string instead of array
         */
        $request->setDestStreet($this->getStreet(-1));
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($this->getBaseSubtotal());
        $request->setPackageValueWithDiscount($this->getBaseSubtotalWithDiscount());
        $request->setPackageWeight($this->getWeight());
        $request->setPackageQty($this->getItemQty());
        $request->setFreeMethodWeight($this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        /*$request->setStoreId(Mage::app()->getStore()->getId());
        $request->setWebsiteId(Mage::app()->getStore()->getWebsiteId());*/

        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());

        $result = Mage::getModel('shipping/shipping')
            ->collectRates($request)
            ->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = Mage::getModel('sales/quote_address_rate')
                    ->importShippingRate($shippingRate);
                $this->addShippingRate($rate);

                if ($this->getShippingMethod() == $rate->getCode()) {
                    $this->setShippingAmount($rate->getPrice());
                    $this->setDispatchDate($rate->getDispatchDate());
                    $found = true;
                }
                if ($rate->getEarliest()) {
                    $this->setEarliest($rate->getEarliest());
                } elseif ($rate->getCarrier() == 'customcalendar' ||
                    $rate->getCarrier() == 'upscalendar') {
                    $this->setEarliest('');
                }
            }
        }

        if (!$found) {
            $this->setShippingAmount(0)
                ->setBaseShippingAmount(0)
                ->setShippingMethod('')
                ->setShippingDescription('')
                ->setDispatchDate('');
        }

        return $this;
    }
     public function getAllVisibleItems()
    {
        
        $items = array();
        foreach ($this->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $items[] = $item;
            }
        }
        return $items;
    }
    public function getAllMultipleVisisbleItems()
    {
        //echo "javed";echo "<pre>";print_r($parentId);die(get_class($this));$items = array();
        
        $items = array();
        foreach ($this->getAllItems() as $item) {
            echo $item->getAddressItemId()."=".$item->getParentItemId();echo "=<pre>";print_r($parentId);
           
                $items[] = $item;
           
        }
        return $items;
    }
}