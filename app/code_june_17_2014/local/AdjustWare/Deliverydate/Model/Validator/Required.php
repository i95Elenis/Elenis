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
class AdjustWare_Deliverydate_Model_Validator_Required
{
    public function validate($label, $value){
        $errors = array();
        if (!$value){
            $errors[] = Mage::helper('adjdeliverydate')->__('Please provide %s' , $label);
        }
        return $errors;
    }
}