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
 * @package    Mage_Sales
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



class Webshopapps_Calendarbase_Helper_Data extends Mage_Core_Helper_Data
{

    private static $_numOfWeeks;
    private static $_numOfDatesAtCheckout;
    private static $_debug;
    private static $_showWholePrices;

    public static function resetStatics() {
        Mage::helper('webshopapps_dateshiphelper')->resetStatics();

        self::$_numOfWeeks = null;
        self::$_numOfDatesAtCheckout = null;
    }

    public static function isDebug()
    {
        if (self::$_debug==NULL) {
            self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Calendarbase');
        }
        return self::$_debug;
    }


    /**
     * Used in phtml to determine if to display information text
     * @return bool
     * @deprecated since 2.0 - Use showCustomText()
     */
    public function showInformationText()
    {
        return Mage::getStoreConfig('carriers/calendarbase/active') && in_array('show_information',
            explode(',',Mage::getStoreConfig("carriers/calendarbase/ship_options")));
    }


    public function getTimeSlots() {

        $arr = array();
        $arr[] = array('value'=>0, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_1')));
        $arr[] = array('value'=>1, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_2')));
        $arr[] = array('value'=>2, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_3')));
        $arr[] = array('value'=>3, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_4')));
        $arr[] = array('value'=>4, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_5')));
        $arr[] = array('value'=>5, 'label'=>Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_6')));
        return $arr;
    }

    public function getTimeSlotOptions() {

        return array (
            '0' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_1')),
            '1' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_2')),
            '2' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_3')),
            '3' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_4')),
            '4' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_5')),
            '5' => Mage::helper('calendarbase')->__(Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_6')),
        );
    }

    public function getTimeSlotArr($code='') {

        $timeSlots=array ();
        $numTimeSlots = Mage::helper('webshopapps_dateshiphelper')->getNumTimeSlots();

        for ($i=1; $i <= $numTimeSlots; $i++) {
            $timeSlots[] = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/slot_'.$i);
        };

        if (''===$code) {
            return $timeSlots;
        }
        return $timeSlots[$code];
    }

    /**
     * returns the date of dispatch given the expected delivery date and number of delivery days
     * Counts backwards
     * Assumes that a blackout production day and a blackout production date mean cant be sent on that date
     * Assumes that a blackout delivery day/date means cant be delivered on that date
     * @param $expectedDeliveryDate
     * @param int $numDeliveryDays
     * @param String $dateFormat
     * @internal param int $dayCount number of days from first available pickup date
     * @internal param String $newExpectedDeliveryDate
     * @return String $dispatchDate
     */
    public function getRollbackDispatchDate($expectedDeliveryDate,$numDeliveryDays,$dateFormat)
    {
        $expPickupTime = strtotime('-'.$numDeliveryDays.' day', strtotime($expectedDeliveryDate));
        $expPickupDayOfWeek = date("N",$expPickupTime);
        $todayDate = date($dateFormat);
        $midnightToday = strtotime($todayDate);

        //Time of day matters only on sameday deliveries
        if($expectedDeliveryDate == $todayDate) {
            $dayCount = floor(($expPickupTime - time())/(60*60*24));
        } else {
            $dayCount = floor(($expPickupTime - $midnightToday)/(60*60*24));
        }

        if (!Mage::helper('webshopapps_dateshiphelper')->getDispatchDayCount($expPickupDayOfWeek,$dayCount,true)) {
            return date($dateFormat,strtotime($expectedDeliveryDate. ' -'.$numDeliveryDays.' day'));
        }

        if (!$this->getFreeDispatchDay($dayCount)) {
            return date($dateFormat,strtotime($expectedDeliveryDate. ' -'.$numDeliveryDays.' day'));
        }

        $dispatchDate=Mage::helper('webshopapps_dateshiphelper')->getDate($dayCount);

        return $dispatchDate;
    }

    /**
     * Used when reverse finding the dispatch day. Want a clear day which isnt a blackout production day/date
     * @param $dayCount
     * @return bool
     */
    private function getFreeDispatchDay(&$dayCount) {
        $i=0;
        $validDateFound=false;
        $first=true;
        $suggestedDispatchDay=$this->getDay($dayCount);
        $blackoutProductionDays = Mage::helper('webshopapps_dateshiphelper')->getBlackoutProductionDays();
        $blackoutProductionDates = Mage::helper('webshopapps_dateshiphelper')->getBlackoutProductionDates();

        while ($validDateFound==false) {
            $validDateFound=true;
            if ($blackoutProductionDays != '') {
                if (!Mage::helper('webshopapps_dateshiphelper')->getBlackoutDaysCount($blackoutProductionDays,$suggestedDispatchDay,$dayCount,$validDateFound,true)) {
                    return false;
                }
            }

            if ($blackoutProductionDates !='' && ($first || !$validDateFound)) {
                $first=false;
                $suggestedDispatchDay=Mage::helper('webshopapps_dateshiphelper')->getDispatchDate($blackoutProductionDates,$dayCount,$validDateFound,true);
            } else {
                $validDateFound=true;
            }
            $i++;
            if ($i>21) {
                break;
            }
        }


        return $validDateFound;
    }

    private function getDay($dayCount) {
        return date("N",Mage::app()->getLocale()->storeTimeStamp()+(86400*$dayCount));
    }

    public static function getNumOfWeeks() {
        if (self::$_numOfWeeks==NULL) {
            self::$_numOfWeeks = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/num_of_weeks');
        }
        return self::$_numOfWeeks;
    }


    public function getWorkingDate($dispatchDay,$dispatchDayCount,$numDaysToAdd,&$deliverDay) {
        $tempDispatchDay=$dispatchDay;
        $tempDispatchDayCount=$dispatchDayCount;
        for ($i=0;$i<$numDaysToAdd;$i++) {
            $tempDispatchDayCount++;
            $tempDispatchDay=($tempDispatchDay+1) %7;
            if ($tempDispatchDay==0) {
                $tempDispatchDay=7;
            }
            Mage::helper('webshopapps_dateshiphelper')->getDispatchDayCount($tempDispatchDay,$tempDispatchDayCount);
        }
        $deliverDay=$this->getDay($tempDispatchDayCount);
        return Mage::helper('webshopapps_dateshiphelper')->getDate($tempDispatchDayCount);
    }

    public function reduceAvailSlot($observer) {
        $orders = $observer->getEvent()->getOrder();
        if (!is_object($orders)) {
            $orders = $observer->getEvent()->getOrders();
            if(!is_array($orders)){
                return;
            }
        }

        !is_array($orders) ? $orders = array($orders) : false;

        foreach($orders as $order){
            $shippingMethod = $order->getShippingMethod();
            $parts = explode('_',$shippingMethod);
            $partsCounter = count($parts)-1; //Lets use this as a index

            if ($partsCounter < 2 || $parts[0]!='customcalendar') {
                return;
            }
            $slot=$parts[$partsCounter-1];
            $date=$parts[$partsCounter];
            $day=date('N',strtotime($date));

            $dispatchDate = $order->getDispatchDate();
            $dispatchDay = date('N',strtotime($dispatchDate));

            $expectedDeliveryDate = $order->getExpectedDelivery();
            if ($expectedDeliveryDate=='') {
                return;
            }
            $previousMonday=$this->getPreviousMonday($expectedDeliveryDate);
            $collection = Mage::getModel('timegrid/timegrid')->getCollection()
                ->setSlot($slot)
                ->setWeekCommencing($previousMonday);
            $noTimeSlot=false;
            $timeGrid = $collection->getData();

            if (count($timeGrid)<1) {
                // create new one using default
                $collection = Mage::getModel('timegrid/timegrid')->getCollection()
                    ->setSlot($slot)
                    ->setWeekCommencing('0000-00-00');
                $noTimeSlot=true;
                $timeGrid = $collection->getData();
            }

            // update available slot
            if ($this->isDebug()) {
                Mage::helper('wsalogger/log')->postInfo('calendarbase','Order Shipping Method',$shippingMethod);
                Mage::helper('wsalogger/log')->postInfo('calendarbase','Order Available Slots',$timeGrid[0][$day.'_slots']);
            }

            if(!is_array($timeGrid)) return;

            $id=$timeGrid[0]['timegrid_id'];
            $timeSlotModel = Mage::getModel('timegrid/timegrid');
            $timeSlotModel->load($id);
            if (!is_object($timeSlotModel)) {
                return;
            }

            $slotDecrement = true;
            $dispatchDecrement = true;

            //No slot limit set
            if($timeSlotModel[$day.'_slots'] == -1 || $timeSlotModel[$day.'_slots'] == 0) {
                $slotDecrement = false;
            } else {
                $timeSlotModel[$day.'_slots']=$timeSlotModel[$day.'_slots']-1;
            }

            //No dispatch slot limit set
            if($timeSlotModel[$dispatchDay.'_dispatch'] == -1 || $timeSlotModel[$dispatchDay.'_dispatch'] == 0) {
                $dispatchDecrement = false;
            } else {
                $timeSlotModel[$dispatchDay.'_dispatch']=$timeSlotModel[$dispatchDay.'_dispatch']-1;
            }

            if(!$slotDecrement && !$dispatchDecrement) {
                return null;
            }

            if ($noTimeSlot) {
                $model = Mage::getModel('timegrid/timegrid');
                $model->setData($timeSlotModel->getData());
                $model->setTimegridId();
                $model['week_commencing'] = $previousMonday;

                $model->save();
            } else {
                $id=$timeGrid[0]['timegrid_id'];
                $timeSlotModel = Mage::getModel('timegrid/timegrid');
                $timeSlotModel->load($id);

                if (!is_object($timeSlotModel)) {
                    return;
                }

                if($slotDecrement) {
                    $timeSlotModel[$day.'_slots']=$timeSlotModel[$day.'_slots']-1;
                } else if ($dispatchDecrement) {
                    $timeSlotModel[$dispatchDay.'_dispatch']=$timeSlotModel[$dispatchDay.'_dispatch']-1;
                }

                $timeSlotModel->setId($id)->save();
            }
        }
    }

    public function getPreviousMonday($date) {
        $dayofWeek = date("w",strtotime($date));
        if ($dayofWeek == 0) {
            $adjuster = 6;
        } else {
            $adjuster = $dayofWeek - 1;
        }
        return date('Y-m-d',strtotime($date . "-" .$adjuster. " days"));
    }

    public static function getNumOfDatesAtCheckout() {
        if (self::$_numOfDatesAtCheckout==NULL) {
            self::$_numOfDatesAtCheckout = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/dates_at_checkout');
        }
        return self::$_numOfDatesAtCheckout;
    }

    public static function showWholePrices() {
        if (self::$_showWholePrices==NULL) {
            $_options = explode(',',Mage::getStoreConfig("carriers/calendarbase/ship_options"));
            self::$_showWholePrices =  in_array('show_whole_prices',$_options);
        }
        return self::$_showWholePrices;
    }

    /**
     * Should the custom text be displayed
     * @return bool
     */
    public function showCustomText()
    {
        return Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/active') && in_array('show_information',
            explode(',',Mage::getStoreConfig("shipping/webshopapps_dateshiphelper/ship_options")));

    }

    public function getCustomText1($address)
    {
        $customText = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/custom_text_1');
        $prodDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/production_days');
        $itemProdDays = 0;

        $items = $address->getAllItems();

        if(is_array($items)) {
            $itemProdDays = Mage::helper('webshopapps_dateshiphelper')->getHighestProductionDayCount($items);
        }

        if($itemProdDays > $prodDays) {
            $prodDays = $itemProdDays;
        }

        $customText = str_replace("%PROD_DAYS%",$prodDays,$customText);
        return $customText;
    }

    /**
     * @param $address - Mage_Sales_Quote_Address
     * @return string
     */
    public function getCustomText2($address)
    {
        $customText = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/custom_text_2');
        $dispatchDate = $address->getEarliest();

        $customText = str_replace("%END_DATE%",$dispatchDate,$customText);

        return $customText;
    }



    public function useUPSRates() {
        if (!Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/active')  ||
            Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Customcalendar','carriers/customcalendar/active')) {
            return false;
        }
        return true;
    }


}