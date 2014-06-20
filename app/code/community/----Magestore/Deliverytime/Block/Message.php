<?php
class Magestore_Deliverytime_Block_Message extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function show_warning_message()
	{
		if(!Mage::helper('deliverytime')->isOutOfDeliveryTime())
		{
			return "";
		}
		$cart = $this->_getCart();
		if ($cart->getQuote()->getItemsCount()) 
		{
			$session = Mage::getSingleton("deliverytime/session");						
			return Mage::getStoreConfig('deliverytime/general/message');			
		}
	}
	
	protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
     
}