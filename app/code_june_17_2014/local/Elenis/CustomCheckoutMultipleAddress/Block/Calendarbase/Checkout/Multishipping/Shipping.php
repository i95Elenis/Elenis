<?php

class Elenis_CustomCheckoutMultipleAddress_Block_Calendarbase_Checkout_Multishipping_Shipping extends Webshopapps_Calendarbase_Block_Checkout_Multishipping_Shipping {

    public function getAddressItems($address) {

        $items = array();
        $parentId = array();

        $quoteAddressItems = Mage::getModel('sales/quote_address_item')->getCollection()->addFieldToSelect('*')->addFieldToFilter('quote_address_id', $address->getId())->setOrder('address_item_id');
        //echo $quoteAddressItems->getSelect()->__toString()."<br/>";
        foreach ($quoteAddressItems as $addressItem) {
            $parentId[] = $addressItem->getParentItemId();
        }
        $parentId = array_filter(array_unique($parentId));
        //echo "<pre>";print_r($parentId);
        foreach ($quoteAddressItems as $addressItem) {
            //echo "<pre>";print_r($addressItem->getData());exit;
            // echo "test".$addressItem->getParentItemId();
            // echo "<pre>";print_r($parentId);
            // if($addressItem->getAddressItemId()!=$addressItem->getParentItemId()){
            if (in_array($addressItem->getParentItemId(), $parentId) && !in_array($addressItem->getAddressItemId(), $parentId) && $addressItem->getParentItemId() != NULL) {
                //echo "hr".$addressItem->getParentItemId()."k";

               // echo "1" . "=" . $addressItem->getAddressItemId() . "=" . $addressItem->getParentItemId() . "<br/>";
                $addressItem->setQuoteItem($this->getCheckout()->getQuote()->getItemById($addressItem->getQuoteItemId()));
                $addressItem->save();
                $items[] = $addressItem;
            }
            if (!in_array($addressItem->getParentItemId(), $parentId) && !in_array($addressItem->getAddressItemId(), $parentId) && $addressItem->getParentItemId() == NULL) {
               // echo "2" . "=" . $addressItem->getAddressItemId() . "=" . $addressItem->getParentItemId() . "<br/>";
                $addressItem->setQuoteItem($this->getCheckout()->getQuote()->getItemById($addressItem->getQuoteItemId()));
                //$addressItem->save();
                $items[] = $addressItem;
            }
            // }
        }
        //return $items;
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    public function getParameterValues($info) {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (is_array($info)) {
            $addressIds = array();
            $itemsInfo = array();
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    //  $allQty += $data['qty'];
                    $itemsInfo[$quoteItemId] = $data;
                }
            }
        }
        foreach ($quote->getAllItems() as $_item) {
            foreach ($itemsInfo as $itemInfo) {
                $addressIds[] = $itemInfo[$_item->getId()]['address'];
            }
        }
        $addressIds = array_filter($addressIds);
        return $addressIds;
    }

    public function getProductDays($prodId) { 
        $product = Mage::getModel('catalog/product')->load($prodId);
        $attributeValue = $product->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($product);
        if ($attributeValue == 'Lead Time') {
            $leadDays = $product->getResource()->getAttribute('lead_time')->getFrontend()->getValue($product);
            if (ctype_digit($leadDays)) {
                $days = $leadDays;
                
            }else{
                $days=0;
            }
            return $days;
        }
       /* if ($attributeValue == 'Next Available Date') {
            $attrValue = $product->getResource()->getAttribute('next_available_date')->getFrontend()->getValue($product);
            $firstDate =  date("d-m-Y", strtotime($attrValue));
            Mage::log($firstDate,1,'multishipping.log');
            //return $firstDate;
            $convertDate = explode("-", $firstDate);
            $startDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0] + 1, $convertDate[2]);
            $secondDate = date("d-m-Y");
            $convertDate = explode("-", $secondDate);
            $endDate = mktime(12, 0, 0, $convertDate[1], $convertDate[0], $convertDate[2]);
            if ($startDate > $endDate) {
                $offset = $startDate - $endDate;
            }
            
            $actualDate = floor($offset / 60 / 60 / 24);
            Mage::log($actualDate,1,'multishipping.log');
           
                return $actualDate;
           
        }*/
    }

}
