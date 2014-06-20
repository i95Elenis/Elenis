<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */
class Aitoc_Aitsys_Model_Module_License_Light_Performer_Closed
extends Aitoc_Aitsys_Abstract_Model
{
    protected $_eventPrefix = 'aitsys_performer_closed';
    
    protected function _construct()
    {
        $this->_init('aitsys/module_license_light_performer_closed');
    }
    
    /**
     * @override
     */
    public function load($id, $field=null)
    {
        if(version_compare($this->tool()->db()->dbVersion(),'2.15.0','ge'))
        {
            return parent::load($id, $field);
        }
        return $this;
    }
    
    /**
     * Prevents after load event in Mage_Core_Model_Abstract from launching
     * (compatibility with some modules).
     * 
     * @return Aitoc_Aitsys_Model_Module_License_Light_Performer_Closed
     */
    protected function _afterLoad()
    {
        return $this;
    }
}