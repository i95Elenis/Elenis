<?php
class Aitoc_Aitsys_Model_Mysql4_Module_License_Light_Performer_Closed extends Aitoc_Aitsys_Abstract_Mysql4
{
    protected function _construct()
    {
        $this->_init('aitsys/performer','id');
    }
    
    protected function _getAdminPathHash()
    {
        $platform = Aitoc_Aitsys_Model_Module_License_Light_Platform::getInstance();
        return md5($platform->getAdminUrl());
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if(Mage::getStoreConfigFlag('aitsys/settings/multiple_admin_urls')) {
            $hash = $this->_getAdminPathHash();
            $select->where("(path_hash = '". $hash ."' OR path_hash = '')")
                ->order('path_hash DESC')
                ->limit(1);
        }
        return $select;
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setData('path_hash', $this->_getAdminPathHash());
        return parent::_beforeSave($object);
    }
}