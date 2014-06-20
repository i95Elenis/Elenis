<?php
class Elenis_CheckData_Model_Sales_Quote_Item_Abstract extends Mage_Sales_Model_Quote_Item_Abstract
{
     /**
     * Checking item data
     *
     * @return Mage_Sales_Model_Quote_Item_Abstract
     */
    public function checkData()
    {
        $this->setHasError(false);
        $this->clearMessage();

        $qty = $this->_getData('qty');
		//echo $qty;exit;
        try {
            $this->setQty($qty);
        } catch (Mage_Core_Exception $e){
            $this->setHasError(true);
            $this->setMessage($e->getMessage());
        } catch (Exception $e){
            $this->setHasError(true);
            $this->setMessage(Mage::helper('sales')->__('Item qty declaration error.'));
        }

        try {
            echo get_class($this);exit;
          $this->getProduct()->getTypeInstance(true)->checkProductBuyState($this->getProduct());

        } catch (Mage_Core_Exception $e) {
            $this->setHasError(true);
            $this->setMessage($e->getMessage());
            $this->getQuote()->setHasError(true);

            $this->getQuote()->addMessage(
                Mage::helper('sales')->__('Some of the products below do not have all the required options. Please edit them and configure all the required options.')
            );
        } catch (Exception $e) {
            $this->setHasError(true);
            $this->setMessage(Mage::helper('sales')->__('Item options declaration error.'));
            $this->getQuote()->setHasError(true);
            $this->getQuote()->addMessage(Mage::helper('sales')->__('Items options declaration error.'));
        }

        return $this;
    }

}
		