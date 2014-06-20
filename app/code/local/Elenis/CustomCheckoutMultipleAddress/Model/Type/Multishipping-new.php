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

class Elenis_CustomCheckoutMultipleAddress_Model_Type_Multishipping extends Mage_Checkout_Model_Type_Multishipping{

    private $_checkoutReceipantsItemData;
    
 
 public function getQuoteShippingAddressesItems_orig(){
   $items = array();
   $addresses  = $this->getQuote()->getAllAddresses();
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
  * @return type
  */
 public function getQuoteShippingAddressesItems(){
   $items = array();
   $addresses  = $this->getQuote()->getAllAddresses();
   $addressesItemCountArray = array();
   $itemObject = null;
   $j = 0; 

   $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
       $receipantSessData = $session->getData("recipientsItemData");
       $receipantItemDataArray = (!empty($this->_checkoutReceipantsItemData)? $this->_checkoutReceipantsItemData:((!empty($receipantSessData)? $receipantSessData:array('0'=>1))));
       
       //$targetItemSku = $receipantItemDataArray['itemsku'];
       //$receipantQtyArray = $receipantItemDataArray['qtyarray'];
       //$noofReceipants = sizeof($receipantQtyArray);
       
       $recipientAddressQtyArray = Array();
       foreach($receipantQtyArray as $key=>$qty){
           $recipientAddressQtyArray[$key] = array('qty'=>$qty, 'addressid'=>1) ;
       }
       
       $executedFlag = false;
       $completedItemSku = array();
       foreach ($addresses as $address) {
          $addressesItemCountArray[] = count($address->getAllItems());
          foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
              continue;
            }
            if ($item->getProduct()->getIsVirtual()) {
              $items[] = $item;
              continue;
            }

            $curItemSku = $item->getSku();
             
             
            $receipantQtyArray = array();
            $receipantQtyArray = (!empty($receipantItemDataArray[$curItemSku]['qtyarray'])? $receipantItemDataArray[$curItemSku]['qtyarray']:array());
            $noofReceipants = sizeof($receipantQtyArray);
            //print_r($receipantQtyArray);
            if(!empty($receipantQtyArray)){
                if(!empty($noofReceipants) && (!in_array($curItemSku,$completedItemSku))){
                    $executedFlag = true;
                    for($i=0;$i<$noofReceipants;$i++){
                        $addressItem = clone $item;
                        $curQty = (!empty($receipantQtyArray[$i])? $receipantQtyArray[$i]:1);
                        //$curAddressId = (!empty($recipientAddressQtyArray[$i]['addressid'])? $recipientAddressQtyArray[$i]['addressid']:1);
                        $addressItem->setQty($curQty)
                                     ->setCustomerAddressId($address->getCustomerAddressId())
                                     ->save();
                        $items[] = $addressItem;
                    }
                    $completedItemSku[] = $curItemSku;
                }else{
                    //$item->setCustomerAddressId($address->getCustomerAddressId());
                    //$items[] = $item;
                }
            }else{
                
                $item->setCustomerAddressId($address->getCustomerAddressId());
                $items[] = $item;
            }
            
          }
       }
       //$receipantSessData = $session->setData("recipientqtyarray",array());
       //echo sizeof($items); echo 'final'; exit;
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->save();
        
        $this->save();
   
   return $items;
 }
 
 /**
  * 
  * @param type $receipantsItemData
  */
 public function setReceipants($receipantsItemData){
     $this->_checkoutReceipantsItemData = $receipantsItemData;
 }
 
 /**
     * Remove item from address
     *
     * @param int $addressId
     * @param int $itemId
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function removeAddressItem($addressId, $itemId)
    {
         //echo $addressId.'--'.$itemId; 
         //echo "you are here"; exit;
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
        } 
        
    }
 


}

?>
