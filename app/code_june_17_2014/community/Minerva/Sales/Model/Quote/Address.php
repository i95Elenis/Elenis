<?php

class Minerva_Sales_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{

    protected function _construct()
    {
        parent::_construct();
    }

	/**
     * Get shopping cart items summary (inchlude config settings)
     *
     * @return decimal
     */
    public function getSummaryQty()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();

        //If there is no quote id in session trying to load quote
        //and get new quote id. This is done for cases when quote was created
        //not by customer (from backend for example).
        if (!$quoteId && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        }

        if ($quoteId && $this->_summaryQty === null) {
            if (Mage::getStoreConfig('checkout/cart_link/use_qty')) {
                $this->_summaryQty = $this->getItemsQty();
            }
            else {
                $this->_summaryQty = $this->getItemsCount();
            }
        }
        return $this->_summaryQty;
    }

    /**
     * Get shopping cart items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount()*1;
    }

    /**
     * Get shopping cart summary qty
     *
     * @return decimal
     */
    public function getItemsQty()
    {
        return $this->getQuote()->getItemsQty()*1;
    }
	/**
     * Validate minimum quantity
     *
     * @return bool
     */
    public function validateMinimumQty()
    {
        $storeId = $this->getQuote()->getStoreId();
        if (!Mage::getStoreConfigFlag('sales/minimum_orderqty/active', $storeId)) {
            return true;
        }

		$minqty = Mage::getStoreConfig('sales/minimum_orderqty/amount', $storeId);
//		$count = $this->helper('checkout/cart')->getSummaryCount();
        if (($this->getQuote()->getItemsQty()) < $minqty) {
            return false;
        }
        return true;
    }
}
