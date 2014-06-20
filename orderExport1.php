<?php

//echo "123";

// include the core code we are going to use
require_once('app/Mage.php');
umask (0);
Mage::app('default');
//$orders = array(0=>'139');
 
 $order_id=182;
 $order = Mage::getModel('sales/order')->load($order_id);
  
  echo "<pre>";
   $orderCreatedDate = '12/27/2013  2:38:43 PM';
   $orderCreatedDate1 =  explode(' ',$orderCreatedDate);
    
   
   
   
   
  // print_r($orderCreatedDate1[0]);
  //  echo $orderCreatedDate1['0'];
    $orderData= $order->getData();
  $payarry=$order->getPayment()->getData();
 
 
   $cc_exp_month = $payarry['cc_exp_month'] ? $payarry['cc_exp_month'] : '';
  $cc_exp_year = $payarry['cc_exp_year'] ? $payarry['cc_exp_year'] : '';
 
 $ccExp = $cc_exp_month.'/'.'01'.'/'.$cc_exp_year;
 $expiryDdate =  (string)date("m/Y", strtotime($ccExp)); 
 
 $deliveryDate =  $order->getDeliveryDate();
     
        if($deliveryDate!=''){
         $deliveryDate = date("m/d/Y", strtotime($deliveryDate));
        }
 
  print_r($orderData);exit;
       
  $sagepaymentspro = Mage::getModel('ebizmarts_sagepaymentspro/transaction')->getCollection()
                        ->addFieldToFilter('order_id', $order_id);
        
         $postCodeReusult='';
        foreach($sagepaymentspro as $transaction) {
          
            $postCodeReusult= $transaction->getPostCodeResult();
             
        }
  
  // $postCodeReusult;
   //  $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
    
  //   print_r($shippingAddress->getData());
   //  echo $this->getShippingMethod($order);
   
          die('121');  
           
       
 
?>
