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
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
 * Mustishipping checkout shipping
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Calendarbase_Block_Checkout_Multishipping_Shipping extends Mage_Checkout_Block_Multishipping_Shipping
{
	private $_currentAddress;

	public function setCurrentAddress($address){
		$this->_currentAddress = $address;
	}

	public function getAddress()
	{
		return $this->_currentAddress;
	}

	public function getCodes($arg)
	{
		$ref = array();
		foreach($arg as $key=>$v)
		{
			array_push($ref,$key);
		}
		return $ref;
	}


	public function getBlackoutDeliveryDays() {
		return Mage::helper('core')->jsonEncode(Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDays());
	}

	public function getBlackoutDeliveryDates() {
		$dates = Mage::helper('webshopapps_dateshiphelper')->getBlackoutDeliveryDates();
		$pushedDates=array();
		Mage::helper('webshopapps_dateshiphelper')->getDateFormatString() == 'dd-mm-yy' ? $split = '-' : $split = '/';

 		foreach ($dates as $date) {
 			$splitDates[]=explode($split,$date);
 			$pushedDates[]=$splitDates;
 			unset($splitDates);
 		}

		return Mage::helper('core')->jsonEncode($pushedDates);
	}

	public function getShippingPrice($address, $price, $flag) {
		if (Mage::helper('calendarbase')->showWholePrices()) {
			return preg_replace('(\.[0-9][0-9])','',parent::getShippingPrice($address, $price, $flag));
		}
		else {
			return parent::getShippingPrice($address, $price, $flag);
		}
	}


    public function getMinDate($address) {
        $earliestDate = $address->getEarliest();
 		if ($earliestDate!='') {
 			return $earliestDate;
 		}
 		return date("D M j G:i:s T Y",Mage::app()->getLocale()->storeTimeStamp());
    }

    public function getInitialRates($address)
    {
        $selectedDate = '';
        $checkedCode = '';
        $_shippingRateGroups = $address->getGroupedAllShippingRates();
        $useUpsRates = Mage::helper('calendarbase')->useUPSRates();

        if ($address->getShippingMethod() != '') {
            // have pre-selected the rate, so use this
            $selectedDate = $address->getExpectedDelivery();
            $checkedCode = $address->getShippingMethod();
        } else {
            $price = 1000000;
            foreach ($_shippingRateGroups as $code => $rates) {
                if ($code == 'customcalendar'  || ($useUpsRates && $code == 'ups')) {
                    foreach ($rates as $rate) {
                        if ($rate->getPrice() < $price) {
                            $selectedDate = $rate->getExpectedDelivery();
                            $checkedCode = $rate->getCode();
                            $price = $rate->getPrice();
                        }
                    }
                    break;
                }
            }
        }

        return $this->_buildResultSet($address, $_shippingRateGroups, $checkedCode, $selectedDate, $useUpsRates);
    }


    private function _buildResultSet($address,$_shippingRateGroups,$checkedCode,$selectedDate,$useUpsRates) {
    	$resultSet = array();
    	foreach ($_shippingRateGroups as $code => $rates) {
        	if ($code == 'customcalendar'  || ($useUpsRates && $code == 'ups')) {
	    	    foreach ($rates as $rate) {
                    if($selectedDate!=$rate->getExpectedDelivery()) {
                        continue;
                    }
                    $resultSet[] = array(
                        'code'					=> $rate->getCode(),
                        'price' 				=> $this->getShippingPrice($address,$rate->getPrice(), Mage::helper('tax')->displayShippingPriceIncludingTax()),
                        'method_description' 	=> $rate->getMethodDescription(),
                        'checked'				=> $rate->getCode()===$checkedCode ? true : false,
                        'expected_delivery'		=> $selectedDate,
                    );
                }
            }
	    	break;
    	}
        return $resultSet;
    }
    public function getAddressItems($address) {

        $items = array();
        
     
        
     
                            
            $quoteAddressItems = Mage::getModel('sales/quote_address_item')->getCollection()->addFieldToSelect('*')->addFieldToFilter('quote_address_id', $address->getId());
            foreach ($quoteAddressItems as $addressItem) {

                if ($addressItem->getQuoteAddressId() == $address->getId() && ($addressItem->getCheckMultiple() != 1 || $addressItem->getCheckMultiple() == NULL))
                {
                    $addressItem->setQuoteItem($this->getCheckout()->getQuote()->getItemById($addressItem->getQuoteItemId()));
                     $items[] = $addressItem;
                }
                
                   
            }
        //return $items;
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }
    public function getShippingRates($address)
    {
		//die(get_class($address));
		$address->setCollectShippingRates(true);
                $address->save();
		$address->collectShippingRates();
        $groups = $address->getGroupedAllShippingRates();
        return $groups;
    }

}
