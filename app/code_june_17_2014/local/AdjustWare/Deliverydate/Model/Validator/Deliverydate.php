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
class AdjustWare_Deliverydate_Model_Validator_Deliverydate
{
    public function validate($field, $value){
        $hlp    = Mage::helper('adjdeliverydate');
        $label  = $field->getLabel();
        
        if (!$value){
            return array($hlp->__('Please provide %s' , $label));
        }
        
        $date  = $field->getValue($field->getFormat());
        $value = trim($value);
        if ($date != $value){
            return array($hlp->__('Please provide a valid date in %s format', strtolower($field->getFormat())));
        }

        $holiday = Mage::getSingleton('adjdeliverydate/holiday');
        if ($holiday->isHoliday($field->getValue('yyyy-MM-dd'))){
             return array($hlp->__('Delivery is not available on %s' , $value));
        }
        
        $max = Mage::getStoreConfig('checkout/adjdeliverydate/max');

        $time = strtotime($field->getValue('yyyy-MM-dd'))- $max*86400;
        if (time() < $time ) {
            return array($hlp->__('You cannot request delivery more then for %d days' , $max));
        }
        
        return array();
    }
}