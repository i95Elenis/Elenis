<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
final class Aitoc_Aitsys_Model_Platform extends Aitoc_Aitsys_Abstract_Model
{
    const PLATFORMFILE_SUFFIX = '.platform.xml';
    const INSTALLATION_DIR    = '/ait_install/';
    const CACHE_CLEAR_VERSION = '2.21.0';
    const DEFAULT_VAR_PATH    = 'var';
    
    /**
     * @var Aitoc_Aitsys_Model_Platform
     */
    static protected $_instance;
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
            self::$_instance->preInit();
        }
        return self::$_instance;
    }
    
    protected $_block = false;
    
    protected $_inited = false;
    
    protected $_modulesList; // Module_Name => array( 'module_path' => Module_Path, 'module_file' => Module_File )
    
    protected $_modules = array();
    
    protected $_version;
    
    protected $_installDir;
    
    protected $_licenseDir; // rastorguev fix
    
    protected $_copiedPlatformFiles = array();
    
    /**
     * @var Aitoc_Aitsys_Model_License_Service
     */
    protected $_service = array();
    
    protected $_moduleIgnoreList = array('Aitoc_Aitinstall' => 0, 'Aitoc_Aitsys' => 0, 'Aitoc_Aitprepare' => 0);
    
    protected $_aitocPrefixList = array('Aitoc_', 'AdjustWare_');
    
    protected $_moduleDirs = array('Aitoc', 'AdjustWare');
    
    protected $_reloaded = false;
    
    protected $_needCorrection = false;
    
    protected $_adminError = '';
    
    protected $_adminErrorEventLoaded = false;
    
    protected $_addEntHash;
    
    public function addAdminError($message)
    {
        $this->_adminError = $message;
        $this->renderAdminError();
    }
    
    public function renderAdminError($eventLoaded = false)
    {
        if ($eventLoaded) {
            $this->_adminErrorEventLoaded = true;
        }
        
        if ($this->_adminErrorEventLoaded && $this->_adminError) {
            $admin = Mage::getSingleton('admin/session');
            if ($admin->isLoggedIn()) {
                $session = Mage::getSingleton('adminhtml/session');
                /* @var $session Mage_Adminhtml_Model_Session */
                $session->addError($this->_adminError);
                $this->_adminError = '';
            }
        }
        return $this;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function block()
    {
        $this->_block = true;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getModuleDirs()
    {
        return $this->_moduleDirs;
    }
    
    /**
     * @param string $namespace
     * @param bool $compare
     * @return bool
     */
    /*
    protected function _isAitocNamespace($namespace, $compare = false)
    {
        if ($compare) {
            return in_array($namespace,$this->_moduleDirs);
        }
        foreach ($this->_moduleDirs as $dir) {
            if (false !== strstr($namespace, $dir)) {
                return true;
            }
        }
        return false;
    }
    */
    
    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->_block;
    }
    
    /**
     * @return array
     */
    public function getModules()
    {
        if (!$this->_modules) {
            $this->__init();
            $this->_generateModulesList();
        }
        return $this->_modules;
    }
    
    /**
     * @return array
     */
    public function getModuleKeysForced()
    {
        $modules = array();
        foreach($this->_modulesList as $moduleKey => $moduleData) {
            $modules[$moduleKey] = ('true' == (string)Mage::getConfig()->getNode('modules/' . $moduleKey . '/active'));
        }
        return $modules;
    }
    
    /**
     * @param string $key
     * @return Aitoc_Aitsys_Model_Module
     */
    public function getModule($key)
    {
        $this->getModules();
        return isset($this->_modules[$key]) ? $this->_modules[$key] : null;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_License_Service
     */
    public function getService($for = 'default')
    {
        if (!isset($this->_service[$for])) {
            $this->_service[$for] = new Aitoc_Aitsys_Model_License_Service();
            $this->_service[$for]->setServiceUrl($this->getServiceUrl());
        }
        return $this->_service[$for];
    }
    
    /**
     * @return bool
     */
    public function isCheckAllowed()
    {
        return !$this->hasData('no_check');
    }
    
    public function getVarPath()
    {
        return $this->hasData('var_path') ? trim($this->getData('var_path'), '\\/') : self::DEFAULT_VAR_PATH;
    }
    
    /**
     * @return string
     */
    public function getServiceUrl()
    {
        if ($url = $this->tool()->getApiUrl()) {
            return $url;
        }
        if ($url = $this->getData('_service_url')) {
            return $url;
        }
        $url = $this->getData('service_url');
        return $url ? $url : Mage::getStoreConfig('aitsys/service/url');
    }
    
    /**
     * @return string
     */
    public function getVersion()
    {
        if (!$this->_version) {
            $this->_version = (string)Mage::app()->getConfig()->getNode('modules/Aitoc_Aitsys/version'); 
        }
        return $this->_version;
    }
    
    /**
     * @param $mode
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function setTestMode($mode = true)
    {
        if (!$this->isModePresetted()) {
            $this->setData('mode', $mode ? 'test' : 'live');
        }
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isModePresetted()
    {
        return $this->hasData('mode');
    }
    
    /**
     * @return bool
     */
    public function isTestMode() 
    {
        return $this->getData('mode') == 'test';
    }
    
    /**
     * @param bool $base
     * @return string
     */
    public function getInstallDir($base = false)
    {
        if (!$this->_installDir) {
            $this->_installDir = $this->tool()->filesystem()->getAitsysDir() . '/install/';
        }
        return $this->_installDir;
    }
    
    /**
     * @param bool $base 
     * @return string
     * @throws Aitoc_Aitsys_Model_Aitfilesystem_Exception
     */
    public function getLicenseDir($base = false)
    {
        if (!$this->_licenseDir) {
            $this->_licenseDir = BP . DS . $this->getVarPath() . self::INSTALLATION_DIR;
            
            if (!$this->tool()->filesystem()->isWriteable($this->_licenseDir)) {
                throw new Aitoc_Aitsys_Model_Aitfilesystem_Exception($this->_licenseDir . " should be writeable");
            }
            
            if (!file_exists($this->_licenseDir)) {
                $this->tool()->filesystem()->mkDir($this->_licenseDir);
            }
        }
        if ($base) {
            return $this->_licenseDir;
        }
        return rtrim($this->_licenseDir . $this->getPlatformId(), '/') . '/';
    }
    
    /**
     * @return null|string
     */
    public function getPlatformId()
    {
        return $this->getData('platform_id');
    }
    
    /**
     * @param string $platformId
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function setPlatformId($platformId)
    {
        return $this->setData('platform_id', $platformId);
    }

    public function preInit()
    {
        try {
            $this->_loadConfigFile();
        } catch (Exception $e) {
            $this->tool()->testMsg('Error on attempt of loading platform config file');
        }
    }
    
    protected function __init()
    {
        if (!$this->_inited) {
            $this->_inited = true;
            try {
                try {
                    // $this->setData($this->tool()->getCache()->load('aitsys_platform', array())); // removed from 2.20
                    if(!$this->getPlatformId()) {
                        $this->_fixOldPlatform();
                        if (!$this->_loadPlatformData()->getPlatformId() && $this->isCheckAllowed()) {
                            $this->_registerPlatform();
                        }
                        
                        /* removed from 2.20 
                        if (!empty($this->_data)) {
                            $this->tool()->getCache()->save($this->getData(), 'aitsys_platform');
                        }
                        */
                    }
                    $this->_checkNeedCacheCleared();
                    $this->reset();
                } catch (Exception $exc) {
                    $this->block();
                    throw $exc;
                }
            } catch (Aitoc_Aitsys_Model_Aitfilesystem_Exception $exc) {
                $msg = "Error in the file: %s. Probably it does not have write permissions.";
                $this->addAdminError(Aitoc_Aitsys_Abstract_Service::get()->getHelper()->__($msg, $exc->getMessage()));
            }
        }
    }
    
    protected function _registerPlatform()
    {
        if (!$this->tool()->getCache()->load('aitsys_platform_registration_attempted', 0, false)) { 
            $license = $this->_getAnyLicense();
            $service = $license ? $license->getService() : $this->getService();
            
            $this->tool()->testMsg('begin register platform');
            
            try {
                $service->connect();
                
                $data = array(
                    'purchaseid'          => $license ? $license->getPurchaseId() : '',
                    'initial_module_list' => $this->getModulePurchaseIdQuickList()
                );
                $platformId = $service->registerPlatform($data);
                
                $service->disconnect();
                $this->tool()->testMsg('Generated platform id: ' . $platformId);
                $this->setPlatformId($platformId);
                $this->setServiceUrl($service->getServiceUrl());
                $this->_savePlatformData();
                $this->_copyToPlatform($platformId);
                $this->unsPlatformId();
                $this->_loadPlatformData();
            } catch (Exception $exc) {
                $this->tool()->testMsg($exc);
            }
        }
        $this->tool()->getCache()->save(1, 'aitsys_platform_registration_attempted', false, 3600); // for an hour - api protection
    }
    
    protected function _checkNeedCacheCleared()
    {
        if (!Mage::app()->getUpdateMode() && version_compare($this->tool()->db()->dbVersion(), self::CACHE_CLEAR_VERSION, 'lt')) {
            $this->tool()->clearCache();
        }
    }
    
    protected function _fixOldPlatform()
    {
        if (!$this->tool()->getCache()->load('aitsys_old_platform_fixed', 0, false)) {
            $installDir = $this->getInstallDir(true);
            if ($platforms = glob($installDir . '*'. self::PLATFORMFILE_SUFFIX)) {
                foreach ($platforms as $platformFile) {
                    $platformId = $this->_castPlatformId($platformFile);
                    $platformDir = $this->getLicenseDir() . $platformId;
                    $this->tool()->filesystem()->makeDirStructure($platformDir);
                    $oldPlatformDir = $this->getInstallDir() . $platformId;
                    if ($pathes = glob($oldPlatformDir . '/*')) {
                        foreach ($pathes as $path) {
                            $fileinfo = pathinfo($path);
                            if ('xml' == $fileinfo['extension']) {
                                $to = $this->getInstallDir().$fileinfo['basename'];
                            } else {
                                $to = $this->getLicenseDir() . $platformId . "/" . $fileinfo['basename'];
                            }
                            $this->tool()->filesystem()->moveFile($path,$to);
                        }
                    }
                    $this->tool()->filesystem()->moveFile($platformFile, $platformDir . self::PLATFORMFILE_SUFFIX);
                    $this->tool()->filesystem()->rmFile($oldPlatformDir);
                }
            }
            $this->tool()->getCache()->save(1, 'aitsys_old_platform_fixed', false, 0);  // cache permanently
        }
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function save()
    {
        return $this->_savePlatformData();
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function reset()
    {
        $this->_modules = array(); // to reinit all licensed modules after platform registration
        foreach ($this->getModules() as $module) {
            $this->tool()->testMsg('Update module ' . $module->getLabel() . ' status after generating');
            $module->updateStatuses();
        }
        return $this;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function reload()
    {
        if ($this->_reloaded) {
            return $this;
        }
        
        foreach ($this->_modules as $module) {
            $license = $module->getLicense();
            if ($license && !$license->isLight()) {
                continue;
            }
            
            if (!$license || $license->checkStatus()->isInstalled()) {
                $module->setAvailable(true);
            } else {
                $module->setAvailable(false);
            }
        }        
        $this->_reloaded = true;
        return $this;
    }    
    
    /**
     * @param string $moduleKey
     * @return bool
     */
    public function isIgnoredModule($moduleKey)
    {
        return isset($this->_moduleIgnoreList[$moduleKey]);
    }
    
    protected function _isPlatformFileName($filename)
    {
        return preg_match('/' . preg_quote(self::PLATFORMFILE_SUFFIX) . '$/', $filename);
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module_License | null
     */
    protected function _getAnyLicense()
    {
        $path = $this->getInstallDir() . '*.xml';
        if ($pathes = glob($path)) {
            foreach ($pathes as $path) {
                if (!$this->_isPlatformFileName($path)) {
                    $module = $this->_makeModuleByInstallFile($path);
                    return $module->getLicense();
                }
            }
        }
    }
    
    /**
     * @return array
     */
    public function getModulePurchaseIdQuickList()
    {
        $this->_createModulesList()->_loadLicensedModules();
        $list = array();
        foreach($this->_modules as $module) {
            $list[$module->getKey()] = $module->getLicense()->getPurchaseId(); 
        }
        return $list;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _loadConfigFile()
    {
        $path = dirname($this->getInstallDir(true)) . '/config.php';
        $this->tool()->testMsg('check config path: ' . $path);
        if (file_exists($path)) {
            include $path;
            if (isset($config) && is_array($config)) {
                $this->tool()->testMsg('loaded config:');
                $this->tool()->testMsg($config);
                $this->setData($config);
            }
        }
        return $this;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _generateModulesList()
    {
        $this->tool()->testMsg('Try to generate modules list!'); 
        $this->_createModulesList();
        // if(!$this->_loadModulesFromCache()) { // removed from 2.20
            $this->_loadLicensedModules()
                 ->_loadAllModules();
        /* removed from 2.20
            $this->_saveModulesToCache();
        }
        */

        $this->tool()->event('aitsys_generate_module_list_after');
        $this->tool()->testMsg('Modules list generated');
        return $this;
    }
    
    /**
     * Attempt to load modules' entities from cache.
     * 
     * @return bool
     */
    /* removed from 2.20
    protected function _loadModulesFromCache()
    {
        $this->_modules = array();
        $data = $this->tool()->getCache()->load('aitsys_modules', array());
        foreach($data as $moduleKey => $moduleData) {
            $module = new Aitoc_Aitsys_Model_Module();
            $this->_modules[$moduleKey] = $module->loadFromCache($moduleData);
        }
        return !empty($this->_modules);
    }
    */
    
    /**
     * Attempt to save modules to cache
     */
    /* removed from 2.20
    protected function _saveModulesToCache()
    {
        $cacheData = array();
        foreach($this->_modules as $moduleKey => $module) {
            $cacheData[$moduleKey] = $module->getDataForCache();
        }
        $this->tool()->getCache()->save($cacheData, 'aitsys_modules');
    }
    */
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _loadLicensedModules()
    {
        $this->_loadLicensedModulesOld();
        foreach ($this->_modulesList as $moduleKey => $moduleData) {
            if (isset($this->_modules[$moduleKey])) {
                // module already loaded from the old-format license file
                // in the _loadLicensedModulesOld method
                continue;
            }
            
            $licenseFile = $moduleData['module_path'] . '/etc/' . Aitoc_Aitsys_Model_Module_License::LICENSE_FILE;
            if(!@is_file($licenseFile)) {
                // module license file not found in the module's `etc` folder
                continue;
            }
            
            // loading module
            $this->tool()->testMsg("Try load licensed module");
            $module = $this->_makeModuleByInstallFile($licenseFile);
            $this->_addLicensedModule($module);
        }
        
        return $this;
    }
    
    /**
     * Compatibility with Aitsys older then 2.18.0
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _loadLicensedModulesOld()
    {
        if (!file_exists($this->getInstallDir())) {
            return $this;
        }
        
        $dir = new DirectoryIterator($this->getInstallDir());
        foreach ($dir as $item) {
            /* @var $item DirectoryIterator */
            if ($item->isFile()) {
                $filename = $item->getFilename();
                if ('.xml' === substr($filename, -4, 4)) {
                    if ($this->_isPlatformFileName($filename) || $this->_isUpgradeFilename($filename)) {
                        continue;
                    }
                    
                    // loading module
                    $this->tool()->testMsg("Try load licensed module with old license file");
                    $module = $this->_makeModuleByInstallFile($item->getPathname());
                    $this->_addLicensedModule($module);                 
                }
            }
        }
        return $this;
    }
    
    protected function _addLicensedModule(Aitoc_Aitsys_Model_Module $module)
    {
        if ((!$this->_addEntHash() && $module->getLicense()->getEntHash()) || ($this->_addEntHash() && !$module->getLicense()->getEntHash())) {
            $this->_moduleIgnoreList[$module->getKey()] = 'ER_ENT_HASH';
            return;
        }
    
        $key = $module->getKey();
        $this->tool()->testMsg("Try load licensed module finished: ".$key);
        if (!isset($this->_modules[$key])) {
            $this->tool()->testMsg("Add new module");
            $this->_modules[$key] = $module;
        } else {
            $this->tool()->testMsg("Reset existed module");
            $this->_modules[$key]->reset();
        }
    }
    
    /**
     * @return bool
     */
    protected function _addEntHash()
    {
        if (is_null($this->_addEntHash)) {
            $this->_addEntHash = false;
            
            $etcDir = $this->tool()->fileSystem()->getEtcDir();
            $eeModuleXmlFile = $etcDir . DS . 'Enterprise_Enterprise.xml';
            if(file_exists($eeModuleXmlFile)) {
                try {
                    $eeModule = new SimpleXMLElement($eeModuleXmlFile, 0, true);
                    $val = $eeModule->modules->Enterprise_Enterprise->active;
                    $this->_addEntHash = ((string)$val == 'true');
                } catch (Exception $e) {}
            }
        }
        return $this->_addEntHash;
    }
    
    /**
     * @return bool
     */
    public function getEntHash()
    {
        return $this->_addEntHash();
    }
    
    /**
     * @return bool
     */
    protected function _isUpgradeFilename($filename)
    {
        return false !== strstr($filename, '.upgrade-license.xml');
    }
    
    /**
     * Load certain module by using its license file
     * 
     * @param string $path Path to license .xml file
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _makeModuleByInstallFile($path)
    {
        $module = new Aitoc_Aitsys_Model_Module();
        $module->loadByInstallFile(str_replace('.php', '.xml', $path));
        $this->tool()->testMsg(get_class($module->getLicense()));
        $this->tool()->event('aitsys_create_module_after', array('module' => $module));
        return $module;
    }
    
    /**
     * Based on /code/local/Aitoc|AdjustWare subfolders
     * 
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _createModulesList()
    {
        if (is_null($this->_modulesList)) {
            $this->_modulesList = array();
            
            /* removed from 2.20 
            $this->_modulesList = $this->tool()->getCache()->load('aitsys_modules_list', array());
            if(!empty($this->_modulesList)) {
                return $this;
            }
            */
            
            $aitocModulesDirs = $this->tool()->filesystem()->getAitocModulesDirs();
            foreach ($aitocModulesDirs as $aitocModuleDir) {
                if (@file_exists($aitocModuleDir) && @is_dir($aitocModuleDir)) {
                    $aitocModuleSubdirs = new DirectoryIterator($aitocModuleDir);
                    foreach ($aitocModuleSubdirs as $aitocModuleSubdir) {
                        // skip dots and svn folders
                        if (in_array($aitocModuleSubdir->getFilename(), $this->tool()->filesystem()->getForbiddenDirs())) {
                            continue;
                        }
                        
                        $moduleKey  = basename($aitocModuleDir) . "_" . $aitocModuleSubdir->getFilename();
                        if (!$this->isIgnoredModule($moduleKey)) {
                            $moduleFile = $this->tool()->filesystem()->getEtcDir() . "/{$moduleKey}.xml";
                            $this->_modulesList[$moduleKey] = array(
                                'module_path' => $aitocModuleSubdir->getPathname(),
                                'module_file' => @is_file($moduleFile) ? $moduleFile : null
                            );
                        }
                    }
                }
            }
            // $this->tool()->getCache()->save($this->_modulesList, 'aitsys_modules_list'); // removed from 2.20
        }
        return $this;
    }
    
    /**
     * Return list of all Aitocs' modules or certain module info
     * 
     * @param string $module Module key
     * @return array
     */
    public function getModulesList($module = '')
    {
        if(!$module) {
            return $this->_modulesList;
        }
        return isset($this->_modulesList[$module]) ? $this->_modulesList[$module] : null;
    }
    
    /**
     * Load all modules which have main config file
     * 
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _loadAllModules()
    {
        foreach ($this->_modulesList as $moduleKey => $moduleData) {
            if($moduleData['module_file']) { // only if the config file for this module in /app/etc/modules does exist
                $this->_makeModuleByModuleFile($moduleKey, $moduleData['module_file']);
            }
        }
        return $this;
    }
    
    /**
     * Load certain module by using its main config file 
     * 
     * @param string $moduleKey
     * @param string $moduleFile
     * 
     * @return Aitoc_Aitsys_Model_Module
     */
    protected function _makeModuleByModuleFile($moduleKey, $moduleFile)
    {
        $this->tool()->testMsg('Check: ' . $moduleKey . ' -- ' . $moduleFile);
        
        // check if module was already loaded during licensed modules load
        if ($module = (isset($this->_modules[$moduleKey]) ? $this->_modules[$moduleKey] : null)) {
            return $module;
        }
        
        $this->tool()->testMsg('Create: ' . $moduleKey);
        $module = new Aitoc_Aitsys_Model_Module();
        $module->loadByModuleFile($moduleFile, $moduleKey);

        return $this->_modules[$moduleKey] = $module;
    }

    /**
     * @return string
     */
    protected function _castPlatformId($file)
    {
        if ($file instanceof SplFileInfo) {
            $file = $file->getFilename();
        }
        $fileinfo = pathinfo($file);
        list($platformId) = explode('.', $fileinfo['basename'], 2);
        return $platformId;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _loadPlatformData()
    {
        $this->_copiedPlatformFiles = array();

        foreach ($this->getPlatforms() as $item) {
            /* @var $item DirectoryIterator */
            $platformId = $this->_castPlatformId($item);
            if (!file_exists($item->getPathname()) || $this->getPlatformId() || !$this->_checkPlatformId($platformId, $item->getPathname())) {
                $this->tool()->testMsg("Platform id broken or superfluous: " . $platformId);
                $this->_removePlatform($platformId);
                continue;
            }
            
            $dom = new DOMDocument('1.0');
            $dom->load($item->getPathname());
            $platform = $dom->getElementsByTagName('platform');
            if ($platform->length) {
                $platform = $platform->item(0);
                foreach ($platform->childNodes as $item) {
                    if ('location' == $item->nodeName) {
                        continue;
                    }
                    if (!$this->hasData($item->nodeName)) {
                        
                        $this->setData($item->nodeName, $item->nodeValue);
                    }
                }
            }
            $this->setPlatformId($platformId);
            $this->tool()->testMsg("Platform id: " . $platformId);
        }
        if ($platformId = $this->getPlatformId()) {
            $this->_copyToPlatform($platformId);
        }
        return $this;
    }
    
    /**
     * @return array
     */
    public function getPlatforms()
    {
        $result = array();
        try {
            $dir = new DirectoryIterator($this->getLicenseDir(true));
        }
        catch (Aitoc_Aitsys_Model_Aitfilesystem_Exception $exc) {
            throw $exc;
        }
        catch (Exception $e) {
            return $result;
        }

        foreach ($dir as $item) {            
            /* @var $item DirectoryIterator */
            if ($item->isFile() && $this->_isPlatformFileName($item->getFilename())) {
                $result[] = $item->getFileInfo();
            }
        }
        return $result;
    }
    
    /**
     * @return array
     */
    public function getPlatformPathes()
    {
        $pathes = array();
        foreach ($this->getPlatforms() as $item) {
            $platformId = $this->_castPlatformId($item);
            $pathes[] = dirname($item->getPathname()) . '/' . $platformId . '/';
        }
        return $pathes;
    }
    
    /**
     * @param string $platformId
     * @param string $path
     * @return bool
     */
    protected function _checkPlatformId($platformId, $path) 
    {
        $dom = new DOMDocument('1.0');
        $dom->load($path); 
        if ($location = $dom->getElementsByTagName('location')->item(0)) {
            /* @var $location DOMElement */
            if ($location->getAttribute('domain') == $this->tool()->getRealBaseUrl()
             && $location->getAttribute('path') == $this->getInstallDir(true))
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     * @throws Aitoc_Aitsys_Model_Aitfilesystem_Exception
     */
    protected function _savePlatformData()
    {
        if ($platformId = $this->getPlatformId()) {
            $defaultInstallDir = $this->getLicenseDir(true);
            $path = $defaultInstallDir.$platformId.self::PLATFORMFILE_SUFFIX;
            $this->tool()->testMsg("Save platform path: " . $path);
            
            $dom = $this->getPlatformDom();
            $dom->save($path);
            if (!file_exists($path)) {
                $msg = 'Write permissions required for: ' . $defaultInstallDir . ' and all files included.';
                throw new Aitoc_Aitsys_Model_Aitfilesystem_Exception($msg);
            }
        }
        return $this;
    }
    
    /**
     * Genarate platform DOM structure
     * @param $configData custom configuration data
     * @return DOMDocument
     */
    public function getPlatformDom($configData = array())
    {
        $data = array(
            'domain' => $this->tool()->getRealBaseUrl(),
            'path'   => $this->getInstallDir(true),
        ); 
        if ($configData) {
            $data = array_merge($data, $configData);
        }
        $dom = new DOMDocument('1.0');
        $platform = $dom->createElement('platform');
        $dom->appendChild($platform);
        $this->tool()->testMsg(array('try to save', $this->getData()));
        foreach ($this->getData() as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $platform->appendChild($dom->createElement($key, $value));
        }
        $location = $dom->createElement('location');
        /* @var $location DOMElement */
        $location->setAttribute('domain', $data['domain']);
        $location->setAttribute('path', $data['path']);
        $platform->appendChild($location);
        
        return $dom;
    }

    /**
     * @param string $platformId
     * @return Aitoc_Aitsys_Model_Platform
     * @throws Aitoc_Aitsys_Model_Aitfilesystem_Exception
     */
    protected function _copyToPlatform($platformId)
    {
        $path = $this->getLicenseDir(true);
        $platformPath = $path . $platformId . DS;
        if (!file_exists($platformPath)) {
            $this->tool()->filesystem()->makeDirStructure($platformPath);
        }
        $dir = new DirectoryIterator($path);
        foreach ($dir as $item) {
            /* @var $item DirectoryIterator */ 
            $filename = $item->getFilename();
            if ($item->isFile() && !$this->_isPlatformFileName($filename) 
             && $item->getFilename() != $this->tool()->getUrlFileName())
            {
                if (!$this->tool()->filesystem()->isWriteable($item->getPathname())) {
                    throw new Aitoc_Aitsys_Model_Aitfilesystem_Exception("File does not have write permissions: " . $item->getPathname());
                }
                
                $to = $platformPath . $filename;
                if (file_exists($to) && in_array($filename, $this->_copiedPlatformFiles)) {
                    $this->tool()->filesystem()->rmFile($item->getPathname());
                } else {
                    $this->tool()->filesystem()->moveFile($item->getPathname(), $to);
                }
            }
        }
        return $this;
    }
    
    /**
     * @param string $platformId
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _removePlatform($platformId)
    {
        $this->_modules = array();
        return $this->_copyFromPlatform($platformId);
    }
    
    /**
     * @param string $platformId
     * @return Aitoc_Aitsys_Model_Platform
     */
    protected function _copyFromPlatform($platformId)
    {
        $path = $this->getLicenseDir(true);
        $dir = new DirectoryIterator($path . $platformId);
        foreach ($dir as $item) {
            /* @var $item DirectoryIterator */
            $filename = $item->getFilename();
            if ($item->isFile() && '.php' !== substr($filename, -4)) {
                $this->tool()->filesystem()->cpFile($item->getPathname(), $path . $filename);
                $this->_copiedPlatformFiles[] = $filename;
            }
        }
        return $this;
    }
    
    /**
     * @param bool $value
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function setNeedCorrection($value = true)
    {
        $this->_needCorrection = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNeedCorrection()
    {
        return $this->_needCorrection;
    }
}
