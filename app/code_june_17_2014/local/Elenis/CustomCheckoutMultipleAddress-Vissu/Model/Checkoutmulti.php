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
class Elenis_CustomCheckoutMultipleAddress_Model_Checkoutmulti{
    
    public function addReceipants($noofReceipants){
        $multishippingModel = Mage::getSingleton('customcheckoutmultipleaddress/type_multishipping');
        $multishippingModel->getQuoteShippingAddressesItemsPerReceipants($noofReceipants);
        echo "you are herein model";
        //exit;
    }
    
}

?>
