<?php

class Elenis_QuoteItemConvert_Model_Observer {

    public function quoteItemList(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        // echo "<pre>";print_r($order->getData());print_r($quote->getData());exit;

        foreach ($quote as $item) {
            $orderItem = Mage::getModel('sales/order_item')
                            ->setStoreId($item->getStoreId())
                            ->setQuoteItemId($item->getId())
                            ->setQuoteParentItemId($item->getParentItemId())
                            ->setProductId($item->getProductId())
                            ->setProductType($item->getProductType())
                            ->setQtyBackordered($item->getBackorders())
                            ->setProduct($item->getProduct())
                            ->setBaseOriginalPrice($item->getBaseOriginalPrice())
            ;

            $options = $item->getProductOrderOptions();
            if (!$options) {
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            }
            $orderItem->setProductOptions($options);
            Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item', $item, $orderItem);

            if ($item->getParentItem()) {
                $orderItem->setQtyOrdered($orderItem->getQtyOrdered() * $item->getParentItem()->getQty());
            }

            if (!$item->getNoDiscount()) {
                Mage::helper('core')->copyFieldset('sales_convert_quote_item', 'to_order_item_discount', $item, $orderItem);
            }

            Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
                            array('order_item' => $orderItem, 'item' => $item)
            );
        }


        //$orderItem->save();
        //return $orderItem;
        //exit;
    }

    public function getParentItemIds() {
        $parentId = array();
        foreach ($this->_getQuote()->getItemsCollection() as $item) {
            $parentId[] = $item->getParentItemId();
        }
        $parentId = array_filter(array_unique($parentId));
        return $parentId;
    }

}
