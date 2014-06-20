<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Info_License extends Aitoc_Aitsys_Model_Module_Info_Abstract
{
    protected function _init()
    {
        $this->_loaded = $this->getModule()->isLicensed();
    }
    
    /**
     * @return string
     */
    public function getVersion()
    {
        return (string)$this->getModule()->getVersion();
    }
    
    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->getModule()->getLicense()->getEntHash() ? self::PLATFORM_EE : self::PLATFORM_CE;
    }
    
    /**
     * @return string
     */
    public function getSerial()
    {
        return $this->getModule()->getPurchaseId();
    }
}