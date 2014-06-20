<?php

class Elenis_OrderExport_Model_Observer 
{
        /**
	 * Observer to to get orderId and Export CSV sheet under var/export/netdot
         */
    function orderExport($observer) {
       
        try {
        
           $orderIds = $observer->getEvent()->getOrderIds();
          
           //echo $orderIds;exit;
           Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrders($orderIds);
          
           
           return true;
           
        }
          catch (Exception $exc) {
           Mage::log($exc->getMessage(),'1','customerassing.log');
        }
    }
    
    
    
      
    
    
    
     
	
	  

}
?>