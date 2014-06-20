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
class AdjustWare_Deliverydate_Block_Adminhtml_Filter_Delivery extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime
{

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param string $date
     * @param string $locale
     * @return Zend_Date
     */
    protected function _convertDate($date, $locale, $endDay = false)
    {
        try {
            $dateObj = $this->getLocale()->date(null, null, $locale, false);

            //set default timezone for store (admin)
            //$dateObj->setTimezone(Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));
            
            if ( $endDay )
            {
                //set ending of day
                $dateObj->setHour(23);
                $dateObj->setMinute(59);
                $dateObj->setSecond(59);
                
                $dateObj->set($date, Zend_Date::DATE_SHORT);
            }else{
                //set begining of day
                $dateObj->setHour(00);
                $dateObj->setMinute(00);
                $dateObj->setSecond(00);
                
                $dateObj->set($date, Zend_Date::DATE_SHORT);
            }
            
            //set date with applying timezone of store
//            $dateObj->set($date, Zend_Date::DATETIME_SHORT);

            //convert store date to default date in UTC timezone without DST
            //$dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

            return $dateObj;
        }
        catch (Exception $e) {
            return null;
        }
    }
    
    public function getCondition()
    {
        $value = $this->getValue();
        if ( null !== $value )
        {
            if ( !empty($value['orig_to']) )
            {
                $value['to'] = $this->_convertDate($value['orig_to'], $value['locale'], true);
            }
        }
        return $value;
    }

    
    
}