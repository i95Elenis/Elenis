<?php
class Webdziner_Bgsetting_Block_Bgsetting extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBgsetting()     
     { 
        if (!$this->hasData('bgsetting')) {
            $this->setData('bgsetting', Mage::registry('bgsetting'));
        }
        return $this->getData('bgsetting');
        
    }
}