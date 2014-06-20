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
class AdjustWare_Deliverydate_Model_Rewrite_FrontCheckoutTypeMultishipping extends Mage_Checkout_Model_Type_Multishipping
{
    public function setShippingMethods($methods)
    {
        $errors = Mage::getModel('adjdeliverydate/step')->multiprocess();
        if ($errors)
        {
            Mage::throwException($errors['message']);
        }
        $aAllShippingAddresses = $this->getQuote()->getAllShippingAddresses();
        Mage::getModel('adjdeliverydate/quote')->multiSaveDelivery($aAllShippingAddresses);
            
        return parent::setShippingMethods($methods);
    }
}