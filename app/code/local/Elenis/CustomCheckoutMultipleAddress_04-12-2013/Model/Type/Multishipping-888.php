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
        
         $removeItemType = Mage::app()->getRequest()->getParam('type');
         $removeItemRow = Mage::app()->getRequest()->getParam('row');
         
         $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
         $receipantSessData = $session->getData("recipientsItemData");
         
         
        if($removeItemType=='ci'){
            if($removeItemRow!=''){
             $address = $this->getQuote()->getAddressById($addressId);
                /* @var $address Mage_Sales_Model_Quote_Address */
              if ($address) {
                $item = $address->getValidItemById($itemId);
                //if ($item->getQty()>1 && !$item->getProduct()->getIsVirtual()) {
                      //  $item->setQty($item->getQty());
               // }
               /* $curItemSku = $item->getSku();
                $curQtyArray = $receipantSessData[$curItemSku]['qtyarray'];
              
                $tempDummyArray = array();
                foreach($curQtyArray as $key=>$val){
                    if($key!=($removeItemRow-1)){
                        $tempDummyArray[] = $val;
                    }
                }
               */
               // $receipantSessData[$curItemSku]['qtyarray'] = $tempDummyArray;
               
               // $session->setData("recipientsItemData",$receipantSessData);
                //$this->getQuoteShippingAddressesItems();
                $checkoutSession= Mage::getSingleton('checkout/session');
                $quote = $checkoutSession->getQuote();
                $cart = Mage::getModel('checkout/cart');
                $cartItems = $cart->getItems();
               // echo "<pre>";print_r($cartItems);exit;
                foreach ($cartItems as $item)
                {
                    //echo $item->getId().$itemId;exit;
                    if($item->getId()==$itemId){
                        $quote->removeItem($item->getId())->save();
                    }
                    
                }
                Mage::getModel('sales/quote_address_item')->load($itemId)->delete();
              } 
            }
           // $receipantSessData = $session->getData("recipientsItemData");
            //echo "<pre>";print_r($receipantSessData);exit;
            return true;
        }else{
            parent::removeAddressItem($addressId, $itemId);
        } 
        
        
        
    }

}

?>
