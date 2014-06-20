<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Upscalendar
 * User         karen
 * Date         03/07/2013
 * Time         00:12
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_Upscalendar_Model_Usa_Shipping_Upstransit extends Varien_Object
{

    private static $_debug;
    protected $_timeInTransitArr = array();
    protected $_earliestDeliveryDate = null;

    public function _populateTimeInTransitValues($request,$rawRequest,$xmlAccessRequest,$debug)
    {
        self::$_debug = $debug;
        $dayCount = 0;
        $dispatchDate = '';
        $maxTransitDays = $this->_getMaxTransitDays($request->getAllItems());

        if (is_object($this->_getQuote()->getShippingAddress())) {
            $quoteShippingAddress = $this->_getQuote()->getShippingAddress();
            if (is_null(Mage::registry('md_delivery_date')) && $quoteShippingAddress->getExpectedDelivery() != '') {
                Mage::register('md_delivery_date', date('Ymd', strtotime($quoteShippingAddress->getExpectedDelivery())));
            }
        }
         Mage::log('count days ' . $dayCount."=".$dispatchDate, '1', 'temp1.log');
        Mage::helper('webshopapps_dateshiphelper')->getDispatchDay($dayCount, $dispatchDate, 0, -1, 'Ymd');
        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postInfo('upscalendar', 'Earliest Dispatch Date', $dispatchDate);
        }
        Mage::log('transit time ' . json_encode($rawRequest), '1', 'temp2.log');
        $this->_timeInTransitArr = Mage::getSingleton('upscalendar/usa_shipping_transit')->_getTimeInTransitArr(
            $rawRequest, $xmlAccessRequest, $dispatchDate, $this->_earliestDeliveryDate,
            $maxTransitDays);
      //  echo "<pre>";print_r($this->_timeInTransitArr);
       // die(get_class($this));
         Mage::log('transit time ' , '1', 'temp2.log');
    }

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     *
     * Work out the maximum delivery days, use the shortest day found
     * @param $items
     * @return int|mixed|string
     */
    protected function _getMaxTransitDays($items) {
        $storeMaxTransitDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/max_delivery_days');

        $useParent = true;
        $shortestMaxTransitDays = -1;
        foreach($items as $item) {

            if ($item->getParentItem()!=null &&
                $useParent ) {
                // must be a bundle
                $product = $item->getParentItem()->getProduct();

            } else if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !$useParent ) {
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $product=$child->getProduct();
                        break;
                    }
                }
            } else {
                $product = $item->getProduct();
            }

            $maxProductTransitDays = $product->getData('max_transit');
            if (is_numeric($maxProductTransitDays) && ($shortestMaxTransitDays == -1 || $maxProductTransitDays<$shortestMaxTransitDays)) {
                $shortestMaxTransitDays = $maxProductTransitDays;
            }
        }
        if ($shortestMaxTransitDays == -1) {
            $shortestMaxTransitDays = $storeMaxTransitDays;
        }
        Mage::log(json_encode($shortestMaxTransitDays),1,"hj1.log");
        return $shortestMaxTransitDays;

    }


    public function isSaturday(){
        if (count($this->_timeInTransitArr)){
            foreach ($this->_timeInTransitArr as $method) {
                if ($method['day'] == "SAT"){
                    return true;
                }
            }
        }
        return false;
    }


    public function refineRates($priceArr) {

        if (count($this->_timeInTransitArr)<1) {
            return array();
        }

        // get pickup day

        $finalRatesArr=array();

        foreach ($priceArr as $code=>$price) {
            if (array_key_exists($code,$this->_timeInTransitArr)) {
                $codeDates = $this->_timeInTransitArr[$code];
                $finalRatesArr[$codeDates['method']]=array (
                    'pickup' => $codeDates['pickup'],
                    'price' => $price,
                    'date'	=> $codeDates['date'],
                );
            }
        }
        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postInfo('upscalendar',
                'Rates after combining with Transit Allowed Dates',$finalRatesArr);
        }

        return $finalRatesArr;
    }


    public function getEarliestDeliveryDate() {
        return $this->_earliestDeliveryDate;
    }

    public function processRateResults($priceArr) {
        $dateFormat = Mage::helper('webshopapps_dateshiphelper')->getDateFormat();

        $newRates = $this->refineRates($priceArr);
        $earliestDeliveryDate = $this->getEarliestDeliveryDate();

        $result = Mage::getModel('shipping/rate_result');
        if (empty($newRates)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if(!isset($errorTitle)){
                $errorTitle = Mage::helper('usa')->__('Cannot retrieve shipping rates');
            }
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($newRates as $method=>$dateRates) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $method_arr = Mage::getModel('upscalendar/upscalendar')->getCode('method', $method);
                $rate->setMethodTitle(Mage::helper('usa')->__($method_arr).' - '.date($dateFormat,
                    strtotime($dateRates['date'])));
                $rate->setEarliest($earliestDeliveryDate);
                $rate->setDispatchDate(date($dateFormat,strtotime($dateRates['pickup'])));
                $rate->setExpectedDelivery(date($dateFormat,strtotime($dateRates['date'])));
                $rate->setMethodDescription($method_arr);
                $rate->setCost(0);
                //  $rate->setCost($costArr[$method]);
                $rate->setPrice($dateRates['price']);
                $result->append($rate);
            }
        }
        if (self::$_debug) {
            Mage::helper('wsalogger/log')->postInfo('upscalendar',
                'Final Results',$result);
        }
        return $result;
    }

}