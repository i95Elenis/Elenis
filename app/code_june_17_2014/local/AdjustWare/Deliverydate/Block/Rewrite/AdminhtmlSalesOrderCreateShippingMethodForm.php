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
class AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderCreateShippingMethodForm extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{
    protected function _construct()
    {
        parent::_construct();
        
        $step = 'shippingMethod';

        if ($sDeliveryDate = Mage::getSingleton('adminhtml/session_quote')->getOrder()->getDeliveryDate())
        {
            $aDateTime = explode(' ', $sDeliveryDate);
            
            if (!empty($aDateTime[1]))
            {
                $sTime = str_replace(':', ',', $aDateTime[1]);
            }
            else 
            {
                $sTime = '';
            }
            
            Mage::getModel('adjdeliverydate/step')->setValues($step, array
            (   
                'delivery_date'     => $sDeliveryDate, 
                'delivery_time'     => $sTime, 
                'delivery_comment'  => Mage::getSingleton('adminhtml/session_quote')->getOrder()->getDeliveryComment())
            );
        }
    }

}