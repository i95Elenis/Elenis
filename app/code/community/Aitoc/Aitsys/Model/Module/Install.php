<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Module_Install extends Aitoc_Aitsys_Model_Module_Abstract
{
    /**
     * @return Aitoc_Aitsys_Model_Module_Install
     */
    public function checkStatus()
    {
        $this->setInstallable(file_exists($this->getModule()->getFile()));
        $this->setStatusUninstalled();
        if ($this->isInstallable() && $this->getModule()->getValue()) {
            $this->setStatusInstalled();
        }
        $this->tool()->testMsg('Module installation status set to: ' . $this->getModule()->getKey() . ' - ' . $this->getStatus());
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isInstallable()
    {
        return $this->getInstallable();
    }

    /**
     * @return Aitoc_Aitsys_Model_Module_Install
     */
    public function uninstall()
    {
        $this->tool()->testMsg('UNINSTALL MODULE: ' . $this->getModule()->getKey());
        $this->_processModule(false);
        return $this;
    }

    /**
     * @return Aitoc_Aitsys_Model_Module_Install
     */
    public function install()
    {
        $this->tool()->testMsg('INSTALL MODULE: ' . $this->getModule()->getKey());
        $this->_processModule(true);
        return $this;
    }
    
    /**
     * @param bool $status Module new status. True to install and False to uninstall
     */
    protected function _processModule($status)
    {
        $data = array();
        foreach ($this->tool()->platform()->getModuleKeysForced() as $module => $value) {
            /* @var $module Aitoc_Aitsys_Model_Module */
            $isCurrent = $module === $this->getModule()->getKey();
            $data[$module] = $isCurrent ? $status : $value;
        }
        
        $aitsysModel = new Aitoc_Aitsys_Model_Aitsys();
        if ($errors = $aitsysModel->saveData($data, array(), true)) {
            $this->addErrors($errors);
        }
        $this->getModule()->reset();
        $this->tool()->clearCache();
    }
}