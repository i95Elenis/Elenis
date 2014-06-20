<?php

class Webshopapps_Timegrid_Model_Mysql4_Timegrid extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the timegrid_id refers to the key field in your database table.
        $this->_init('timegrid/timegrid', 'timegrid_id');
    }
    
    
      public function _beforeSave(Mage_Core_Model_Abstract $object)
    	{
	 		if ($object->getWeekCommencing()!='' && date("w",strtotime($object->getWeekCommencing()))!=1) {
	        	$object->setWeekCommencing(Mage::helper('calendarbase')->getPreviousMonday($object->getWeekCommencing()));
	        }
    	
    }
    
  	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        parent::_afterLoad($object);
  	 	if ($object->getWeekCommencing()=='0000-00-00' || $object->getWeekCommencing()=='00-00-0000') {
        	$object->setWeekCommencing('');
        }
       
    }



}