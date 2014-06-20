<?php

class Elenis_CustomCheckoutMultipleAddress_Block_Checkout_Multishipping_Addresses extends Mage_Checkout_Block_Multishipping_Addresses {

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param  $item
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index) {
        //  echo "test<pre>";print_r($item->getData());exit;
        $select = $this->getLayout()->createBlock('core/html_select')
                ->setName('ship[' . $index . '][' . $item->getQuoteItemId() . '][address]')
                ->setId('ship_' . $index . '_' . $item->getQuoteItemId() . '_address')
                ->setValue($item->getCustomerAddressId())
                ->setClass('shipping-address_'.$index)
                ->setOptions($this->getAddressOptions());

        return $select->getHtml();
    }
    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                     'label' => $address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }
    

}

