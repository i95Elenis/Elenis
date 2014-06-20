<?php

class Magestore_Deliverytime_Model_Session extends Mage_Core_Model_Session_Abstract
{  
    
    public function __construct()
    {
        $this->init('deliverytime');
		
    }

    public function unsetAll()
    {
        parent::unsetAll();       
    }
	

    public function setDeliverytime($deliverytime)
	{
		$this->setData("deliverytime",$deliverytime);
	}
	
	public function getDeliverytime()
	{
		return $this->getData("deliverytime");
	}
	
	
	public function isShowMessage()
	{
		if(!$this->getData("Showed"))
		{
			$this->setData("Showed",true);
			return true;
		}
		return false;
	}
	
	
}