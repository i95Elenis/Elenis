<?php

class Webshopapps_Timegrid_Model_Mysql4_Timegrid_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('timegrid/timegrid');
    }
    
	public function setWeekCommencing($weekCommencing) {
		
		if ($weekCommencing=='0000-00-00') {
			$this->getSelect()
	             ->where('main_table.week_commencing = ? OR main_table.week_commencing IS NULL', $weekCommencing);
		} else {
	        $weekCommencing=date('Y-m-d',strtotime($weekCommencing));
			$this->getSelect()
	        	 ->where('main_table.week_commencing = ?', $weekCommencing);
		}
		
		$this->getSelect()
        	->order(array('main_table.week_commencing','main_table.time_slot_id'))
	        ->limit(1);

        return $this;
	}
    
    
	public function setSlot($slot) {
        $this->getSelect()
            ->where('main_table.time_slot_id = ?', $slot);
        return $this;
	}
	

	
}