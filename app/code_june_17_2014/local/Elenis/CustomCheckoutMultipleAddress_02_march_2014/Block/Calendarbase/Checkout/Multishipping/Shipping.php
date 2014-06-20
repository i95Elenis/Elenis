<?php
class Elenis_CustomCheckoutMultipleAddress_Block_Calendarbase_Checkout_Multishipping_Shipping extends Webshopapps_Calendarbase_Block_Checkout_Multishipping_Shipping
{
public function getAddressItems($address) {

        $items = array();
            $quoteAddressItems = Mage::getModel('sales/quote_address_item')->getCollection()->addFieldToSelect('*')->addFieldToFilter('quote_address_id', $address->getId());
            foreach ($quoteAddressItems as $addressItem) {

                if ($addressItem->getQuoteAddressId() == $address->getId() && ($addressItem->getCheckMultiple() != 1 || $addressItem->getCheckMultiple() == NULL))
                {
                    $addressItem->setQuoteItem($this->getCheckout()->getQuote()->getItemById($addressItem->getQuoteItemId()));
                     $items[] = $addressItem;
                }
            }
        //return $items;
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }
	
}
			