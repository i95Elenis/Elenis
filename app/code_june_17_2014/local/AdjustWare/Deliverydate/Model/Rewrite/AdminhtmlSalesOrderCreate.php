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
class AdjustWare_Deliverydate_Model_Rewrite_AdminhtmlSalesOrderCreate extends Mage_Adminhtml_Model_Sales_Order_Create
{

    /**
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        // START AITOC DELIVERY DATE
        $errors = Mage::getModel('adjdeliverydate/step')->process();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        // FINISH AITOC DELIVERY DATE

        $quoteAddress = $this->getQuote()->getShippingAddress();
        Mage::getModel('adjdeliverydate/quote')->saveDelivery($quoteAddress);

        $order = parent::createOrder();

        return $order;
    }


}