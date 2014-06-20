<?php

class Undottitled_Estimateddeliverydate_Model_Resource_Deliveries extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Intialize resource model.
     * Set main entity table name and primary key field name.
     *
     */
    protected function _construct()
    {
        $this->_init('estimateddeliverydate/deliveries', 'id');
    }
    
    
    public function getDatesBySku($pid)
    {
        
		$select = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))
        	->where('pid = ?', $pid)
        	->order('date','ASC');
		return $this->_getReadAdapter()->fetchAll($select);
    }

}