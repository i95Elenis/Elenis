<?php

class Magestore_Deliverytime_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDeliveryDropdown()
	{
		
	}
	
	public function getDeliveryTimeOfDay($day_late, $current_hour =0, $current_munite = 0)
	{				
		$deliery_result = array();
		
		$day = time();
		$time = mktime(0,0,0,date('m',$day),date('d',$day) + $day_late ,date('Y',$day)); 
		
		$day_text = strtolower(date('l',$time));
		
		$config_open_time = Mage::getStoreConfig('deliverytime/time_schedule/' . $day_text . "_open");
		$config_close_time = Mage::getStoreConfig('deliverytime/time_schedule/' . $day_text . "_close");
		$time_interval = Mage::getStoreConfig('deliverytime/general/time_interval');
		
		
		if(!$config_open_time)
		{
			return;
		}
		
		$config_open_time = explode(":",$config_open_time);
		
		$config_close_time = explode(":",$config_close_time);

		$allow_time = mktime($current_hour,$current_munite,0,date('m',$time),date('d',$time) ,date('Y',$time)); 
		$open_time = mktime($config_open_time[0],$config_open_time[1],0,date('m',$time),date('d',$time) ,date('Y',$time)); 
				
		if($open_time < $allow_time )
		{
			$open_time = $allow_time;
			while($open_time % ($time_interval * 60) != 0)
			{
				$open_time = $open_time + 60;
			}
		}
				
		$close_time =  mktime($config_close_time[0],$config_close_time[1],0,date('m',$time),date('d',$time) ,date('Y',$time)); 		
				
		while($open_time <= $close_time)
		{
			$deliery_result[] = date('D d M H:i',$open_time);
			
			$open_time = $open_time + $time_interval * 60;
		}

		return $deliery_result;
	}
	
	
	public function getAllDeliveryTime()
	{
		$result = array();
		$day_ship = Mage::getStoreConfig('deliverytime/general/day_ship');
		$minimum_gap = Mage::getStoreConfig('deliverytime/general/minimum_gap');
		
		
		$result = $this->getDeliveryTimeOfDay(0,date('H',time()),date('i',time()) + $minimum_gap);
		
		for($i=1; $i <= 3; $i++)
		{
			
			$temp = $this->getDeliveryTimeOfDay($i);
			
			if(count($temp))
			{
				if(!count($result))
				{
					$result = $temp;
				}
				else
				{
					$result = array_merge($result, $temp);
				}	
			}
			
		}

		return $result;
	}
	
	public function isOutOfDeliveryTime()
	{
		$day = time();
		$time = mktime(0,0,0,date('m',$day),date('d',$day),date('Y',$day)); 
		$day_text = strtolower(date('l',$time));
		
		$config_open_time = Mage::getStoreConfig('deliverytime/time_schedule/' . $day_text . "_open");
		$config_close_time = Mage::getStoreConfig('deliverytime/time_schedule/' . $day_text . "_close");
		$minimum_gap = Mage::getStoreConfig('deliverytime/general/minimum_gap');
		
		
		
		if(!$config_open_time)
		{			
			return true;
		}
		
		$config_open_time = explode(":",$config_open_time);		
		$config_close_time = explode(":",$config_close_time);
		
		$open_time = mktime($config_open_time[0],$config_open_time[1],0,date('m',$time),date('d',$time) ,date('Y',$time)); 
		
		if($config_close_time[0] == '00')
		{
			$close_time =  mktime($config_close_time[0],$config_close_time[1],0,date('m',$time),date('d',$time)+1 ,date('Y',$time)); 
		}
		else
		{
			$close_time =  mktime($config_close_time[0],$config_close_time[1],0,date('m',$time),date('d',$time) ,date('Y',$time)); 
		}
		
		$start_ship_time = $day  + (int)$minimum_gap * 60;
				
		return (($open_time > $start_ship_time) || ($close_time < $start_ship_time));
	}
	
	public function isModuleEnable()
	{
		$enable = Mage::getStoreConfig('deliverytime/general/enable');
		return $enable;
	}
	
	
	
}