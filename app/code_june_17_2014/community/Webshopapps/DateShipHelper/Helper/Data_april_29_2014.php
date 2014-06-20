<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_DateShipHelper
 * User         karen
 * Date         19/05/2013
 * Time         00:03
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_DateShipHelper_Helper_Data extends Mage_Core_Helper_Abstract {

    protected static $_dateFormat;
    protected static $_dateFormatString;
    protected static $_blackoutProductionDays;
    protected static $_blackoutProductionDates;
    protected static $_blackoutDeliveryDates;
    protected static $_blackoutDeliveryDays;
    protected static $_shortDateFormat;

    public static function resetStatics() {
        self::$_dateFormat = null;
        self::$_dateFormatString = null;
        self::$_blackoutProductionDays = null;
        self::$_blackoutProductionDates = null;
        self::$_blackoutDeliveryDates = null;
        self::$_blackoutDeliveryDays = null;
    }

    public function getDefaultSlots() {
        return Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/default_avail_slots');
    }

    public function dateDiff($dFormat, $beginDate, $endDate) {
        $date_parts1 = explode($dFormat, $beginDate);
        $date_parts2 = explode($dFormat, $endDate);
        $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
        $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
        return $end_date - $start_date;
    }

    public static function showUPSDates() {
        return Mage::getStoreConfigFlag('shipping/webshopapps_dateshiphelper/active');
    }

    public static function showUpsDateSeparately() {
        return false;
    }

    public static function getDateFormat() {
        if (self::$_dateFormat == NULL) {
            self::$_dateFormat =
                    Mage::getModel('webshopapps_dateshiphelper/dateShipHelper_source_dateformat')->getDateFormat(
                            Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/date_format'));
        }
        return self::$_dateFormat;
    }

    public static function getShortDateFormat() {
        if (self::$_shortDateFormat == NULL) {
            self::$_shortDateFormat =
                    Mage::getModel('webshopapps_dateshiphelper/dateShipHelper_source_dateformat')->getShortDateFormat(
                            Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/date_format'));
        }
        return self::$_shortDateFormat;
    }

    /**
     * Retrieve an array of numeric blackout days. 0=Sunday
     * @return array
     */
    public static function getBlackoutDeliveryDays() {
        if (self::$_blackoutDeliveryDays == NULL) {
            $configDeliveryDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/delivery_daysofweek');
            self::$_blackoutDeliveryDays = $configDeliveryDays == null ? array() : explode(",", $configDeliveryDays);
        }
        return self::$_blackoutDeliveryDays;
    }

    public static function getBlackoutDeliveryDates() {
        if (self::$_blackoutDeliveryDates == NULL) {
            $configDeliveryDates = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/delivery_dates');
            self::$_blackoutDeliveryDates = $configDeliveryDates == null ? array() : explode(",", $configDeliveryDates);
        }
        return self::$_blackoutDeliveryDates;
    }

    public function getNumTimeSlots() {
        return Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/num_slots');
    }

    public static function getBlackoutProductionDays() {
        if (self::$_blackoutProductionDays == NULL) {
            $configProductionDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/production_daysofweek');
            self::$_blackoutProductionDays = $configProductionDays == null ? array() : explode(",", $configProductionDays);
        }
        return self::$_blackoutProductionDays;
    }

    public static function getBlackoutProductionDates() {
        if (self::$_blackoutProductionDates == NULL) {
            $configProductionDates = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/production_dates');
            self::$_blackoutProductionDates = $configProductionDates == null ? array() : explode(",", $configProductionDates);
        }
        return self::$_blackoutProductionDates;
    }

    public function getLeadTimeDaysCount() {
        $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();

        foreach ($allAddresses as $address) {

            $allItems = $address->getAllItems();
            foreach ($allItems as $item) {
                $attrValue = Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('lead_time');
                $leadTime[] = $attrValue;
            }
        }
        $leadTime = array_unique($leadTime);
        $leadTime = array_filter($leadTime);
        $maxDays = max($leadTime);
        $productionDays = $maxDays;

        Mage::log('After updating ' . $productionDays, '1', 'leadtime.log');
        Mage::log('Lead Time array ' . json_encode($leadTime), '1', 'leadtime.log');
        return $productionDays;
    }
    public function getNextAvaiableDate() {
        $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();

        foreach ($allAddresses as $address) {

            $allItems = $address->getAllItems();
            foreach ($allItems as $item) {
                // $attrValue = Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('next_available_date');
                $prod = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                $attrValue = $prod->getResource()->getAttribute('next_available_date')->getFrontend()->getValue($prod);

                // echo "jj".$attrValue;
                $nextAvaiableDate[] = $attrValue;
            }
        }

        $nextAvaiableDate = array_unique($nextAvaiableDate);
        $nextAvaiableDate = array_filter($nextAvaiableDate);
        $maxDays = max($nextAvaiableDate);
        $productionDays = $maxDays;

        Mage::log('After updating ' . $productionDays, '1', 'ups-nextdeliverydate.log');
        Mage::log('Next Avaiable Date array ' . json_encode($nextAvaiableDate), '1', 'ups-nextdeliverydate.log');
        return date("d-m-Y", strtotime($productionDays));
    }
    public function getDispatchDay(&$dayCount, &$dispatchDate, $productionDays = 0, $cutOffTime = -1, $dateFormat=null) {
        $dispatchDate = date("N", Mage::app()->getLocale()->storeTimeStamp());

        if ($productionDays <= 0 && $cutOffTime == -1) {
            $productionDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/production_days');
            Mage::log('Before updating ' . $productionDays, '1', 'leadtime.log');
        }

       // $productionDays=$this->getLeadTimeDaysCount();
        Mage::log('ups-next ' . $this->getNextAvaiableDate(), '1', 'ups-nextdeliverydate.log');

        
      
        $currentDay = date("N", Mage::app()->getLocale()->storeTimeStamp());
        $dayCount = 0;
        $this->getPickupDayCount($productionDays, $cutOffTime, $dayCount);  // TODO: Check for true/false
        $pickupDay = ($currentDay + $dayCount) % 7;
        if ($pickupDay == 0) {
            $pickupDay = 7;
        }

        if (!$this->getDispatchDayCount($pickupDay, $dayCount)) {
            return null;
        }

        $dispatchDate = $this->getDate($dayCount, $dateFormat);

        return $pickupDay;
    }

    /**
     * Get a numerical representation of the day of the week from a date
     *
     * @param string $date
     * @return bool|string
     */
    public function getDayOfWeek($date) {
        $unixTime = strtotime($date);

        $dayOfWeek = date('N', $unixTime);

        return $dayOfWeek;
    }
    public function getSpecificDate()
    {
                  $firstDate = $this->getNextAvaiableDate();
        $convertDate = explode("-", $firstDate);
        $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0] , $convertDate[2]);
        $secondDate = date("d-m-Y");
        $convertDate = explode("-", $secondDate);
        $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);
        if ($startDate > $endDate) {
            $offset = $startDate - $endDate;
        }


        $actualDate = floor($offset / 60 / 60 / 24);
	return $actualDate;
    }
    public function getDate($dayCount, $dateFormat=NULL) {
        $newLeadTime=0;$newActualSpecificDate=0;
        if (is_null($dateFormat)) {
            $dateFormat = self::getDateFormat();
        }
         $newLeadTime=$this->getLeadTimeDaysCount();
         $newActualSpecificDate=$this->getSpecificDate();
          mage::log("test".$newActualSpecificDate.$newLeadTime,1,"test.log");
         if($newActualSpecificDate>$newLeadTime)
         {
             mage::log("one".$newActualSpecificDate.$newLeadTime,1,"test.log");
            return date($dateFormat, Mage::app()->getLocale()->storeTimeStamp()+(86400 * $this->getSpecificDate()));
         }
         if($newActualSpecificDate<=$newLeadTime)
         {
             mage::log("two".$newActualSpecificDate.$newLeadTime,1,"test.log");
             return date($dateFormat, Mage::app()->getLocale()->storeTimeStamp() + (86400 * $dayCount));
         }
        //return date($dateFormat, Mage::app()->getLocale()->storeTimeStamp() + (86400 * $dayCount));
    }

    public static function getDateFormatString() {
        switch (self::getDateFormat()) {
            case 'd-m-Y': self::$_dateFormatString = 'dd-mm-yy';
                break;
            case 'm/d/Y': self::$_dateFormatString = 'mm/dd/yy';
                break;
            case 'D d-m-Y': self::$_dateFormatString = 'D dd-mm-yy';
                break;
            default: self::$_dateFormatString = 'dd-mm-yy';
                break;
        }
        return self::$_dateFormatString;
    }

    public function getDispatchDayCount(&$pickupDay, &$dayCount, $rollback=false) {
        $validDateFound = false;
        $first = true;
        $i = 0;
        while ($validDateFound == false) {
            $validDateFound = true;
            if (self::getBlackoutDeliveryDays() != '') {
                if (!$this->getBlackoutDaysCount(self::getBlackoutDeliveryDays(), $pickupDay, $dayCount, $validDateFound, $rollback)) {
                    return false;
                }
            }

            if (self::getBlackoutDeliveryDates() != '' && ($first || !$validDateFound)) {
                $first = false;
                $pickupDay = $this->getDispatchDate(self::getBlackoutDeliveryDates(), $dayCount, $validDateFound, $rollback);
            } else {
                $validDateFound = true;
            }

            $i++;
            if ($i > 21) {
                break;
            }
        }
        return true;
    }

    public static function addBlackoutDeliveryDate($date) {
        self::$_blackoutDeliveryDates[] = $date;

        array_unique(self::$_blackoutDeliveryDates);
    }

    public static function addBlackoutDeliveryDay($day) {
        $dayArr = explode("-", $day);

        if (count($dayArr) > 1) {
            for ($i = $dayArr[1]; $i >= $dayArr[0]; $i--) {
                self::$_blackoutDeliveryDays[] = $i;
            }
        } else {
            self::$_blackoutDeliveryDays[] = $day;
        }
        array_unique(self::$_blackoutDeliveryDays);
    }

    protected function getPickupDayCount($productionDays, $cutoffTime = -1, &$dayCount=0, $rollback=false) {

        if ($cutoffTime == -1) {
            $cutoffTime = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/cutoff_time');
        }

        // see if blackout day or date
        $startProductionDay = date("N", Mage::app()->getLocale()->storeTimeStamp());
        if (!$this->getProductionDay($startProductionDay, $dayCount, $rollback)) {
            return false;
        }

        if ($dayCount == 0 && !empty($cutoffTime)) {
            $cutoffArr = explode(":", $cutoffTime);
            if (count($cutoffArr) == 2) {
                $cutoffMins = $cutoffArr[0] * 60 + $cutoffArr[1];
                $time = date("G:i", Mage::app()->getLocale()->storeTimeStamp());
                $timeArr = explode(":", $time);
                if (count($timeArr) == 2) {
                    $timeNowMins = $timeArr[0] * 60 + $timeArr[1];
                    if ($timeNowMins > $cutoffMins) {
                        $dayCount++;
                    }
                }
            }
        }

        if (!$this->getProductionDay($startProductionDay, $dayCount, $rollback)) {
            return false;
        }

        for ($i = 0; $i < $productionDays; $i++) {
            $dayCount++;
            if (!$this->getProductionDay($startProductionDay, $dayCount, $rollback)) {
                return false;
            }
        }

        return true;
    }

    protected function getProductionDay($productionDay, &$dayCount, $rollback=false) {

        $productionDay = $productionDay + $dayCount % 7;
        if ($productionDay == 0) {
            $productionDay = 7;
        }
        $validDateFound = false;
        $first = true;
        $i = 0;
        while ($validDateFound == false) {
            $validDateFound = true;
            if (self::getBlackoutProductionDays() != '') {
                if (!$this->getBlackoutDaysCount(self::getBlackoutProductionDays(), $productionDay, $dayCount, $validDateFound, $rollback)) {
                    return false;
                }
            }

            if (self::getBlackoutProductionDates() != '' && ($first || !$validDateFound)) {
                $first = false;
                $productionDay = $this->getDispatchDate(self::getBlackoutProductionDates(), $dayCount, $validDateFound, $rollback);
            } else {
                $validDateFound = true;
            }
            $i++;
            if ($i > 21) {
                break;
            }
        }
        return true;
    }

    public function getBlackoutDaysCount($blackDaysArr, &$day, &$dayCount, &$validDateFound, $rollback=false) {
        while (true) {
            if (in_array($day, $blackDaysArr)) {
                $day = $rollback ? ($day - 1) % 7 : ($day + 1) % 7;
                if ($day == 0) {
                    $day = 7;
                }
                $dayCount = $rollback ? $dayCount - 1 : $dayCount + 1;
                if ($dayCount > 200 || $dayCount < -200) {
                    return false;
                }
                $validDateFound = false;
            } else {
                break;
            }
        }

        return true;
    }

    /**
     *   TODO Unwind this as isnt a date, is a day. Too much reuse of same method names
     * @param array $blackoutDatesArr
     * @param int $dayCount
     * @param bool $validDateFound
     * @param bool $rollback
     * @return string
     */
    public function getDispatchDate($blackoutDatesArr, &$dayCount, &$validDateFound, $rollback = false) {
        $dateFormat = self::getDateFormat();
        $pickupDay = date($dateFormat, Mage::app()->getLocale()->storeTimeStamp() + (86400 * $dayCount));
        //$usTime=date('m/d/Y',Mage::app()->getLocale()->storeTimeStamp()+(86400*$dayCount));

        $validDateFound = true;

        $reset = true;
        while ($reset) {
            $reset = false;
            foreach ($blackoutDatesArr as $blackoutDate) {
                if ($blackoutDate == $pickupDay) {
                    $dayCount = $rollback ? $dayCount - 1 : $dayCount + 1;
                    $pickupDay = date($dateFormat, Mage::app()->getLocale()->storeTimeStamp() + (86400 * $dayCount));
                    //$usTime=date('m/d/Y',Mage::app()->getLocale()->storeTimeStamp()+(86400*$dayCount));
                    $reset = true;
                    $validDateFound = false;
                }
            }
        }
        return date("N", strtotime($pickupDay));
    }

    public function isValidDispatchDate($proposedDispatchDate) {

        // ensure isnt a blackout production date or blackout production day
        $productionDates = self::getBlackoutProductionDates();
        $productionDays = self::getBlackoutProductionDays();

        if (in_array($proposedDispatchDate, $productionDates)) {
            return false;
        }

        // get day from date
        $proposedDispatchDay = date("N", strtotime($proposedDispatchDate));

        if (in_array($proposedDispatchDay, $productionDays)) {
            return false;
        }

        return true;
    }

    /**
     * Says whether a calender is being used for output
     * @return bool
     */
    public function showCalendar() {
        return false;
    }

    public function getHighestProductionDayCount($items) {
        $highestProductionDays = 0;

        foreach ($items as $item) {
            $product = Mage::helper('wsacommon/shipping')->getProduct($item);

            if ($product->getProductionDays() > $highestProductionDays) {
                $highestProductionDays = $product->getProductionDays();
            }
        }

        return $highestProductionDays;
    }

}