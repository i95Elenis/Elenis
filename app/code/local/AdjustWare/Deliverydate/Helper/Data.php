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
class AdjustWare_Deliverydate_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DATETIME_PHP_FORMAT       = 'yyyy-MM-dd HH:mm';
    const DATE_PHP_FORMAT           = 'yyyy-MM-dd';

    public function getMonthOptions(){
        $options = array();
        $options[-1] = $this->__('All');
    
        $m = Mage::app()->getLocale()->getTranslationList('months');
        foreach (array_values($m['format']['wide']) as $code => $name) {
            $options[$code+1] = $name;
        }
        
        return $options;
    }
  
    public function getYearOptions(){
        $options = array();
        $options[-1] = $this->__('All');
        
        for ($i = 0; $i < 3; ++$i)
            $options[date('Y')+$i] = date('Y')+$i;
        
        return $options;
    }
    
    public function getDayOptions(){
        $options = array();
        for ($i = 1; $i < 32; ++$i)
            $options[$i] = $i;
        
        return $options;
    }

    public function formatDate($date=null, $format='short', $showTime=false)
    {
        if (Mage_Core_Model_Locale::FORMAT_TYPE_FULL    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_LONG    !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM  !==$format &&
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT   !==$format) {
            return $date;
        }
        if (!($date instanceof Zend_Date) && $date && !strtotime($date)) {
            return '';
        }
        
        if (!$date instanceof Zend_Date) {
            $date = Mage::app()->getLocale()->date(strtotime($date), null, null, false);
#            d($date);
        }

        if ($showTime) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        }
        else {
            $format = Mage::app()->getLocale()->getDateFormat($format);
        }

        return $date->toString($format);
    }

    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }
    
    /**
     * Check whether the OPC module is active or not
     * 
     * @return boolean
     */
    public function isOPCEnabled()
    {
        return $this->isModuleEnabled('Aitoc_Aitcheckout');
    }

    public function convertDate($date)
    {
		$zendDate = new Zend_Date($date, Mage::getStoreConfig('checkout/adjdeliverydate/format').' HH:mm');
		$dateToSave = $zendDate->toString(Mage::getStoreConfig('checkout/adjdeliverydate/show_time') ? self::DATETIME_PHP_FORMAT : self::DATE_PHP_FORMAT);
		return $dateToSave;
    }
}