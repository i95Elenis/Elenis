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
class Elenis_CustomCheckoutMultipleAddress_Checkout_MultiController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        // add some code here
    }

    public function addreceipentsAction() {
       
            $postData = Mage::app()->getRequest()->getPost();
            $recipientsData = json_decode($postData['recipientqtydata']);

            // echo "<pre>1";print_r(Mage::app()->getRequest()->getParams());echo "2";print_r($postData);echo "3";print_r($recipientsData[0]->qty);exit;
            $totalQty = Mage::app()->getRequest()->getParam('totqty');
            $sku = Mage::app()->getRequest()->getParam('itemsku');
            $numberOfRecipient = Mage::app()->getRequest()->getParam('numofrecipient');
            $productId = Mage::getModel("catalog/product")->getIdBySku($sku);
            //$actualQty = Mage::app()->getRequest()->getParam('actualqty');
            // $qty = Mage::app()->getRequest()->getParam('totqty');
            $addressItemId = Mage::app()->getRequest()->getParam('address_item_id');
            $quoteAddressId = Mage::app()->getRequest()->getParam('quote_address_id');
            $quoteItemId = Mage::app()->getRequest()->getParam('quote_item_id');
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quoteId = $quote->getId();
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $addresses = $quote->getAllAddresses();
            $addresesData = "";
            $multiShipModel = Mage::getModel("customcheckoutmultipleaddress/multiship");
            $getSplitValue = $multiShipModel->loadSplitNumber($customerId, $productId, $quoteId);
            $getIdtoDelete = $multiShipModel->loadId($customerId, $productId, $quoteId);
            // echo "<pre>";print_r($getIdtoDelete);echo $getIdtoDelete['qty']; exit;
            if ($getIdtoDelete[0]['qty'] > 1) {
                //echo $getIdtoDelete['qty'].$getIdtoDelete['id'];exit;
                //  $multiShipModel->deleteRecord($getIdtoDelete[0]['id']);
                $multiShipModel->setId($getIdtoDelete[0]['id'])->delete();
            }
            // echo "<pre>";print_r($getIdtoDelete['qty']);exit;
            //echo "<pre>";print_r($multiShipModel->getCollection()->getData());exit;
            foreach ($addresses as $address) {
                $addresesData.=$address->getId() . ",";
            }
            $numberOfSplits = $getSplitValue + 1;
            for ($i = 0; $i < count($recipientsData); $i++) {

                $multiShipData['quote_id'] = $quoteId;
                $multiShipData['product_id'] = $productId;
                $multiShipData['customer_id'] = $customerId;
                $multiShipData['numrecipients'] = $numberOfRecipient;
                $multiShipData['qty'] = $recipientsData[$i]->qty;
                $multiShipData['addresses'] = $addresesData;
                //  $multiShipData['actualqty'] = $actualQty;
                $multiShipData['total_qty'] = $totalQty;
                $multiShipData['address_id'] = $addressItemId;
                $multiShipData['item_id'] = $quoteAddressId;
                $multiShipData['quote_item_id'] = $quoteItemId;
                $multiShipData['number_splits'] = $numberOfSplits;

                // echo "kj";print_r($multiShipData);exit;
               // $multiShipModel->setData($multiShipData)->setId($this->getRequest()->getParam("id"))->save();
                $quoteItemModel = Mage::getModel('sales/quote_item')->load($quoteItemId);
                $quoteItemAddressModel = Mage::getModel('sales/quote_address_item')->load($addressItemId);
                Mage::getModel("customcheckoutmultipleaddress/multiship")->insertQuoteItem($quoteItemModel, $productId, $recipientsData[$i]->qty);
                Mage::getModel("customcheckoutmultipleaddress/multiship")->insertQuoteAddressItem($quoteItemAddressModel, $productId, $recipientsData[$i]->qty);
                //  echo "kkj";exit;
               
            }
             Mage::getModel("customcheckoutmultipleaddress/multiship")->updateCheckMulti($addressItemId);
            // Mage::helper('Elenis_CustomCheckoutMultipleAddress')->updateQuoteQty($totalQty, $quoteId);
            $this->_redirect('checkout/multishipping/addresses');
            echo "you are here";
            exit;
        
    }

    public function saveaddressAction() {
        $postData = Mage::app()->getRequest()->getPost();
        $addressId = (!empty($postData['addressid']) ? $postData['addressid'] : "");
        $curAddressId = (!empty($postData['customer_addressid']) ? $postData['customer_addressid'] : "");
        $itemAddressData = json_decode($postData['itemaddressdata']);
        // echo "<pre>";print_r($postData);print_r($curAddressId);print_r($addressId);print_r($itemAddressData);
        foreach ($itemAddressData as $key => $obj) {
            $tempkeyArray = array_keys((array) $obj);
            $itemAddressSetData[$tempkeyArray[0]] = $obj->$tempkeyArray[0];
        }
        //  echo "<pre>";print_r($itemAddressSetData);
        if (!empty($curAddressId)) {
            $curItemAddressObj = Mage::getModel('customer/address')->load($curAddressId);

            foreach ($itemAddressSetData as $key => $val) {
                $curItemAddressObj->setData($key, $val);
            }
            //  echo "<pre>";print_r($curItemAddressObj);exit;
            $curItemAddressObj->save();
            //echo "<pre>";print_r($curItemAddressObj->getData());exit;
        } else {
            echo "Please give address id";
        }
        if (!empty($addressId)) {
            $curAddressObj = Mage::getModel('sales/quote_address')->load($addressId);

            foreach ($itemAddressSetData as $key => $val) {
                //   echo $key."=".$val."\n";
                $curAddressObj->setData($key, $val);
            }
            $curAddressObj->setId($addressId)->setCustomerAddressId($curAddressId)->save();
            //  $curAddressObj->save();
            // echo "<pre>";print_r($curAddressObj->getData());exit;
        } else {
            echo "Please give address id";
        }
        //print_r(Mage::getModel('customer/address')->load(1));
        //print_r($postData);
        $this->_redirect('checkout/multishipping/addresses');
        $message = $this->__('Edited Customer Address');
        Mage::getSingleton('core/session')->addSuccess($message);

        echo 'Success';
        exit;
    }

}

?>
