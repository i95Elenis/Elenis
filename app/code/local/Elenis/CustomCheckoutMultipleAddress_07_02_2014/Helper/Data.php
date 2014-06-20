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
class Elenis_CustomCheckoutMultipleAddress_Helper_Data extends Mage_Core_Helper_Abstract {

    public function updateQuoteQty($qty, $quoteId) {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName('sales/quote');
        //echo $qty.$quoteId;exit;
        $query = "UPDATE {$table} SET items_qty = {$qty} WHERE entity_id = " . (int) $quoteId;
        echo $query;exit;
          $result=  $writeConnection->query($query);
            return $result;
    }

}