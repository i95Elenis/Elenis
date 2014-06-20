<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Calendarbase_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{

    public function getCodes($arg)
    {
        $ref = array();

        foreach ($arg as $key => $v) {
            array_push($ref, $key);
        }
        return $ref;
    }

    public function getBlackoutDeliveryDays()
    {
        return Mage::helper('core')->jsonEncode(Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDays());
    }

    public function getBlackoutDeliveryDates()
    {
        $dates = Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDates();
        $pushedDates = array();
        Mage::helper('webshopapps_dateshiphelper')->getDateFormatString() == 'dd-mm-yy' ? $split = '-' : $split = '/';

        foreach ($dates as $date) {
            $splitDates[] = explode($split, $date);
            $pushedDates[] = $splitDates;
            unset($splitDates);
        }

        return Mage::helper('core')->jsonEncode($pushedDates);
    }

    public function getMinDate()
    {
        $earliestDate = $this->getAddress()->getEarliest();

        if ($earliestDate != '') {
            return $earliestDate;
        }

        // should never fall into here
        return date(Mage::helper('webshopapps_dateshiphelper')->getDateFormat(), Mage::app()->getLocale()->storeTimeStamp());
    }


    public function getShippingPrice($price, $flag)
    {
        if (Mage::helper('calendarbase')->showWholePrices()) {
            return preg_replace('(\.[0-9][0-9])', '', parent::getShippingPrice($price, $flag));
        } else {
            return parent::getShippingPrice($price, $flag);
        }
    }

    public function getInitialRates()
    {
        $address = $this->getAddress();
        $checkedCode = '';
        $selectedDate = '';
        $shippingRateGroups = $address->getGroupedAllShippingRates();
        $useUpsRates = Mage::helper('calendarbase')->useUPSRates();

        if ($address->getShippingMethod() != '') {
            // have pre-selected the rate, so use this
            $selectedDate = $this->getAddress()->getExpectedDelivery();
            $checkedCode = $address->getShippingMethod();
        } else {
            $price = 1000000;
            
             foreach ($shippingRateGroups as $code => $rates) {
                 $methods[]=$code;
             }
             $methods = json_encode($methods);
                     // Mage::log('Shipping code from getInitialRates $methods:'.$methods,'1','shipping_codes.log');
            foreach ($shippingRateGroups as $code => $rates) {
                if ($code == 'customcalendar' || ($useUpsRates && $code == 'ups')) { //code was originally calendarbase
                   //   Mage::log('Shipping code from getInitialRates:'.$code,'1','shipping_codes.log');
                    foreach ($rates as $rate) {
                        if ($rate->getPrice() < $price) {
                            $selectedDate = $rate->getExpectedDelivery();
                            $checkedCode = $rate->getCode();
                            $price = $rate->getPrice();
                        }
                    }
                    
                    
                }
                
            }
        }

        return $this->_buildResultSet($shippingRateGroups, $checkedCode, $selectedDate, $useUpsRates);
    }

    private function _buildResultSet($_shippingRateGroups, $checkedCode, $selectedDate, $useUpsRates)
    {
        //Mage::log(' _buildResultSet $useUpsRates'.json_encode($useUpsRates),'1','shipping_codes.log');
        $resultSet = array();
        
        foreach ($_shippingRateGroups as $code => $rates) {
          //   Mage::log('Shipping code from _buildResultSet outside:'.$code,'1','shipping_codes.log');
            if ($code == 'customcalendar' || ($useUpsRates && $code == 'ups')) {
             //    Mage::log('Shipping code from _buildResultSet inside:'.$code,'1','shipping_codes.log');
                foreach ($rates as $rate) {
                    if ($selectedDate != $rate->getExpectedDelivery()) {
                        continue;
                    }
                    $resultSet[] = array(
                        'code' => $rate->getCode(),
                        'price' => $this->getShippingPrice($rate->getPrice(), Mage::helper('tax')->displayShippingPriceIncludingTax()),
                        'method_description' => $rate->getMethodDescription(),
                        'checked' => $rate->getCode() === $checkedCode ? true : false,
                        'expected_delivery' => $selectedDate,
                    );
                }
            }
            
        }
 //       Mage::log("Shipping methods".$rate->getCode(),'1','shipping_codes1.log');
        return $resultSet;
    }


}