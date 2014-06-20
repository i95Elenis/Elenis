<?php
/**
 * Main module model
 *
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 *
 * @method bool getAccess()
 * @method bool getValue() 
 * @method string getFile()
 * @method string getLabel()
 * @method string getVersion()
 * @method string getKey()
 * @method bool getAvailable()
 * @method int getLinkId()
 */
final class Aitoc_Aitsys_Model_Module extends Aitoc_Aitsys_Abstract_Model
{
    const PACKAGE_FILE = 'package.xml';
    
    /**
     * Errors storage
     * 
     * @var array
     */
    protected $_errors = array(); 
    
    /**
     * Performer file extension
     * 
     * @var string
     */
    protected $_perfExt;
    
    /**
     * Status correction necessity flag
     * 
     * @var bool
     */
    protected $_needCorrection = false;
    
    /**
     * @var Aitoc_Aitsys_Model_Module_Install
     */
    protected $_install;
    
    /**
     * @var Aitoc_Aitsys_Model_Module_License
     */
    protected $_license;

    /**
     * @var Mage_Admin_Model_Acl
     */
    protected $_adminAcl;
    
    /**
     * @var string
     */
    protected $_licenseVersion = '';
    
    /**
     * @var Aitoc_Aitsys_Model_Module_Info_Abstract
     */
    protected $_info;
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function getPlatform()
    {
        return $this->tool()->platform();
    }
    
    /**
     * Add an error to the storage
     * 
     * @param string $error
     * @return Aitoc_Aitsys_Model_Module
     */
    public function addError( $error )
    {
        $this->_errors[] = $error;
        return $this;
    }
    
    /**
     * Add a number of errors to the storage
     * 
     * @param $errors
     * @return Aitoc_Aitsys_Model_Module
     */
    public function addErrors( array $errors )
    {
        foreach ($errors as $error)
        {
            $this->addError($error);
        }
        return $this;
    }
    
    /**
     * Get all unique errors from the storage and optionally clear the storage
     * 
     * @param bool $clear Do clear errors storage on complete?
     * @return array
     */
    public function getErrors( $clear = false )
    {
        $result = $this->_errors;
        if ($clear)
        {
            $this->_errors = array();
        }
        return array_unique($result);
    }
    
    /**
     * Add all current errors to the session
     * 
     * @param $translator
     * @param Mage_Adminhtml_Model_Session $session
     * @return bool
     */
    public function produceErrors( $translator , Mage_Adminhtml_Model_Session $session = null )
    {
        if (!$session)
        {
            $session = $this->tool()->getInteractiveSession();
        }
        if (!$session)
        {
            $session = Mage::getSingleton('adminhtml/session');
        }
        /* @var $session Mage_Adminhtml_Model_Session */
        foreach ($this->getErrors() as $error)
        {
            if (!is_array($error))
            {
                $error = (array)$error;
            }
            $msg = array_shift($error);
            $session->addError($translator->__($msg));
        }
        return !empty($this->_errors);
    }

    /**
     * Reload the module from an appropriate install and/or module file.
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    public function reset()
    {
        $this->unsAccess();
        $path = $this->getInstall()->getPath();
        if (file_exists($path))
        {
            $this->tool()->testMsg('Reset by install file: '.$path);
            $this->loadByInstallFile($path);
        }
        else
        {
            $this->tool()->testMsg('Reset by module file: '.$this->getFile());
            $this->loadByModuleFile($this->getFile());
        }
        $this->updateStatuses();
        return $this;
    }

    /**
     * Determinate an extension of the performer file if the one exists
     * 
     * @return string
     */
    protected function _getPerfExt($path = '')
    {
        if(is_null($this->_perfExt) && $path)
        {
            $this->_perfExt = 'perf';
            if(!file_exists($path.$this->_perfExt))
            {
                if(file_exists($path.'php'))
                {
                    $this->_perfExt = 'php';
                }
            }
        }
        return $this->_perfExt;
    }
    
    /**
     * Load the module using the license xml file
     * 
     * @param string $path Path to a license xml file
     * @return Aitoc_Aitsys_Model_Module
     */
    public function loadByInstallFile( $path )
    {
        $this->tool()->testMsg("Load by install file: ".$path);
        $xml = simplexml_load_file($path);
        $key = (string)$xml->product->attributes()->key;
        $linkId = (string)$xml->product->attributes()->link_id;
        $file = $this->tool()->filesystem()->getEtcDir().'/'.$key.'.xml';
        $perf = $this->tool()->filesystem()->getLocalDir().str_replace("_", DS, $key).DS."Model".DS."Performer.";
        $perf.= $this->_getPerfExt($perf);
        $this->addData(array(
            'id'        => (int)$xml->product->attributes()->id ,
            'label'     => (string)$xml->product ,
            'store_url' => (string)$xml->store_url,
            'key'       => $key ,
            'link_id'   => $linkId ,
            'value'     => false ,
            'available' => false ,
            'access'    => null ,
            'file'      => $file,
            'perf'      => $perf,
            'version'   => (string)$xml->product->attributes()->version,
            'decode'    => (bool)($this->_getPerfExt()=='perf')
        ))->_setLicenseVersion()
          ->_createInstall()
          ->_createLicense();
        $this->getInstall()->setPath($path);
        $this->getLicense()
            ->setPurchaseId((string)$xml->serial)
            ->setCheckid((string)$xml->checkid)
            ->setServiceUrl((string)$xml->service)
            ->addConstrain($xml->constraint)
            ->setLicenseId((int)$xml->product['license_id'])
            ->setEntHash((string)$xml->product['ent_hash']);
        $this->tool()->event('aitsys_module_load_install_file',array('module' => $this));
        $this->_updateByModuleFile()
             ->_checkCorrectionStatus();
        return $this;
    }

    /**
     * Load the module using the main module config file form /etc/modules folder
     * 
     * @param string $path Path to the module xml file
     * @param string $key Module key
     * @return Aitoc_Aitsys_Model_Module
     */
    public function loadByModuleFile( $path , $key = null )
    {
        if (!$key) {
            $key = basename($path, '.xml');
        }

        $this->addData(array(
            'key'       => $key,
            'available' => true ,
            'file'      => $path ,
            'version'   => (string)Mage::getConfig()->getNode('modules')->{$key}->version
        ))->_updateByModuleFile()
          ->_createInstall()
          ->_checkCorrectionStatus();
        return $this;
    }
    
    /**
     * Load the module and license using the data stored in magento cache
     * 
     * @param array $data
     * @return Aitoc_Aitsys_Model_Module
     */
    /* removed from 2.20
    public function loadFromCache(array $data)
    {
        $license_data = null;
        if(isset($data['license_data']))
        {
            $license_data = $data['license_data'];
            unset($data['license_data']);
        }
        $this->setData($data);
        $this->_createInstall()->_createLicense();

        if($this->getId() && $license_data)
        {
            $this->getLicense()->setData($license_data);
        }
        $this->_checkCorrectionStatus();
        return $this;
    }
    */
    
    /**
     * Prepare and return module's and license's data to be cached into magento cache
     * 
     * @return array
     */
    /* removed from 2.20
    public function getDataForCache()
    {
        $data = $this->getData();
        if($this->getId() && $this->getLicense())
        {
            $data['license_data'] = $this->getLicense()->getData(); 
        }
        return $data;
    }
    */
    
    /**
     * Check whether the module needs correction
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _checkCorrectionStatus()
    {
        if(version_compare($this->tool()->db()->dbVersion(), '2.15.6', 'ge'))
        {
            $dbStatus  = $this->tool()->db()->getStatus($this->getKey());
            $xmlStatus = $this->getValue();
            if($dbStatus !== $xmlStatus)
            {
                $this->_needCorrection = true;
                $this->tool()->platform()->setNeedCorrection();
            }
        }

        return $this;
    }
    
    /**
     * Whether the module needs correction?
     * 
     * @return bool
     */
    public function isNeedCorrection()
    {
        return $this->_needCorrection;
    }
    
    /**
     * Get module/product id
     * 
     * @return int 
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Does this module have a license?
     * 
     * @return bool
     */
    public function isLicensed()
    {
        return (bool)$this->getId();
    }
    
    /**
     * Whether the module can be enabled or not?
     * 
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getAvailable();
    }

    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    public function updateStatuses()
    {
        $this->getInstall()->setStatusUnknown();
        $license = $this->getLicense();
        if ($license)
        {
            $this->tool()->testMsg("Module status update: Unset performer");
            $license->setStatusUnknown()->unsPerformer();
        }

        $this->_updateInstallStatus()->_updateLicenseStatus();
        if($this->getPlatform()->isCheckAllowed())
        {
            $this->tool()->event('aitsys_module_checkstatus_after',array('module' => $this));
        }
        $license = $this->getLicense();
        if (!$license || $license->isInstalled())
        {
            $this->setAvailable(true);
        }
        else
        {
            $this->setAvailable(false);
        }
        return $this;
    }

    /**
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function getLicense()
    {
        return $this->_license;
    }

    /**
     * @return Aitoc_Aitsys_Model_Module_Install
     */
    public function getInstall()
    {
        return $this->_install;
    }

    public function getSourcePath( $suffix = '' )
    {
        return $this->getInstall()->getSourcePath($suffix);
    }

    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _updateByModuleFile()
    {
        if (null === $this->getAccess()) {
            $path = $this->getFile();
            $key  = $this->getKey();
            if (file_exists($path)) {
                $xml  = simplexml_load_file($path);
                $this->tool()->testMsg('Update module by config file: '.$key);
    
                if (!$this->getLabel()) {
                    $this->setLabel((string)$xml->modules->$key->self_name ? (string)$xml->modules->$key->self_name : $key);
                }
                
                if ($this->getPlatform()->isCheckAllowed()) {
                    $access = $this->tool()->filesystem()->checkWriteable($path);
                } else {
                    $access = true;
                }
                $this->setValue('true' == (string)$xml->modules->$key->active)
                     ->setAccess($access);
            } else {
                $this->setValue(false)
                     ->setAccess(!$this->getPlatform()->isCheckAllowed());
            }
        }
        return $this;
    }

    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _updateInstallStatus()
    {
        $this->getInstall()->checkStatus();
        return $this;
    }

    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _updateLicenseStatus()
    {
        if ($license = $this->getLicense())
        {
            $license->checkStatus();
        }
        return $this;
    }

    /**
     * Create model instance and set it to the appropriate property 
     * 
     * @param string $name
     * @param string $class
     * 
     * @return Aitoc_Aitsys_Model_Module_Abstract
     */
    protected function _getInstance($name)
    {
        $model_class = 'Aitoc_Aitsys_Model_Module_' . ucfirst($name);

        if ($licenseVersion = $this->_getLicenseVersion()) {
            $model_class .= '_' . ucfirst($licenseVersion);
        }
        
        $model = new $model_class();
        $model->setModule($this)->init();
        $this->tool()->testMsg("Child class created: " . $model_class);
        $this->{'_'.$name} = $model;
        
        return $model;
    }

    /**
     * Init and return the license model if necessary
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _createLicense()
    {
        if (!$this->_license)
        {
            $this->tool()->testMsg("Create license object");
            if ($this->getId()) {
                $this->_getInstance('license');
            }
        }
        return $this;
    }

    /**
     * Init and return the install model
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _createInstall()
    {
        if (!$this->_install) {
            $this->_getInstance('install');
        }
        return $this;
    }
    
    /**
     * Init license source if this module is licensed 
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    public function initSource()
    {
        if ($license = $this->getLicense())
        {
            $license->getPerformer();
        }
        return $this;
    }
    
    /**
     * Get module ACL definitions
     * 
     * @return Mage_Admin_Model_Acl
     */
    public function getAdminAcl()
    {
        if (!$this->_adminAcl)
        {
            $config = clone Mage::getConfig();
            $config->reinit();
            $config->setNode('adminhtml/acl/resources', '');
            $file = $config->getModuleDir('etc', $this->getKey()).DS.'config.xml';
            $config->loadFile($file);
            $node = $config->getNode('adminhtml/acl/resources');
            if ($node === false)
                return false;
                
            $acl = Mage::getModel('admin/acl');
            /* @var $acl Mage_Admin_Model_Acl */
            Mage::getSingleton('admin/config')->loadAclResources($acl, $node);
            $this->_adminAcl = $acl;
        }
        
        return $this->_adminAcl;
    }
    
    /**
     * Check if module has 'All' acl configuration
     *
     * @return bool
     */
    public function hasAllAdminAcl()
    {
        $acl = $this->getAdminAcl();
        return $acl->has('all') || $acl->has('acl/admin');
    }
    
    /**
     * Check if module has acl configuration
     *
     * @param string $resource resource name
     * @return bool 
     */
    public function hasAdminAcl($resource)
    {
        $acl = $this->getAdminAcl();
        if (!preg_match('#^acl/#', $resource))
        {
            $resource .= 'acl/';
        }
        return $acl->has($resource);
    }

    /**
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _setLicenseVersion()
    {
        if (file_exists($this->getPerf())) {
            $this->_licenseVersion = 'light';
        }
        return $this;
    }
    
    /**
     * @return string
     */
    protected function _getLicenseVersion()
    {
        return $this->_licenseVersion;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module_Info_Abstract
     */
    public function getInfo()
    {
        if (is_null($this->_info) && $this->getKey()) {
            try {
                $this->_info = Aitoc_Aitsys_Model_Module_Info_Factory::getModuleInfo($this);
            } catch (Aitoc_Aitsys_Model_Module_Info_Exception $e) {
                $this->tool()->testMsg($e->getMessage());
            }
        }
        return $this->_info;
    }
}