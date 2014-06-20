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
class Elenis_CustomCheckoutMultipleAddress_Checkout_MultiController extends Mage_Core_Controller_Front_Action{

    public function indexAction(){
        // add some code here
    }
    
    public function addreceipentsAction(){
        $postData = Mage::app()->getRequest()->getPost();
        $recipientsData = json_decode($postData['recipientqtydata']);
        $recipientsSetData = array();
       
        foreach($recipientsData as $key=>$obj){
         $recipientsSetData[$key] = $obj->qty;    
        }
       
        
        //$recipientsItemData['itemsku'] = (!empty($postData['itemsku']) ? $postData['itemsku']:"");
        //$recipientsItemData['qtyarray'] = $recipientsSetData;
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
        $recipientsItemData = $session->getData("recipientsItemData");
        
        //$recipientsItemData['itemsku'] = (!empty($postData['itemsku']) ? $postData['itemsku']:"");
        $recipientsItemData[$postData['itemsku']]['qtyarray'] = $recipientsSetData;
        
        $multishippingModel = Mage::getSingleton('customcheckoutmultipleaddress/type_multishipping');
        $multishippingModel->setReceipants($recipientsItemData);
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
        $session->setData("recipientsItemData", $recipientsItemData);
        //$multishippingModel->getQuoteShippingAddressesItems();
        $this->_redirect('checkout/multishipping/addresses');
         echo "you are here"; exit;
        
    }
    
    public function saveaddressAction(){
        $postData = Mage::app()->getRequest()->getPost();
        $curAddressId = (!empty($postData['addressid'])? $postData['addressid']:"");
        $itemAddressData = json_decode($postData['itemaddressdata']);
        foreach($itemAddressData as $key=>$obj){
           $tempkeyArray = array_keys((array) $obj); 
           $itemAddressSetData[$tempkeyArray[0]] = $obj->$tempkeyArray[0];    
        }
       
        if(!empty($curAddressId)){
        $curItemAddressObj = Mage::getModel('customer/address')->load(1);
        foreach($itemAddressSetData as $key=>$val){
            $curItemAddressObj->setData($key,$val);
        }
        $curItemAddressObj->save();
        }else{
            echo "Please give address id";
        }
        //print_r(Mage::getModel('customer/address')->load(1));
        //print_r($postData);
        echo 'you are hree'; exit;
    }
    
}

?>
