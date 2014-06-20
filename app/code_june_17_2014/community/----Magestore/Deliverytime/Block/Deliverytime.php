<?php
class Magestore_Deliverytime_Block_Deliverytime extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getChangeDeliveryTimeUrl()
	{
		return $this->getUrl('deliverytime/index/changetime', array('_secure'=>true));
	}   
}