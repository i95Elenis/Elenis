<?php

class Undottitled_Estimateddeliverydate_Model_Deliveries extends Mage_Core_Model_Abstract
{
	/**
     * Intialize model
     */
    function _construct()
    {
        $this->_init('estimateddeliverydate/deliveries');
    }
    
    
    
    public function getNextDeliveryDate($pid,$qty) {
    	
    	$stock = Mage::getModel('cataloginventory/stock_item');
    	$_currentStock = ($stock->loadByProduct($pid)->getStockQty()-$stock->getMinQty()); //Current stock - out of stock notification qty
		
		if($_currentStock):		
			if($_currentStock >= $qty):
				// Available immediately
				return date("Y-m-d H:i:s",time());
			else:
				// Not enough in stock currently to handle the request
				// Get items from the database to run checks								
				$allDates = $this->getDatesBySku($pid)->getData();				
				$rollingQty = $_currentStock;
				if(!empty($allDates)):
					foreach($allDates as $dateElement):
						$rollingQty+= $dateElement['qty'];						
						if($rollingQty >= $qty):
							return $dateElement['date'];
						else:
							continue;
						endif;					
					endforeach;	
					
					return false;
							
				endif;				
			endif;
		else:
			// Couldn't find current stock level
			return false;
		endif;    	
    }
    
    
    public function getDatesBySku($pid)
    {
    	$data = $this->getResource()->getDatesBySku($pid);
        
        if (is_array($data)) {
        	$this->addData($data);
        }
        return $this;
    }
    
    
}