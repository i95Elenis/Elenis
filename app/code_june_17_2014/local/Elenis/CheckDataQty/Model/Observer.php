<?php

class Elenis_CheckDataQty_Model_Observer {

    public function setQuoteItemQtyAfter(Varien_Event_Observer $observer) {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();
        $controllerName = Mage::app()->getRequest()->getControllerName();
        $actionName=Mage::app()->getRequest()->getActionName();
        
       // if ($controllerName == "multishipping" && $actionName=="addressesPost") {
            $params = Mage::app()->getRequest()->getParams();
           // echo "<pre>";print_r($params);exit;
          //  if($params['operation']=="update"){
            $reqData = $this->getParamValues(Mage::app()->getRequest()->getParams('ship'));
             /*if (is_array($info)) {
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
           
                }
            }
        }*/
          //  echo "<pre>";print_r($observer);exit;
         //   $item = $observer->getItem();
           /* $info=$params['ship'];
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    if(!is_string($quoteItemId)){
                    $itemIds[]=$quoteItemId;
                    }
                    //$itemsInfo[$quoteItemId] = $data;
                }
            }*/
            
            //echo "<pre>";print_r($params);print_r($item->getData());exit;
            //$qty = $item->getQty();
           // foreach ($itemIds as $itemId) {
             //   echo $itemId;
               // $item=Mage::getModel('cata');
           // $cartHelper->getCart()->removeItem($itemId)->save();
               // $item->setQty($reqData)->setId($itemId)->save();
       // }
            Mage::getModel('checkout/cart')->getQuote()->setItemsQty($reqData)->save();
            //echo $reqData;
        //exit;
       //     Mage::getModel('checkout/cart')->getQuote()->setItemsQty($reqData)->save();
          //  }
            //echo "<pre>";print_r($item->getData());print_r($itemIds);exit;
           //die(get_class($this));
        //}
    }

    public function getParamValues($info) {
        if (is_array($info)) {
            foreach ($info as $itemData) {
                foreach ($itemData as $quoteItemId => $data) {
                    $allQty += $data['qty'];
                    // $itemsInfo[$quoteItemId] = $data;
                }
            }
        }
        return $allQty;
    }
     

}
