<?php

class Elenis_ProductAvailableDate_Model_Cron {

    public function setProductAvailableDate() {
       
        $product_collection = Mage::getModel("catalog/product")->getCollection();
         
        foreach ($product_collection as $product) {
            $prod = Mage::getModel('catalog/product')->load($product->getId());
            $attr = $prod->getResource()->getAttribute('flag_leadtime_nextavaiabledate')->getFrontend()->getValue($prod);
            if ($attr == "Next Available Date") {
                $exp_date = $prod->getResource()->getAttribute('next_available_date')->getFrontend()->getValue($prod);
                $exp_date = date("Y-m-d", strtotime($exp_date));
                $todays_date = date("Y-m-d");
                $today = strtotime($todays_date);
                $expiration_date = strtotime($exp_date);
                Mage::log($prod->getId()."=".$attr."=".$exp_date."=".$todays_date."=".$today . "=" . $expiration_date . "=" . $prod->getNextAvailableDate() . "=" . $prod->getFlagLeadtimeNextavaiabledate(),1,"productavaiabledate.log");
                if ($expiration_date < $today) {
                    $prod->setNextAvailableDate('');
                    //$prod->setFlagLeadtimeNextavaiabledate(441)
                    $prod->setId($prod->getId());
                    $prod->save();
                    Mage::log("updated:=".$prod->getId()."=".$attr."=".$exp_date."=".$todays_date."=".$today . "=" . $expiration_date . "=" . $prod->getNextAvailableDate() . "=" . $prod->getFlagLeadtimeNextavaiabledate(),1,"productavaiabledate.log");
                }
            }
        }
    }

}