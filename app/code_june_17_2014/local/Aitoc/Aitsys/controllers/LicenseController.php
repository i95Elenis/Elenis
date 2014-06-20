<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_LicenseController extends Aitoc_Aitsys_Abstract_Adminhtml_Controller
{
    protected $_usedModuleName = 'aitsys';
    
    protected $_prepared = false;
    
    /**
     * @return Aitoc_Aitsys_LicenseController
     */
    protected function _prepare()
    {
        if (!$this->_prepared)
        {
            $key = $this->getRequest()->getParam('modulekey');
            $this->tool()->platform()->setData('mode', 'live');
            Mage::register('aitoc_module', $this->tool()->platform()->getModule($key));
            $this->_prepared = true;
        }
        return $this;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _getModule()
    {
        return Mage::registry('aitoc_module');
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module_License
     */
    protected function _getLicense()
    {
        return $this->_getModule()->getLicense();
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_LicenseController
     */
    protected function _prepareLayout()
    {
        $this->_prepare()
            ->loadLayout()
            ->_setActiveMenu('system/aitsys')
            ->_setTitle(array(
                Mage::helper('aitsys')->__('License Management'),
                Mage::helper('aitsys')->__('Aitoc Modules Manager')
             ));
        return $this;
    }
    
    public function deleteAction()
    {
        $this->_prepare();
        $license = $this->_getLicense();
        $license->uninstall();
        if (!$license->isUninstalled())
        {
            if ($this->_getModule()->produceErrors($this,$this->_getSession()))
            {
                $this->_redirect('*/*/manage',array('modulekey' => $this->_getModule()->getKey()));
                return;
            }
        }
        $this->_getSession()->addSuccess($this->__('License deleted, `%s` module uninstalled.',$this->_getModule()->getLabel()));
        $this->_redirect('*');
    }
    
    public function reInstallAction()
    {
        $this->_prepare();
        $license = $this->_getLicense();        
        if ($license->reInstall()->isInstalled())
        {
            $install = $license->getInstall();
            if ($install->isInstalled())
            {
                $this->_getSession()->addSuccess($this->__('License of %s has been re-installed.',$this->_getModule()->getLabel()));
            }
            else 
            {
                $this->_getSession()->addWarning($this->__('License of %s hasn\'t been re-installed.',$this->_getModule()->getLabel()));
                $this->_getModule()->produceErrors($this,$this->_getSession());
                $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
                if ($notices = Mage::getModel('aitsys/aitpatch')->getCompatiblityError($aModuleList))
                {
                    foreach ($notices as $notice)
                    {
                        $this->_getSession()->addNotice($notice);
                    }
                }
            }
            $this->_redirect('*');
        }
        else
        {
            if(!$this->_getModule()->produceErrors($this, $this->_getSession())) {
                $helper = $this->_aithelper('Strings');
                $this->_getSession()->addError($helper->getString('ER_MODULE_CS'));
            }
            $this->getRequest()->setParam('confirmed',true);
            $this->_prepareLayout()->renderLayout();
        }
    }    
    
    public function upgradeAction()
    {
        $this->_prepare();
        $this->_getLicense()->upgrade();
        if ($this->_getModule()->produceErrors($this,$this->_getSession()))
        {
            $this->_redirect('*/*/manage',array('modulekey' => $this->_getModule()->getKey()));
        }
        else
        {
            $this->_getSession()->addSuccess($this->__('New license for `%s` installed.',$this->_getModule()->getLabel()));
            $this->_redirect('*/*/manage',array('modulekey' => $this->_getModule()->getKey()));
        }
    }
    
    public function installAction()
    {
        $this->_prepare();
        $license = $this->_getLicense();
        if ($license->install()->isInstalled())
        {
            $install = $license->getInstall();
            if ($install->isInstalled())
            {
                $this->_getSession()->addSuccess($this->__('License and module %s installed.',$this->_getModule()->getLabel()));
            }
            else 
            {
                $this->_getSession()->addWarning($this->__('License of %s module has been installed.',$this->_getModule()->getLabel()));
                $this->_getModule()->produceErrors($this,$this->_getSession());
                $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
                if ($notices = Mage::getModel('aitsys/aitpatch')->getCompatiblityError($aModuleList))
                {
                    foreach ($notices as $notice)
                    {
                        $this->_getSession()->addNotice($notice);
                    }
                }
            }
            $this->_redirect('*');
        }
        else
        {
            if(!$this->_getModule()->produceErrors($this, $this->_getSession())) {
                $helper = $this->_aithelper('Strings');
                $this->_getSession()->addError($helper->getString('ER_MODULE_CS'));
            }
            $this->getRequest()->setParam('confirmed',true);
            $this->_prepareLayout()->renderLayout();
        }
    }
    
    public function confirmAction()
    {
        $platform = $this->tool()->platform(); 
        $this->_prepare();
        if (!$platform->isModePresetted())
        {
            $testMode = 'test' == $this->getRequest()->getParam('installation_type');
            $platform->setTestMode($testMode);
            $platform->save();
        }
        $this->_redirect('*/*/manage',array(
            'modulekey' => $this->_getModule()->getKey() ,
            'confirmed' => true
        ));
    }
    
    public function manageAction()
    {
        $this->_prepare();
        $license = $this->_getLicense();
        $request = $this->getRequest();
        
        if($request->getParam('newlicense') && !$request->getParam('confirmed')) {
            Mage::getSingleton('adminhtml/session')->addNotice( $this->_aithelper('Strings')->getString('CHANGE_LICENSE_AGREEMENT') );
        }
        
        if($license->isUninstalled() && $request->getParam('confirmed') &&
           $license instanceof Aitoc_Aitsys_Model_Module_License_Light && !$license->getPerformer())
        {
            Mage::getSingleton('adminhtml/session')->addError( $this->_aithelper('Strings')->getString('ER_PERFORMER', true, $license->getModule()->getPerf()) );
        }

        $this->_prepareLayout()->renderLayout();
    }
    public function manualInstallAction()
    {
        $this->_prepareLayout()->renderLayout();
    }    

    public function manualInstallUploadAction()
    {
        $this->_prepare();
        
        $turnOnModule = false;
        if(!$this->_getModule()->getValue() && $this->_getLicense()->isUninstalled())
        {
            $turnOnModule = true;
        }
        
        if(isset($_FILES['license_file']['name']) && $_FILES['license_file']['name'] != '')
        {
            try
            {
                $path = Mage::getBaseDir('var');  
                $fname = $_FILES['license_file']['name'];
                $uploader = new Varien_File_Uploader('license_file'); 
                $uploader->setAllowedExtensions(array('sql','php')); 
                $uploader->setAllowCreateFolders(true); 
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path,$fname);
        
                switch(pathinfo($fname, PATHINFO_EXTENSION))
                {
                    case 'php':
                        copy($path.DS.$fname, $path.DS.'ait_install'.DS.$this->_getLicense()->getPlatform()->getPlatformId().DS.$fname);
                    break;
                    
                    case 'sql':
                    default:
                        $sql = file_get_contents($path.DS.$fname);
                        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $writeConnection->query($sql);
                    break;
                }
                unlink($path.DS.$fname);
                $this->_getModule()->updateStatuses();
                if(!$this->_getLicense()->isUninstalled())
                {
                    if($turnOnModule)
                    {
                        $data = array();
                        foreach ($this->tool()->platform()->getModuleKeysForced() as $module => $value)
                        {
                            /* @var $module Aitoc_Aitsys_Model_Module */
                            $isCurrent = $module === $this->_getModule()->getKey();
                            $data[$module] = $isCurrent ? true : $value;
                        }
                        
                        $aitsysModel = new Aitoc_Aitsys_Model_Aitsys();
                        $errors = $aitsysModel->saveData($data,array(),true);
                        if($errors)
                        {
                            foreach($errors as $error)
                            {
                                Mage::getSingleton('adminhtml/session')->addError($this->__($error));
                            }
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__('License of %s module has been installed.',$this->_getModule()->getLabel()));
                }
                else
                {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Unknown error. Please retry the operation again. If installation fails, contact support department.'));
                }
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('No file uploaded.'));
        }
        $this->_redirect('*');
    }
}