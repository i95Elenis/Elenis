<?php

/**
 *
 *
 * @category       
 * @package        Elenis_CustomCheckoutMultipleAddress
 * @Description    
 * @author         
 * @copyright      
 * @license        
 */
class Elenis_CustomCheckoutMultipleAddress_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping {

    private $_checkoutReceipantsItemData;

    public function getQuoteShippingAddressesItems() {
        $items = array();
        $addresses = $this->getQuote()->getAllAddresses();
        foreach ($addresses as $address) {
            foreach ($address->getAllItems() as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                if ($item->getProduct()->getIsVirtual()) {
                    $items[] = $item;
                    continue;
                }
                $item->setCustomerAddressId($address->getCustomerAddressId());
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * 
     * @param type $receipantsItemData
     */
    public function setReceipants($receipantsItemData) {
        $this->_checkoutReceipantsItemData = $receipantsItemData;
    }

    /**
     * Remove item from address
     *
     * @param int $addressId
     * @param int $itemId
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function removeAddressItem($addressId, $itemId) {
        //echo $addressId.'--'.$itemId; 
        //echo "you are here"; exit;
        //echo "<pre>";print_R(Mage::app()->getRequest()->getParams());exit;
        $removeItemType = Mage::app()->getRequest()->getParam('type');
        $qty = base64_decode(Mage::app()->getRequest()->getParam('qty'));
        $id = Mage::app()->getRequest()->getParam('id');
        echo "qty=" . $qty;
        $multishipId = (Mage::app()->getRequest()->getParam('multiship-id') ? Mage::app()->getRequest()->getParam('multiship-id') : "");
        $removeItemRow = Mage::app()->getRequest()->getParam('row');
        //$qty=Mage::app()->getRequest()->getParam('qty');
        // $session = Mage::getSingleton("core/session", array("name" => "frontend"));
        //echo "<pre>";print_r($removeItemType);print_r($removeItemRow);print_r($session);print_r($addressId);print_r($itemId);exit;
        // $receipantSessData = $session->getData("recipientsItemData");
        if ($removeItemType == 'ci') {
            if ($removeItemRow != '') {
                $address = $this->getQuote()->getAddressById($addressId);
                $multiShipModel = Mage::getModel("customcheckoutmultipleaddress/multiship");
                $quoteModel = Mage::getModel('sales/quote');
                $quoteItemModel = Mage::getModel('sales/quote_item');
                $quoteAddressItemModel = Mage::getModel('sales/quote_address_item');
                //  echo "<pre>";print_r($address->getData());
                if ($address) {
                    $quoteModel->load($address->getQuoteId());

                    $item = $address->getValidItemById($itemId);
                    // echo "<pre>";print_r($item->getData());exit;
                    $quoteItemModel->load($item->getQuoteItemId());
                   // $quoteAddressItemModel->load($item->getId());
                    //echo "<pre>";print_r($quoteAddressItemModel->getData());exit;
                    //  echo $item->getQuoteAddressId().$addressId;exit;
                   // if ($item->getQuoteAddressId() == $addressId) {
                        //echo "qty".$qty;
                        //echo $item->getQty();exit;
                        $quantity = (int) ($item->getQty() - $qty);
                        //echo "item-qty".$itemId.$quantity;
                        if ($item->getQty() > 0) {
                            $item->setQty($quantity)->setId($itemId)->save();
                            if ($item->getQty() == 0) {
                                $item->setId($itemId)->delete();
                            }
                        }


                        $quoteQty = (int) ($quoteModel->getItemsQty() - $qty);
                        // echo "item-qty1".$quoteQty;
                        if ($quoteModel->getItemsQty() > $quoteQty && $quoteModel->getItemsQty() > 0) {
                            $quoteModel->setItemsQty($quoteQty)->setId($address->getQuoteId())->save();
                            // echo "item-qty2".$quoteQty;exit;
                            if($quoteModel->getItemsQty()==0)
                            {
                                $item->setId($quoteModel->getId())->delete();
                            }
                        }
                       /* $quoteItemQty = (int) ($quoteItemModel->getQty() - $qty);
                        if ($quoteItemModel->getQty() > $quoteItemQty && $quoteModel->getQty() > 0) {
                            $quoteItemModel->setQty($quoteItemQty)->setId($item->getQuoteItemId())->save();
                        }
                        if ($quoteItemModel->getQty() == 0) {
                            $quoteItemModel->setId($item->getQuoteItemId())->delete();
                        }
                        if ($multishipId != "") {
                            $multiShipModel->setId($multishipId)->delete();
                        }
                        */
                        // exit;
                  //  }
                    // echo "<pre>";echo "kk".$qty;exit;

                    $this->getQuoteShippingAddressesItems();
                    //Mage::getUrl('*/cart/');
                    //exit;
                }
            }
        } else {
            parent::removeAddressItem($addressId, $itemId);
        }

        /* if($removeItemType=='ci'){
          if($removeItemRow!=''){
          $address = $this->getQuote()->getAddressById($addressId);

          if ($address) {
          $item = $address->getValidItemById($itemId);
          $curItemSku = $item->getSku();
          $curQtyArray = $receipantSessData[$curItemSku]['qtyarray'];
          $tempDummyArray = array();
          foreach($curQtyArray as $key=>$val){
          if($key!=($removeItemRow-1)){
          $tempDummyArray[] = $val;
          }
          }
          $receipantSessData[$curItemSku]['qtyarray'] = $tempDummyArray;
          $session->setData("recipientsItemData",$receipantSessData);
          $this->getQuoteShippingAddressesItems();
          }
          }
          $receipantSessData = $session->getData("recipientsItemData");

          return true;
          }else{
          parent::removeAddressItem($addressId, $itemId);
          } */
    }

    /* public function setShippingItemsInformation($info) {
      echo "<pre>";print_r($info);
      if (is_array($info)) {

      $itemsInfo = array();
      foreach ($info as $itemData) {
      foreach ($itemData as $quoteItemId => $data) {
      echo  $data['qty'];
      $itemsInfo[$quoteItemId] = $data;
      }
      }
      }
      echo "<pre>";print_r($itemsInfo   );
      exit;
      } */
    /*  public function callImportQuoteAddress($address)
      {
      $quote = Mage::getModel('checkout/session')->getQuote();
      if ($address = $this->getCustomerDefaultBillingAddress()) {
      $quote->getBillingAddress()->importCustomerAddress($address);
      }

      }
     */

    public function importCustomerAddress(Mage_Customer_Model_Address $address) {
        //echo "<pre>here";print_r($address->getData());
        $quote = Mage::getModel('checkout/session')->getQuote();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $tempAddressId = $address->getId();
        //echo $tempAddressId;exit;
        $countedAddressId = Mage::getModel("customcheckoutmultipleaddress/multiship")->countQuoteAddressIds($tempAddressId, $quote->getId(), $customerId);
        //$quoteAddress = Mage::getModel('sales/quote_address')->load($tempAddressId);
        // echo $countedAddressId;exit;
        if ($countedAddressId == 0) {
            $quoteAddress = Mage::getModel('sales/quote_address')->load($tempAddressId);
            //$quoteAddress=new Mage_Sales_Model_Quote_Address();
            Mage::helper('core')->copyFieldset('customer_address', 'to_quote_address', $address, $quoteAddress);
            $email = null;
            if ($address->hasEmail()) {
                $email = $address->getEmail();
            } elseif ($address->getCustomer()) {
                $email = $address->getCustomer()->getEmail();
            }

            if ($email) {
                $quoteAddress->setEmail($email);
            }

            $quoteAddress->setQuoteId($quote->getId())->setAddressType('shipping')->setId($quoteAddress->getId())->save();
        }
    }

    public function setShippingItemsInformation($shipToInfo) {
        //  echo "<pre>";print_r(Mage::app()->getRequest()->getParams());exit;

        if (Mage::app()->getRequest()->getParam('operation') == 'update') {

            if ($shipToInfo) {
                $c = 0;
                $tempArray = array();
                foreach ($shipToInfo as $key => $itemData) {
                    $tempArray[$c]['address_item_id'] = $key;

                    foreach ($itemData as $quoteItemId => $data) {

                        if (!is_string($quoteItemId)) {
                            //echo "kk".$quoteItemId.$c."<br/>";
                            $tempArray[$c]['quote_item_id'] = $quoteItemId;
                            //$tempArray['quote_item_id']=$quoteItemId;
                        }
                        if (!is_array($data)) {
                            $tempArray[$c]['qty'] = $data;
                            //$tempArray['qty']=$data;
                            //echo "jj<pre>"$c."<br/>";
                        } else {
                            //echo "ll".$data['address'].$c."<br/>";
                            $tempArray[$c]['customer_address_id'] = $data['address'];
                            //$tempArray['customer_address_id']=$data['address'];
                        }
                        $c++;
                    }
                }
            }
            //  echo "<pre>";print_r($tempArray);
            $totalQty = 0;
            $tempAddressItem = array();
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quoteId = $quote->getId();
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $quoteAddressModel = Mage::getModel('sales/quote_address');
            $quoteAddressItemModel = Mage::getModel('sales/quote_address_item');
            $quoteModel = Mage::getModel('sales/quote');
            $addressData = Mage::getModel('customer/address');
            $quoteItemModel = Mage::getModel('sales/quote_item');
            //echo count($tempArray)/2;exit;
            // echo "<pre>";print_r($tempArray);
            for ($i = 0; $i < count($tempArray) / 2; $i++) {
                $tempId = 0;
                $tempAddressItem = array();
                //echo "ll".$tempArray[($i * 1 * 2)]['qty'] ."-jj". $tempArray[($i * 2 + 1)]['quote_item_id'];
                //$tempAddressItem = Mage::getModel("customcheckoutmultipleaddress/multiship")->loadAddressIdByQuoteAddressItemId($tempArray[($i * 1 * 2)]['address_item_id']);
                //echo "<pre>";print_r($tempAddressItem);exit;
                //$quoteAddressModel->load($tempAddressItem[0]['quote_address_id']);
                $quoteAddressItemModel->load($tempArray[($i * 1 * 2)]['address_item_id']);
                $quoteModel->load($quoteAddressModel->getQuoteId());
                // $quoteItemModel->load($quoteAddressItemModel->getQuoteItemId());
                $totalQty+=$tempArray[($i * 1 * 2)]['qty'];
                $addressData->load($tempArray[($i * 2 + 1)]['customer_address_id']);
                //echo "<pre>";print_r($addressData->getData());exit;
                Mage::getModel('checkout/type_multishipping')->importCustomerAddress($addressData);
                $tempId = $tempArray[($i * 2 + 1)]['customer_address_id'];
                //   echo "kk".$tempId;
                $tempQuoteAddressId = Mage::getModel("customcheckoutmultipleaddress/multiship")->loadQuoteAddressId($tempId, $quoteId, $customerId);
                // echo "<pre>";print_r($tempQuoteAddressId);exit;
                //    echo "<pre>ddfgg";print_r($tempQuoteAddressId);
                /*   $fields=array();$condition=array();
                  if($quoteItemModel->getItemId()!="" )
                  {
                  $fields['qty']=$tempArray[($i *  1*2)]['qty'];
                  $condition['item_id']=$quoteItemModel->getItemId();
                  echo "<pre>1";print_r($fields);print_r($condition);
                  Mage::getModel("customcheckoutmultipleaddress/multiship")->updateQuanties($fields,$condition,'sales/quote_item');
                  }
                 */

                $fields = array();
                $condition = array();
                if ($quoteAddressItemModel->getAddressItemId() != "") {
                    $fields['qty'] = $tempArray[($i * 1 * 2)]['qty'];
                    $fields['quote_address_id'] = $tempQuoteAddressId;

                    $condition['address_item_id'] = $tempArray[($i * 1 * 2)]['address_item_id'];
                    //echo "<pre>2";print_r($fields);print_r($condition);
                    Mage::getModel("customcheckoutmultipleaddress/multiship")->updateQuanties($fields, $condition, 'sales/quote_address_item');
                }

                //exit;
                //	echo ($i *  1*2)."-".($i * 2 + 1)."<br/>";
            }

            $fields = array();
            $condition = array();


            $fields['items_qty'] = $totalQty;
            $condition['entity_id'] = $quoteId;
            //  echo "<pre>3";print_r($fields);print_r($condition);
            Mage::getModel("customcheckoutmultipleaddress/multiship")->updateQuanties($fields, $condition, 'sales/quote');


            // exit; 
        }
    }

}

?>
