<?php

/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */

/**
 * @author Adjustware
 */
class AdjustWare_Deliverydate_Model_Holiday extends Mage_Core_Model_Abstract {

    protected $_holidays = null;
    protected $_weekend = null;

    public function _construct() {
        parent::_construct();
        $this->_init('adjdeliverydate/holiday');
    }

    public function getHolidays() {
        if (!is_null($this->_holidays))
            return $this->_holidays;

        $aHolidays = array();

        $hlp = Mage::helper('adjdeliverydate');

        $aYearList = $hlp->getYearOptions();

        unset($aYearList[-1]);

        $mon = array();
        for ($i = 1; $i <= 12; ++$i) {
            $mon[$i] = array();
        }

        foreach ($aYearList as $iYear) {
            $aHolidays[$iYear] = $mon;
        }

        $collection = $this->getCollection()
                ->addDateFilter()
                ->setPageSize(362)
                ->load();

        if (!$collection->count()) {
            $this->_holidays = $aHolidays;
            return $this->_holidays;
        }

        $currY = date('Y');
        $currM = date('n');

        foreach ($collection as $holiday) {
            if ($holiday->getY() == -1) {
                $aInsertYears = $aYearList;
            } else {
                $aInsertYears = array($holiday->getY());
            }

            foreach ($aInsertYears as $iYear) {
                if ($holiday->getM() == -1) {
                    $from = 1;
                    $to = 12;

                    for ($i = $from; $i <= $to; ++$i) {
                        $aHolidays[$iYear][$i][$holiday->getD()] = true;
                    }
                } else {
//                    if ($holiday->getM() != $currM) {
                    $aHolidays[$iYear][$holiday->getM()][$holiday->getD()] = true;
                }
            }
        }


        $this->_holidays = $aHolidays;
        return $this->_holidays;
    }

    public function getWeekend($asArray = false) {
        $w = Mage::getStoreConfig('checkout/adjdeliverydate/weekend');

        if (!$w)
            $w = 0;

        if ($asArray) {
            if (!$w) {
                $w = array(0);
            } else {
                $w = ($w ? explode(',', $w) : array());
            }
        }

        return $w;
    }

    protected function _getMoveDaysIntervalDependesHolidays($d, $m, $y) {
        // $minInterval = Mage::getStoreConfig('checkout/adjdeliverydate/min');

        $minInt = Mage::getStoreConfig('checkout/adjdeliverydate/min');

        $storePickup = Mage::app()->getRequest()->getParam('store_pickup');
        if ($storePickup == "storepickupmodule" && $this->getLeadTimeDaysCount() == 0) {
            $minInterval = 1;
        } else {
            $minInterval = ($minInt + $this->getLeadTimeDaysCount() + 1);
        }
        $includeOnlyBusinessDay = Mage::getStoreConfig('checkout/adjdeliverydate/include_holidays');

        $shift = 0;
        if ($includeOnlyBusinessDay) {
            for ($day = 1; $day <= $minInterval + $shift; $day++) {
                if ($this->isDayoff($d + $day, $m, $y)) {
                    $shift++;
                }
            }
            $day--;
            if ($minInterval == 1) {
                $day = $this->_checkNextDayDelivery($day);
            }
        } else {
            $day = $minInterval;
        }

        return $day;
    }

    protected function _checkNextDayDelivery($day) {
        list($hTomorrow, $iTomorrow, $sTomorrow) = explode(',', Mage::getStoreConfig('checkout/adjdeliverydate/nextday'));
        $last = sprintf('%02d:%02d:%02d', $hTomorrow, $iTomorrow, $sTomorrow);

        $currentDate = Mage::app()->getLocale()->date();

        $timeNow = $currentDate->toString('HH:mm:ss');

        if ($timeNow > $last) {
            $day++;
        }

        return $day;
    }

    protected function _checkTodayTomorrowDeliveryEnabled($i) {
        list($hToday, $iToday, $sToday) = explode(',', Mage::getStoreConfig('checkout/adjdeliverydate/sameday'));
        list($hTomorrow, $iTomorrow, $sTomorrow) = explode(',', Mage::getStoreConfig('checkout/adjdeliverydate/nextday'));

        $first = sprintf('%02d:%02d:%02d', $hToday, $iToday, $sToday);
        $last = sprintf('%02d:%02d:%02d', $hTomorrow, $iTomorrow, $sTomorrow);

        $currentDate = Mage::app()->getLocale()->date();
        $timeNow = $currentDate->toString('HH:mm:ss');

        if ($i == 0) {
            if ($timeNow > $first) {
                return true;
            }
        } else {
            if ($timeNow > $last) {
                return true;
            }
        }
        return false;
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

        Mage::log('After updating ' . $productionDays, '1', 'messenger-leadtime.log');
        Mage::log('Lead Time array ' . json_encode($leadTime), '1', 'messenger-leadtime.log');
        return $productionDays;
    }

    public function getFirstAvailableDate($format = 'Y-m-d') {
        $currentDate = Mage::app()->getLocale()->date();

        $d = (int) $currentDate->toString('d'); //date('j');
        $m = (int) $currentDate->toString('M'); //date('n');
        $y = (int) $currentDate->toString('Y'); //date('Y');

        $maxInterval = Mage::getStoreConfig('checkout/adjdeliverydate/max');

        $dayMove = $this->_getMoveDaysIntervalDependesHolidays($d, $m, $y);

        for ($i = $dayMove; $i < 365; /* days */ $i++) {
            if ($i > $maxInterval)
                return;

            if ($i < 2) {
                if ($this->_checkTodayTomorrowDeliveryEnabled($i)) {
                    continue;
                }
            }

            if ($this->isDayoff($d + $i, $m, $y))
                continue;


            //if($this->isDayoff($d+$i+$this->getLeadTimeDaysCount(), $m, $y))



            $time = mktime(0, 0, 0, $m, $d + $i, $y);

            if ($format == 'unix') {
                return $time;
            }

            return date($format, $time);
        }
    }

    /**
     *
     * @param int $d Day
     * @param int $m Month
     * @param int $y Year
     */
    public function isDayoff($d, $m, $y) {
        if (is_null($this->_weekend)) {
            $this->_weekend = $this->getWeekend(true);
        }
        $holiday = $this->getHolidays();

        $time = mktime(0, 0, 0, $m, $d, $y);
        if (in_array(date('w', $time), $this->_weekend))
            return true;
        if (array_key_exists(date('j', $time), $holiday[date('Y', $time)][date('n', $time)]))
            return true;
        return false;
    }

    public function isHoliday($date) {

        $time = strtotime($date);
        //echo $this->getLeadTimeDaysCount();

        if (date('Y-m-d', $time) < $this->getFirstAvailableDate())
            return true;

        $weekend = $this->getWeekend(true);
        $holiday = $this->getHolidays();

        if (in_array(date('w', $time), $weekend))
            return true;
        if (array_key_exists(date('j', $time), $holiday[date('Y', $time)][date('n', $time)]))
            return true;

        return false;
    }

}