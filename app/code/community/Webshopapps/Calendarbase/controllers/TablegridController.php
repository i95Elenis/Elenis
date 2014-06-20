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

class Webshopapps_Calendarbase_TablegridController extends Mage_Core_Controller_Front_Action
{

	private $_rates;
    protected $_address;

	/**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    protected $_checkoutSession;


    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError() //|| $this->getOnepage()->getQuote()->getIsMultiShipping()
            ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    public function getDayRateAction() {
    	if ($this->_expireAjax()) {
            return;
        }

        $useUpsRates = Mage::helper('calendarbase')->useUPSRates();

        $addressId=null;
        
        if ($this->getRequest()->isGet()) {
          $selectedDate = $this->getRequest()->getParam('date');
          $addressId 	= $this->getRequest()->getParam('address_id');
        }
        
        if (!is_null($addressId)) {
        	$this->_address = $this->getQuote()->getAddressById($addressId);
        }

        Mage::register('md_delivery_date', date('Ymd',strtotime($selectedDate)));
        
    	if (empty($this->_rates)) {
            if ($useUpsRates) {
                // need to go get rates again unfortunately
                $this->getAddress()->setCollectShippingRates(true);
            }
            $this->_rates = $this->getShippingRates();
        }
        $resultSet='';
    	foreach ($this->_rates as $code => $rates) {
            if ($code == 'customcalendar' || ($useUpsRates && $code == 'ups') ) {
                foreach ($rates as $rate) {
                    if($selectedDate!=$rate->getExpectedDelivery()) {
                        continue;
                    }
                    if($rate->getCode()=='ups_GND')
                    {
                    $resultSet[$rate->getCode()] = array(
                        //'code' 			=> ,
                        'price' 				=> $this->getShippingPrice($rate->getPrice(), Mage::helper('tax')->displayShippingPriceIncludingTax())."
<span style='font-size:11px;color:#E73785 !important' > (Delivery Date Not Guaranteed)</span>",

                    //	'method_title' 			=> $rate->getMethodTitle(),
                        'method_description' 	=> $rate->getMethodDescription(),
                    );
                    }
                    else{
                        $resultSet[$rate->getCode()] = array(
                        //'code' 			=> ,
                        'price' 				=> $this->getShippingPrice($rate->getPrice(), Mage::helper('tax')->displayShippingPriceIncludingTax()),

                    //	'method_title' 			=> $rate->getMethodTitle(),
                        'method_description' 	=> $rate->getMethodDescription(),
                    );
                    }
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($resultSet));

    }


    protected function getAddress()
    {
		if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }


  	protected function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }


    /**
     * This should be in model really
     */
    protected function getShippingRates()
    {
        $address = $this->getAddress();

        if (empty($this->_rates)) {
            $address->collectShippingRates()->save();

            $groups = $address->getGroupedAllShippingRates();

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }


    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout()
    {
     	if ($this->_quote === null) {
        	$this->_checkoutSession = Mage::getSingleton('checkout/session');
    	}
        return $this->_checkoutSession;
    }

    protected function getQuote()
    {
        if ($this->_quote === null) {
            return $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

   protected function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

}