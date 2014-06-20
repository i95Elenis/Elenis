<?php

/**
 * Customcalendar
 *
 * @category   Webshopapps
 * @package    Webshopapps_customcalendar
 * @copyright  Copyright (c) 20101 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Calendarbase_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * event checkout_type_onepage_save_order_after
	 */
	public function saveOrderAfter($observer){

		if (!Mage::getStoreConfig('carriers/customcalendar/active')) {
    	 	return ;
    	 }

    	 try {
    	 	Mage::helper('calendarbase')->reduceAvailSlot($observer);
    	 } catch (Exception $ex) {
    		Mage::helper('wsacommon/log')->postNotice('customcalendar',
    				'Exception whilst attmepting to reduce slots',$ex->getMessage());
    	 }

    	 try {
        	 $order = $observer->getEvent()->getOrder();
        	 $orders = $observer->getEvent()->getOrders(); //Multiaddress checkout returns array of order objects

             if(is_object($order)){
                 $orderArray = array($order);
             } else if (is_array($orders) && count($orders) > 0){
                 $orderArray = $orders;
             } else return;

        	 if(!is_object($orderArray)) { return; }

        	 foreach($orderArray as $order){
            	 $orderAttributes = array();
            	 $blackoutProdDates = Mage::helper('customcalendar')->getBlackoutProductionDates();
            	 $blackoutProdDays = Mage::helper('customcalendar')->getBlackoutProductionDays();
            	 $orderAttributes['delivery_packdate'] = $this->calculatePackdate($order);

            	 $holdDate = strtotime('-5 days', strtotime($orderAttributes['delivery_packdate']));

            	 while(in_array(date('m/d/Y', $holdDate), $blackoutProdDates) || in_array(date('N',$holdDate), $blackoutProdDays)){
            	     $holdDate -= 86400;
            	 }

            	 $dateFormat = Mage::helper('customcalendar')->getDateFormat();
            	 $orderAttributes['delivery_holddate'] = ($holdDate > strtotime(date('Ymd'))) ? date($dateFormat, $holdDate) : date($dateFormat);
            	 $orderAttributes['delivery_date'] = $order->getExpectedDelivery();

            	 if (is_array($orderAttributes)) {
            	     foreach ($orderAttributes as $name=>$value)
            	     {
            	         $order->setData($name, $value);
            	         $order->setData($name . '_is_formated', true);
            	     }
            	 }
            	 $order->save();
    	     }
    	 } catch (Exception $e){
    	     Mage::helper('wsacommon/log')->postNotice('customcalendar', 'Exception whilst attmepting to save hold date and packdate to order',$e->getMessage());
    	 }
	}

	private function calculatePackdate($order){
	    try {
    	    $dispatchString = $order->getDispatchDate();
    	    $expectedDeliveryString = $order->getExpectedDelivery();
    	    $expectedDeliveryTs = strtotime($expectedDeliveryString);
    	    $dispatchDate = '';
    	    $expectedDeliveryDate = '';
    	    $packDate = '';

    	    if($dispatchString != '' && $expectedDeliveryString != '') {
                $dispatchDate = new DateTime($dispatchString);
                $expectedDeliveryDate = new DateTime($expectedDeliveryString);

                $difference = $dispatchDate->diff($expectedDeliveryDate)->format('%a');

                if($difference > 0) {
                    if (date('W', $expectedDeliveryTs) <> date('W', strtotime('-'.$difference.'days', $expectedDeliveryTs))) {
                        $difference += 2;
                    }
                    $packDate = date('Y-m-d', strtotime('-'.$difference.'days', $expectedDeliveryTs));
                } else $packDate = date('Y-m-d', $expectedDeliveryTs);
    	    }

    	    return $packDate;
	    } catch (Exception $e) {
	        Mage::helper('wsacommon/log')->postNotice('customcalendar', 'Exception whilst attmepting to save hold date and packdate to order',$e->getMessage());
	    }
	}
}