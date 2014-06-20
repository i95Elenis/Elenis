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

    public function getMessengerData($pkId) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('matrixrate_shipping/matrixrate');
        $select = $readConnection->select()
                        ->from($table, array('*'))
                        ->where('pk=?', $pkId);

        $rowsArray = $readConnection->fetchRow($select);
        //echo "<pre>";print_r($rowsArray);exit;
        return $rowsArray['delivery_type'];
    }

    public function _getMoveMultipleDaysIntervalDependsHolidays($d, $m, $y, $noOfDays) {
        // $minInt = Mage::getStoreConfig('checkout/adjdeliverydate/min');

        //$storePickup = Mage::app()->getRequest()->getParam('store_pickup');

        $minInterval = 0;
        //if ($storePickup == "storepickupmodule" && $this->getLeadTimeDaysCount() == 0) {
           // $minInterval = $noOfDays;
        //} else {
            $minInterval = ($noOfDays + $minInterval );
        //}
        $firstDate = $this->getNextAvaiableDate();
        $convertDate = explode("-", $firstDate);
        $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0] + 1, $convertDate[2]);
        $secondDate = date("d-m-Y");
        $convertDate = explode("-", $secondDate);
        $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);
        if ($startDate > $endDate) {
            $offset = $startDate - $endDate;
        }


        $actualDate = floor($offset / (60 * 60 * 24));


        Mage::log("diff difference=" . $actualDate . "-" . $minInterval, 1, "javed.log");
        if ($actualDate > $minInterval) {
            $days = $this->getDateForNextAvailableDays($this->getNextAvaiableDate(), $actualDate);
            Mage::log("date=" . $actualDate . "-" . $minInterval, 1, "javed.log");
            return $days;
            //  return $actualDate;
        } else {
            Mage::log("date2=" . $actualDate . "-" . $minInterval, 1, "javed.log");
            $minInterval = $minInterval;
            /* $firstDate = $this->getNextAvaiableDate();

              $convertDate = explode("-", $firstDate);
              $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0]+1 , $convertDate[2]);
              $secondDate = date("d-m-Y");
              $convertDate = explode("-", $secondDate);
              $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);
              if ($startDate > $endDate) {
              $offset = $startDate - $endDate;
              }
              $actualDate = floor($offset / 60 / 60 / 24);
              if ($actualDate > $minInterval) {
              return $actualDate;
              }
              else {
              $minInterval = $minInterval; */
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
                    Mage::log("javv" . $day, 1, "jj.log");
                    $day = $this->_checkNextDayDelivery($day);
                    Mage::log("javv" . $day, 1, "jj.log");
                }
            } else {

                $day = $minInterval;
            }
            return $day;
        }
    }

    protected function _getMoveDaysIntervalDependesHolidays($d, $m, $y, $noOfDays) {
        // $minInterval = Mage::getStoreConfig('checkout/adjdeliverydate/min');
        //$minInterval=$this->getLeadTimeDaysCount();
        $minInt = Mage::getStoreConfig('checkout/adjdeliverydate/min');

        //$this->getConfigSettingAtProductLevel();
        $deliveryData = "";
        $minInterval = 0;
        //$storePickup = Mage::app()->getRequest()->getParam('store_pickup');
        // $shippingData = Mage::app()->getRequest()->getPost('shipping_method');
        // $shippingMethod = explode("_", $shippingData);
        //  Mage::log($code,1,"javed2.log");


        /* $shippingType = Mage::app()->getRequest()->getControllerName();
          $customShippingMethods = Mage::getSingleton('core/session')->getcustomShippingMethod();
          $timeTo = Mage::getStoreConfig('checkout/adjdeliverydate/time_enable_to');
          $currentDate = Mage::app()->getLocale()->date();
          $timeNow = $currentDate->toString('HH');
          //echo "jkh".$timeNow."=".$timeTo."=".$code;
          // echo "javed".$countedDays;
          //echo "<pre>";print_r(Mage::getSingleton('core/session')->getcustomShippingMethod());
          //print_r($shippingMethod);
          //echo "jhj".$this->getNextAvaiableDate();
          //  if (!($this->getLeadTimeDaysCount())) {
          /*if ($storePickup == "storepickupmodule") {
          $minInterval = 1;
          } */
        //if ($code == 'matrixrate') {
        //  if($shippingType=='onepage')
        // {
        // $timeFrom=Mage::getStoreConfig('checkout/adjdeliverydate/time_enable_from');
        //echo "<pre>";print_r($customShippingMethods);


        /* if (strpos($customShippingMethods, $messengerConfig) !== false) {
          //echo date("G").date_default_timezone_get();
          // date_default_timezone_set('America/New_York');
          //echo Mage::getStoreConfig('checkout/adjdeliverydate/sameday');
          // echo $timeInfo[0];
          // echo "jj".$h;
          //echo "mage:=".$timeNow.$timeTo;
          if ($timeNow > $timeTo) {
          $minInterval = 1;
          // echo "javed1";
          }
          if ($timeNow < $timeTo) {
          $minInterval = 1;
          // echo "javed2";
          }
          }if (strpos($customShippingMethods, "Next Day Delivery") !== false) {

          $minInterval = 2;
          }

          } */

        $minInterval = $noOfDays;
        $shippingType = Mage::app()->getRequest()->getControllerName();

        // if ($shippingType != 'multishipping') {
        if ($this->getConfigSettingAtProductLevel()) {
            $configurableSettingValues = $this->getConfigSettingAtProductLevel();
            foreach ($configurableSettingValues as $configVal) {
                //Mage::log("check=".$configVal,1,"javed.log");
                if ($configVal == 'Lead Time') {
                    $leadTimeDays = $this->getLeadTimeDaysCount();
                    if ($leadTimeDays && $leadTimeDays > 0) {

                        $minInterval = $minInterval + $leadTimeDays;
                    }
                }
                if ($configVal == 'Next Available Date') {
                    $firstDate = $this->getNextAvaiableDate();
                }
            }
        }
        $convertDate = explode("-", $firstDate);
        $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0] + 1, $convertDate[2]);
        $secondDate = date("d-m-Y");
        $convertDate = explode("-", $secondDate);
        $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);
        if ($startDate > $endDate) {
            $offset = $startDate - $endDate;
        }


        $actualDate = floor($offset / (60 * 60 * 24));


        Mage::log("diff difference=" . $actualDate . "-" . $minInterval . $configVal, 1, "javed2.log");
        if ($actualDate > $minInterval) {
            $days = $this->getDateForNextAvailableDays($this->getNextAvaiableDate(), $actualDate);
            //Mage::log("date=" . $actualDate . "-" . $minInterval, 1, "javed.log");
            return $days;
            //  return $actualDate;
        } else {
            //  Mage::log("date2=" . $actualDate . "-" . $minInterval, 1, "javed.log");
           $minInterval = $minInterval;
            //echo "kk=".
            //exit;
            $includeOnlyBusinessDay = Mage::getStoreConfig('checkout/adjdeliverydate/include_holidays');
            //  Mage::log("days090=" . $shift, 1, "javed2.log");
            //if ($shippingType != 'multishipping') {
                $shift = 0;
                if ($includeOnlyBusinessDay) {
                    for ($day = 1; $day <= $minInterval + $shift; $day++) {
                        if ($this->isDayoff($d + $day, $m, $y)) {
                            $shift++;
                            //Mage::log("shift=" . $shift, 1, "javed2.log");
                        }
                    }
                    $day--;
                    Mage::log("days090=" . $day, 1, "javed2.log");
                    if ($minInterval == 1) {
                        $day = $this->_checkNextDayDelivery($day);
                        //   Mage::log("date3=" . $day, 1, "javed2.log");
                    }
                } else {

                    $day = $minInterval;
                    // Mage::log("date4=" . $day, 1, "javed2.log");
                }
            //}
            //if ($shippingType == 'multishipping') {
              /*  $shift = 0;
                if ($includeOnlyBusinessDay) {
                    for ($day = 1; $day <= $minInterval + $shift; $day++) {
                        if ($this->isDayoff($d + $day, $m, $y)) {
                            $shift++;
                            //Mage::log("shift=" . $shift, 1, "javed2.log");
                        }
                    }
                    return $day--;
                    Mage::log("days090=" . $day, 1, "javed2.log");
                    if ($minInterval == 1) {
                       return $day = $this->_checkNextDayDelivery($day);
                        //   Mage::log("date3=" . $day, 1, "javed2.log");
                    }
                } else {

                    $day = $minInterval;
                    // Mage::log("date4=" . $day, 1, "javed2.log");
                }*/
           // }
            return $day;
        }
        //  }
        /*  if ($shippingType == 'multishipping') {
          $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();
          $configSetting = array();
          foreach ($allAddresses as $address) {
          $allItems = $address->getAllItems();
          foreach ($allItems as $item) {
          $prod = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
          $attrValue = $prod->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($prod);
          if ($attrValue == "Lead Time") {
          //Mage::log("set=".$aValue."=".$attrValue,1,"multishipping.log");
          $aValue1 = $prod->getResource()->getAttribute('lead_time')->getFrontend()->getValue($prod);
          $minInterval = $aValue1 + $minInterval;
          }
          if ($attrValue == "Next Available Date") {
          //Mage::log("set=".$aValue."=".$attrValue,1,"multishipping.log");
          $aValue2 = $prod->getResource()->getAttribute('next_available_date')->getFrontend()->getValue($prod);
          $minInterval = $aValue2 + $minInterval;
          }

          // Mage::log("set=".$aValue1."=".$aValue2,1,"multishipping.log");
          }
          }


          $minInterval = $minInterval;

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
         */


        //$firstDate = $this->getNextAvaiableDate();
        // echo "test".$minInterval;
        /* } elseif (strpos($shippingData, 'matrixrate') !== false) {
          $matrixShippingFlag = true;
          date_default_timezone_set('America/New_York');
          //echo date_default_timezone_get();
          $messengerTime = date("G");
          $messengerType = $this->getMessengerData($shippingMethod[2]);
          //echo $messengerType;
          $messengerConfig=Mage::getStoreConfig('elenissec/elenisgrp/delivery_type_handling_request');
          if (strpos($messengerType, $messengerConfig) !== false) {
          if ($messengerTime < 16) {
          $minInterval = ($minInt + $this->getLeadTimeDaysCount() - 1);
          //echo "javed" . $minInterval;
          $minInterval = ($minInt + $this->getLeadTimeDaysCount() );
          //echo "javed1".$minInterval;
          } else {
          $minInterval = ($minInt + $this->getLeadTimeDaysCount() );
          }
          }
          } */
        // }
        /* if (($this->getLeadTimeDaysCount())) {
          echo "leadtime came";
          } */
        //$configSet = $this->getConfigSettingAtProductLevel();
        // Mage::log("dd" . json_encode($configSet), 1, "kk.log");
        /* $firstDate = $this->getNextAvaiableDate();
          if ($firstDate) {
          // echo $firstDate . "=" . $minInterval;
          $convertDate = explode("-", $firstDate);
          $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0] + 1, $convertDate[2]);
          $secondDate = date("d-m-Y", strtotime(Mage::app()->getLocale()->date()));
          // echo $secondDate;
          $convertDate = explode("-", $secondDate);
          $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);

          if ($startDate > $endDate) {
          $offset = $startDate - $endDate;
          }

          $actualDate = floor($offset / (60 * 60 * 24));
          //echo "ll" . $actualDate . "=" . $minInterval;
          if ($actualDate > $minInterval) {

          return $actualDate;
          } else {
          $minInterval = $minInterval;
          $includeOnlyBusinessDay = Mage::getStoreConfig('checkout/adjdeliverydate/include_holidays');

          $shift = 0;
          if ($includeOnlyBusinessDay) {
          for ($day = 1; $day <= $minInterval + $shift; $day++) {
          if ($this->isDayoff($d + $day, $m, $y)) {
          $shift++;
          }
          }
          $day--;
          if ($minInterval <= 1) {
          $day = $this->_checkNextDayDelivery($day);
          }
          } else {

          $day = $minInterval;
          }
          }
          // echo "kk".$day;

          return $day;
          } */
    }

    //echo $minInterval
    //$second_date=$this->formatCutsomDate(date("d-m-Y"));
    // echo "hjh".$first_date."=".$second_date;
    // $first_date = mktime(12, 0, 0, 1, 1, 2005);
    //$second_date = mktime(12, 0, 0, 1, 27, 2005);
    //  $offset = $second_date - $first_date;
    //echo floor($offset / 60 / 60 / 24) . " days";
    //echo $minInterval;$this->getNextAvaiableDate();
    // echo "jja".$storePickup;


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
        //echo "<br/>first".$first;
        //echo "<br/>last".$last;
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

    /* public function getFlagEnabled() {
      $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();

      foreach ($allAddresses as $address) {

      $allItems = $address->getAllItems();
      foreach ($allItems as $item) {
      $attrValue = Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('flag_leadtime_nextavaiabledate');
      $flagEnabled[] = $attrValue;
      }
      }

      $flagEnabled = array_filter(array_unique($leadTime));
      echo "<pre>";print_r($flagEnabled);

      return true;
      } */

    public function getLeadTimeDaysCount() {

        $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();
        $leadTime = array();

        foreach ($allAddresses as $address) {

            $allItems = $address->getAllItems();
            foreach ($allItems as $item) {

                //   $leadTime[] = $attrValue;
                //    $attValConfigSetting=Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('flag_leadtime_nextavaiabledate');
                //   $configSetting[]=(($attValConfigSetting)=='Lead Time'?$attValConfigSetting:"");
                $prod = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                $attributeValue = $prod->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($prod);

                if ($attributeValue == 'Lead Time') {
                    $attrValue = Mage::getModel('catalog/product')->load($prod->getId())->getAttributeText('lead_time');
                    //   Mage::log("hh=".$prod->getId()."=".$attrValue,1,'javed.log');
                    $leadTime[] = $attrValue;
                }
            }
        }

        $leadTime = array_filter($leadTime);
        $leadTime = array_unique($leadTime);
        // $configSetting=array_unqiue(array_filter($configSetting));
        // $leadTime = array_filter($leadTime);
        $maxDays = max($leadTime);

        $productionDays = $maxDays;

        Mage::log('After updating ' . $productionDays, '1', 'messenger-leadtime.log');
        Mage::log('Lead Time array ' . json_encode($leadTime), '1', 'messenger-leadtime.log');
        return $productionDays;
    }

    public function getNextAvaiableDate() {
        $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();

        $nextAvaiableDate = array();
        foreach ($allAddresses as $address) {

            $allItems = $address->getAllItems();
            foreach ($allItems as $item) {
                // $attrValue = Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('next_available_date');
                $prod = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                $attrValue = $prod->getResource()->getAttribute('next_available_date')->getFrontend()->getValue($prod);
                $attributeValue = $prod->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($prod);
                if ($attributeValue == 'Next Available Date') {

                    $nextAvaiableDate[] = date("Y-m-d", strtotime($attrValue));
                    //Mage::log("hh1=".date("Y-m-d", strtotime($attrValue)),1,'javed.log');
                }
                // echo "jj".$attrValue;
                // $nextAvaiableDate[] = $attrValue;
                // $attValConfigSetting=Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getAttributeText('flag_leadtime_nextavaiabledate');
                //$configSetting[]=(($attValConfigSetting)=='Next Available Date'?$attValConfigSetting:"");
            }
        }
        $nextAvaiableDate = array_filter($nextAvaiableDate);
        $nextAvaiableDate = array_unique($nextAvaiableDate);
        /* date_default_timezone_set("GMT");

          $max = 0;
          if( count($nextAvaiableDate) > 1 ){
          for($i = 0; $i < count($nextAvaiableDate) - 1; $i++){
          $start = strtotime( $nextAvaiableDate[$i] );
          $end   = strtotime( $nextAvaiableDate[$i + 1] );

          $diff  = $end - $start;
          if($diff > $max) $max = $diff;
          }
          }

          $max = $max / (60*60*24);
         */
        // $adate=array_combine($nextAvaiableDate, array_map('strtotime', $nextAvaiableDate));
        //  usort($nextAvaiableDate,"");
        //$configTimezone=Mage::app()->getStore()->getConfig('general/locale/timezone');
        //$configVal=Mage::getModel('core/date')->timestamp(time());
        //$configVal1=date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

        rsort($nextAvaiableDate);
        //Mage::log("hh=".json_encode($configTimezone)."=".json_encode($configVal)."=".json_encode($configVal1),1,'javed.log');
        // $configSetting=array_unqiue(array_filter($configSetting));
        //Mage::log("h11h=".json_encode($nextAvaiableDate)."=".$nextAvaiableDate[0],1,'javed.log');
        //  $productionDays = $maxDays;
        // Mage::log("hh=".$maxDays,1,'javed.log');
        //Mage::log('After updating ' . $productionDays, '1', 'messenger-nextdeliverydate.log');
        //Mage::log('Next Avaiable Date array ' . json_encode($nextAvaiableDate), '1', 'messenger-nextdeliverydate.log');
        //return date("d-m-Y", strtotime($productionDays));
        // Mage::log(date("Y-m-d", strtotime($nextAvaiableDate[0])),1,"javed.log");
        return date("d-m-Y", strtotime($nextAvaiableDate[0]));
    }

    public function getConfigSettingAtProductLevel() {
        $allAddresses = Mage::getSingleton('checkout/session')->getQuote()->getAllAddresses();
        $configSetting = array();
        foreach ($allAddresses as $address) {
            $allItems = $address->getAllItems();
            foreach ($allItems as $item) {
                //   Mage::log('set=' . json_encode($item->getProduct()->getId()), 1, 'messenger-setting.log');
                $prod = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                $attrValue = $prod->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($prod);
                //  Mage::log('set=' . json_encode($attrValue), 1, 'messenger-setting.log');
                // echo "999".$attrValue;exit;

                $configSetting[] = $attrValue;
            }
        }

        $configSetting = array_filter($configSetting);
        $configSetting = array_unique($configSetting);

        return $configSetting;
        // Mage::log('set=' . json_encode($configSetting), '1', 'messenger-setting.log');
        //return $configSetting;
        //echo "<pre>4566";print_r($configSetting);exit;
    }

    public function getFirstMulitiplevailableDate($format='Y-m-d', $noOfDays) {
        $currentDate = Mage::app()->getLocale()->date();

        $d = (int) $currentDate->toString('d'); //date('j');
        $m = (int) $currentDate->toString('M'); //date('n');
        $y = (int) $currentDate->toString('Y'); //date('Y');

        $maxInterval = Mage::getStoreConfig('checkout/adjdeliverydate/max');

        $dayMove = $this->_getMoveMultipleDaysIntervalDependsHolidays($d, $m, $y, $noOfDays);
        Mage::log("multiple" . $dayMove, 1, "multishipping.log");
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

            $time = mktime(0, 0, 0, $m, $d + $i, $y);
            if ($format == 'unix') {
                return $time;
            }
            return date($format, $time);
        }
    }

    public function getFirstAvailableDate($format = 'Y-m-d', $noOfDays) {
        $currentDate = Mage::app()->getLocale()->date();
        $shippingData = Mage::getSingleton('core/session')->getcustomShippingMethod();
        // echo "<pre>ddd";print_r($shippingData);
        //
        //echo "days1=".$noOfDays;
        //  echo "<pre>";print_r($noOfDays);exit;
        $countedDays = Mage::getSingleton('core/session')->getDeliveryDateDays();
        //Mage::log($countedDays."=".$noOfDays."=".count($shippingData)."=".json_encode($shippingData),1,"logged.log");
        $sameDayDelivery = Mage::getStoreConfig('elenissec/elenisgrp/messenger_sameday_delivery');
        //echo "kk".$sameDayDelivery;
        $nextDayDelivery = Mage::getStoreConfig('elenissec/elenisgrp/messenger_nextday_delivery');
        //echo "<pre>javed";print_r($noOfDays);
        if ($countedDays == '' && $noOfDays == '' && count($shippingData) == 3 && strpos($shippingData, "Same Day Delivery") !== false) {
            //$noOfDays=1;
            $noOfDays = $sameDayDelivery;
        }
        if ($countedDays == '' && $noOfDays == '' && count($shippingData) == 2 && strpos($shippingData, "Next Day Delivery") !== false) {
            // $noOfDays=2;
            $noOfDays = $nextDayDelivery;
        }
        //echo "<pre>";print_r($this->getConfigSettingAtProductLevel());
        // echo "days2=".$noOfDays;
        //echo "<pre>";print_r($array);

        $d = (int) $currentDate->toString('d'); //date('j');
        $m = (int) $currentDate->toString('M'); //date('n');
        $y = (int) $currentDate->toString('Y'); //date('Y');
        $h = $currentDate->toString('H');
        $maxInterval = Mage::getStoreConfig('checkout/adjdeliverydate/max');
        //Mage::log("shipping".json_encode($_SESSION),1,"javed1.log");
        //echo "max".$d."=".$this->getLeadTimeDaysCount();
        //echo "sum".$d+$this->getLeadTimeDaysCount();
        $code = Mage::getSingleton('core/session')->getData("shipping_code");
        //echo $code;
        // Mage::log("shipping".json_encode($code)."=".$noOfDays."=".$h,1,"javed4.log");
        if ($code == "storepickupmodule") {
            //$noOfDays=2;
            //  $noOfDays=Mage::getStoreConfig('elenissec/elenisgrp/storepickup_delivery_days');
            $noOfDays = Mage::getStoreConfig('elenissec/elenisgrp/storepickup_delivery_days');
            //$dayMove = $this->_getMoveDaysIntervalDependesHolidays($d, $m, $y, $days);
        }
        //echo $noOfDays;
        if ($noOfDays ) {

            $dayMove = $this->_getMoveDaysIntervalDependesHolidays($d, $m, $y, $noOfDays);
            //Mage::log($noOfDays."=".$dayMove,1,"loggeddays.log");
        }
        //echo "jhj".$dayMove;
        //echo $d."=".$m."=".$y."<br/>";
        //echo "day move".$dayMove."<br/>";
        //echo "hhhj".(int)($dayMove+$this->getLeadTimeDaysCount());
        //echo "ll=".$dayMove;
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
            //echo "count".$d + $i + $this->getLeadTimeDaysCount();


            $time = mktime(0, 0, 0, $m, $d + $i, $y);

            if ($format == 'unix') {
                return $time;
            }
          // echo date('m-d-Y',$time);
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
        // echo "jkhjhn";
        //echo "javed".date('Y-m-d', $time);
        //echo "javed1".$this->getFirstAvailableDate();
        if (date('Y-m-d', $time) < $this->getFirstAvailableDate()) {
            //die("11");
            return true;
        }
        $weekend = $this->getWeekend(true);
        $holiday = $this->getHolidays();

        if (in_array(date('w', $time), $weekend)) {
            //  die("22");
            return true;
        }
        if (array_key_exists(date('j', $time), $holiday[date('Y', $time)][date('n', $time)])) {
            // die("33");
            return true;
        }
        return false;
    }

    public function getDateForNextAvailableDays($firstDate, $actualDate) {
        $date = $firstDate;
        $nextDay = explode("-", $date);
        $d = $nextDay[0];
        $m = $nextDay[1];
        $y = $nextDay[2];
        //echo "<pre>";print_r($nextDay);print_r($actualDate);exit;
        // echo "<pre>";print_r(explode("-",$date));exit;



        if ($actualDate) {
            for ($day = 1; $day <= $actualDate; $day++) {
                if ($this->isDayoff($d + $day, $m, $y)) {
                    $shift++;
                }
            }
            $day--;

            $day = $this->_checkNextDayDelivery($day);
        }
        // echo $day;exit;
        return $day;
    }

}