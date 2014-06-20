<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Deliverydate_Model_Quote {

    public function saveDelivery($shippingAddress)
    {
        
        $deliveryData = Mage::app()->getRequest()->getPost('adj');
        
        echo "<pre>";
       // print_R($deliveryData);die('12121');
        if ($deliveryData = Mage::app()->getRequest()->getPost('adj'))
        {
            if (is_array($deliveryData))
            {
                // fix for delivery time
                if (Mage::getStoreConfig('checkout/adjdeliverydate/show_time') AND !empty($deliveryData['delivery_date']) AND !empty($deliveryData['delivery_timeframe']))
                {
                    $deliveryData['delivery_date'] .= ' ' . implode(':', $deliveryData['delivery_timeframe']);

                    unset($deliveryData['delivery_time']);
                }

                $deliveryData['delivery_date'] = Mage::helper('adjdeliverydate')->convertDate($deliveryData['delivery_date']);

                foreach ($deliveryData as $name => $value){
                    print_r($deliveryData);
                    $shippingAddress->setData($name, $value);
                    $shippingAddress->setData($name . '_is_formated', true);
                }
                $shippingAddress->save();
            }
        }
    }

    public function multiSaveDelivery($aAllShippingAddresses)
    {
        
        //echo "<pre>";
        $shippingData = Mage::app()->getRequest()->getPost('shipping_method');
      //print_r($shippingData);die('121');
        
        if ($deliveryData = Mage::app()->getRequest()->getPost('adj'))
        {
            if (is_array($deliveryData)){

                foreach ($aAllShippingAddresses as $shippingAddress)
                {
                    $addressId = $shippingAddress->getData('address_id');
                    // fix for delivery time
                    if (Mage::getStoreConfig('checkout/adjdeliverydate/show_time')
                        AND !empty($deliveryData['delivery_date'.$addressId])
                            AND !empty($deliveryData['delivery_time'.$addressId]))
                    {
                        $deliveryData['delivery_date'.$addressId] .= ' ' . implode(':', $deliveryData['delivery_time'.$addressId]);

                        unset($deliveryData['delivery_time'.$addressId]);
                    }
                    
                    $deliveryData['delivery_date'.$addressId] = Mage::helper('adjdeliverydate')->convertDate($deliveryData['delivery_date'.$addressId]);

                    //data in post has format <field_name><shipping_address_id>
                    //for example delivery_date120
                   $shippingmethod='ups';
                  $shippingmethod = $shippingData[$addressId];
                    
                   //echo $shippingmethod;exit;
             //   echo Mage::getSingleton('checkout/session')->getQuoteId();die('testing');
                    foreach ($deliveryData as $name => $value)
                    {
                        if(strpos($name, $addressId) > 0 )
                        {
                             $sPos = strpos($name, $addressId);
                            $newName = substr($name, 0, $sPos);
                           if($shippingmethod =='storepickupmodule_pickup' ||$shippingmethod =='matrixrate_matrixrate_264' ) {
                            Mage::log('$shippingmethod:L '.$shippingmethod,'1','shi.log');
                            $shippingAddress->setData($newName, $value);
                            $shippingAddress->setData($newName . '_is_formated', true);
                            }
                        else if($shippingmethod =='matrixrate_matrixrate_266'){
                                $dateModified = explode(" ", $value);
                                if(array($dateModified) && isset($dateModified[1])){
                                    $value=$dateModified[1];
                                    $value='12:00';
                                     }
                            $shippingAddress->setData($newName, $value);
                            $shippingAddress->setData($newName . '_is_formated', true);
                            Mage::log('$shippingmethod:P '.$shippingmethod,'1','shi.log');
                            
                             }      
                             else{
                            $shippingAddress->setData('delivery_date', NULL);
                            Mage::log('$shippingmethod:P '.$shippingmethod,'1','shi.log');
                            }      
                        }
                    }
                   
                    $shippingAddress->save();
                }
            }
        }
    }

    public function saveDeliveryForOrder(Mage_Sales_Model_Order $order)
    {
        if ($deliveryData = Mage::app()->getRequest()->getPost('adj'))
        {
            if (is_array($deliveryData))
            {
                // fix for delivery time
                if (Mage::getStoreConfig('checkout/adjdeliverydate/show_time') AND !empty($deliveryData['delivery_date']) AND !empty($deliveryData['delivery_time']))
                {
                    $deliveryData['delivery_date'] .= ' ' . implode(':', $deliveryData['delivery_time']);
                    unset($deliveryData['delivery_time']);
                }

                $deliveryData['delivery_date'] = Mage::helper('adjdeliverydate')->convertDate($deliveryData['delivery_date']);
                
               if($shippingmethod!='ups')
               {
                foreach ($deliveryData as $name => $value){
                    $order->setData($name, $value);
                    $order->setData($name . '_is_formated', true);
                }
               }
                else
                            {
                            $shippingAddress->setData('','');
                      $shippingAddress->setData('', '');
                            }
               
                $order->save();
            }
        }
    }
}