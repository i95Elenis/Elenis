<?php
class Webshopapps_Timegrid_Block_Timegrid extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getTimegrid()
     {
        if (!$this->hasData('timegrid')) {
            $this->setData('timegrid', Mage::registry('timegrid'));
        }
        return $this->getData('timegrid');

    }
}