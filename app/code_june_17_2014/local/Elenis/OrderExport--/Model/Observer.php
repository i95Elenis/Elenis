<?php

class Elenis_OrderExport_Model_Observer 
{
        /**
	 * Observer to to get orderId and Export CSV sheet under var/export/netdot
         */
    function orderExport($observer) {
       
        try {
         //  $order = $observer->getOrder();
         //  $order_id = $order->getId();
            
            //  echo "<pre>";
           $orderIds = $observer->getEvent()->getOrderIds();
           //print_R($orderIds);
            $order_id =  $orderIds[0];
          // Mage::log('orderId:'. $order->getId() . ' Order IncrementId:'.$order->getIncrementId(),'1','orderexport.csv');
           
           Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrders($order_id);
            
           
           return true;
           
        /* @var $item Mage_Sales_Model_Order_Item */
              
        }
          catch (Exception $exc) {
           Mage::log($exc->getMessage(),'1','customerassing.log');
        }
    }
    
    
    
      
    
    
    
     
	
	  

}
?>