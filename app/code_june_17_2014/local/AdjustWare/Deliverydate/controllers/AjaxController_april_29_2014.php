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
 * Description of ajaxController
 *
 * @author lyskovets
 */
class AdjustWare_Deliverydate_AjaxController extends Mage_Core_Controller_Front_Action {

    public function dateValidateAction() {
        /*    ini_set('display_errors',1);
          Mage::setIsDeveloperMode(true);

          $block = new AdjustWare_Deliverydate_Block_Renderer_Deliverydate();

          $debug = new ReflectionClass($block);

          echo Mage::getVersion();

          Zend_Debug::dump($debug->getFileName());
          Zend_Debug::dump($debug->getMethods()); */
        //  $result = $this->_getValidator();
        Mage::getSingleton('core/session')->setDeliveryDateDays('');
        if (!isset($result['error'])) {
            $result['valid_date'] = $this->_getValidDate();
        }
        $newObj = new AdjustWare_Deliverydate_Block_Renderer_Deliverydate();
        $newObj->_initilaizeToday();
        $holiday = Mage::getModel('adjdeliverydate/holiday');
        $noOfDays = Mage::app()->getRequest()->getParam('days');
        $typeofMethod = Mage::app()->getRequest()->getParam('type');
        if ($typeofMethod == "n") {
            $noOfDays = Mage::app()->getRequest()->getParam('days');
            $days = date("m/d/Y", strtotime($noOfDays));
            $d = (int) $currentDate->toString('d'); //date('j');
            $m = (int) $currentDate->toString('m'); //date('n');
            $y = (int) $currentDate->toString('Y'); //date('Y');
            $day1 = $holiday->isDayoff($d, $m, $y);
            $day2 = $holiday->isHoliday($currentDate);
            $days = date("m/d/Y", strtotime($value));
            if (!$day1 && $day2) {
                $nextDay = date("m/d/Y", strtotime($nextDay . ' + 1 day'));
            }
            
            $this->_sendResponse($nextDay);
        } else {



            Mage::getSingleton('core/session')->setDeliveryDateDays($noOfDays);
            $holiday->getFirstAvailableDate('Y-m-d', $noOfDays);
            //$firstAvailable = $holiday->getFirstAvailableDate('unix',3);
            // echo $firstAvailable;
            //return $holiday->getFirstAvailableDate('Y-m-d',$noOfDays);
            $this->_sendResponse($this->_getValidDate());
        }
    }

    private function _getValidDate() {
        Mage::getSingleton('core/session')->setDeliveryDateDays('');
        $format = Mage::getStoreConfig('checkout/adjdeliverydate/format');

        $noOfDays = Mage::app()->getRequest()->getParam('days');

        Mage::getSingleton('core/session')->setDeliveryDateDays($noOfDays);


        $date = Mage::getModel('adjdeliverydate/holiday')->getFirstAvailableDate('unix', $noOfDays);

        //echo "jhj".date("Y-m-d",$date);exit;


        $dateFormer = new Zend_Date($date);
        $formatedDate = $dateFormer->toString($format);

        return $formatedDate;
    }

    protected function _sendResponse($data) {
        //$ajaxResponse = Mage::helper('core')->jsonEncode($data);

        $this->getResponse()->setBody($data);
    }

    private function _getValidator() {
        $baseFormId = 'delivery_date';
        $formId = key($this->getRequest()->getPost('adj'));
        if (strlen($formId) > strlen($baseFormId)) {
            return Mage::getModel('adjdeliverydate/step')->multiprocess('shippingMethod');
        }
        return Mage::getModel('adjdeliverydate/step')->process('shippingMethod');
    }

}