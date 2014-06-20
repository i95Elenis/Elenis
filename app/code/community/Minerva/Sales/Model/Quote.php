<?php
class Minerva_Sales_Model_Quote extends Mage_Sales_Model_Quote
{

    protected function _construct()
    {
	mage::log(__CLASS__ . __FUNCTION__ ); 
        parent::_construct();
    }
	

	public function validateMinimumQty($multishipping = false)
    {
	mage::log(__CLASS__); 
	        $storeId = $this->getStoreId();
		$minOrderActiveQty = Mage::getStoreConfigFlag('sales/minimum_orderqty/active', $storeId);

		if (!$minOrderActiveQty) {
            return true;
        }

        else {
            foreach ($this->getAllAddresses() as $address) {
				if (!$address->validateMinimumQty()) {
                    return false;
                }
            }
        }
        return true;
    }
    
}
