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
class AdjustWare_Deliverydate_Block_Rewrite_SalesOrderPrint extends Mage_Sales_Block_Order_Print
{
    public function getOrder()
    {
        $order = parent::getOrder();
        if (!$order->getShippingDescriptionUpdated())
        {
            $orderShippingDescription = $order->getShippingDescription();

            if ($order->getDeliveryDate())
            {
                $deliveryDate = Mage::helper('adjdeliverydate')->formatDate($order->getDeliveryDate(), 'medium', Mage::getStoreConfig('checkout/adjdeliverydate/show_time'));
                
				$orderShippingDescription .= '<h2>' . Mage::helper('adjdeliverydate')->__('Preferred Delivery Date') . '</h2>';
                $orderShippingDescription .= $deliveryDate;
            }
            
            if ($order->getDeliveryComment()) 
            {
                $deliveryComment = $order->getDeliveryComment();
				
				$orderShippingDescription .= '<h2>' . Mage::helper('adjdeliverydate')->__('Comment') . '</h2>';
                $orderShippingDescription .= $deliveryComment;
            }
            
            $order->setShippingDescription($orderShippingDescription);
            $order->setShippingDescriptionUpdated(true);
        }
        return $order;
    }
    
    public function escapeHtml($data, $allowedTags = null)
    {
        return $data;
    }
}