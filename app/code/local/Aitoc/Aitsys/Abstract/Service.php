<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
final class Aitoc_Aitsys_Abstract_Service
{
    /**
     * @var Aitoc_Aitsys_Abstract_Service
     */
    static private $_instance;
    
    /**
     * @var array()
     */
    private $_licenseHelpers = array();
    
    /**
     * @var Aitoc_Aitsys_Debug_Service
     */
    protected $_debugger;
    
    /**
     * @var Aitoc_Aitsys_Model_Aitfilesystem
     */
    protected $_filesystem;
    
    /**
     * @var Aitoc_Aitsys_Model_Database
     */
    protected $_db;
    
    /**
     * @var Aitoc_Aitsys_Abstract_Model
     */
    protected $_currentObject;
    
    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $_interactiveSession;
    
    /**
     * @var string
     */
    protected $_realBaseUrl;
    
    /**
     * @var Aitoc_Aitsys_Abstract_Version
     */
    protected $_versionComparer;
    
    /**
     * @var array()
     */
    protected $_valueCache;
    
    /**
     * @var Aitoc_Aitsys_Model_Core_Cache
     */
    protected $_cache;
    
    /**
     * @var array()
     */
    protected $_helpers = array();
    
    /**
     * @var string
     */
    protected $_aitocUrl = '';

    /**
     * @return Aitoc_Aitsys_Abstract_Service
     */
    static public function get($object = null)
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance->setCurrentObject($object);
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Version
     */
    public function getVersionComparer()
    {
        if (!$this->_versionComparer) {
            $this->_versionComparer = new Aitoc_Aitsys_Abstract_Version();
        }
        return $this->_versionComparer;
    }
    
    /**
     * @param Mage_Adminhtml_Model_Session $session
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function setInteractiveSession($session)
    {
        $this->_interactiveSession = $session;
        return $this;
    }
    
    public function getRuler($moduleKey)
    {
        return $this->getPerformer($moduleKey)->getRuler();
    }
    
    public function getPerformer($moduleKey)
    {
        return $this->platform()->getModule($moduleKey)->getLicense()->getPerformer();
    }
    
    /**
     * @return Mage_Adminhtml_Model_Session
     */
    public function getInteractiveSession()
    {
        return $this->_interactiveSession;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Platform
     */
    public function platform()
    {
        return Aitoc_Aitsys_Model_Platform::getInstance();
    }
    
    /**
     * @return Aitoc_Aitsys_Debug_Service
     */
    public function debugger() 
    {
        if (!$this->_debugger)
        {
            if ($this->platform()->hasDebugingAllowed()) {
                $this->_debugger = new Aitoc_Aitsys_Debug_Service();
            } else {
                $this->_debugger = new Aitoc_Aitsys_Abstract_Pure();
            }
        }
        return $this->_debugger;
    }
    
    /**
     * @deprecated
     * @return Aitoc_Aitsys_Debug_Service
     */
    public function debuger()
    {
        return $this->debugger();
    }
    
    public function debug($msg, $trace = false)
    {
        $this->debugger()->debug($msg, $trace);
    }
    
    /**
     * @param $object
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function setCurrentObject($object)
    {
        if ($object instanceof Aitoc_Aitsys_Abstract_Model) {
            $this->_currentObject = $object;
        } else {
            $this->_currentObject = null;
        }
        return $this;
    }
    
    public function toPhpArray($object = null, $var = 'info')
    {
        $result = array();
        if (is_array($object)) {
            $data = $object;
        } else {
            $object = $object ? $object : $this->_currentObject;
            $data = $object->getData();
        }
        
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $result[] = "\t'" . $key . "' => '" . addcslashes($value, "'") . "'";
            }
        }
        
        $res = join(",\n", $result);
        if (!is_null($var)) {
            $res = '$' . $var . " = array(\n" . $res . "\n);\n";
        }
        return $res;
    }
    
    public function cleanDomain($url)
    {
        $url = str_replace(array('http://', 'https://', 'www.'), '', $url);
        $url = explode('/', $url);
        return array_shift($url);
    }
    
    public function testMsg($msg, $trace = false)
    {
        $this->debugger()->testMsg($msg, $trace);
    }
    
    /**
     * @param string $name Module name
     * @return bool
     */
    public function isModuleActive($name)
    {
        $val = Mage::getConfig()->getNode('modules/' . $name . '/active');
        return 'true' == (string)$val;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Aitfilesystem
     */
    public function filesystem()
    {
        if (!$this->_filesystem) {
            $this->_filesystem = new Aitoc_Aitsys_Model_Aitfilesystem();
        }
        return $this->_filesystem;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Database
     */
    public function db()
    {
        if (!$this->_db) {
            $this->_db = new Aitoc_Aitsys_Model_Database();
        }
        return $this->_db;
    }
    
    public function isAssocArray($array)
    {
        return is_array($array) && !is_numeric(join('', array_keys($array)));
    }
    
    /**
     * Flush cache storage, re-init magento config, apply updates
     * 
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function clearCache()
    {
        $this->getCache()->flush();
        return $this;
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Core_Cache
     */
    public function getCache()
    {
        if (is_null($this->_cache)) {
            $this->_cache = new Aitoc_Aitsys_Model_Core_Cache();
        }
        return $this->_cache;
    }
    
    /**
     * @param $name
     * @param $data
     * @return Aitoc_Aitsys_Abstract_Service
     */
    public function event($name, $data = array())
    {
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
        Mage::dispatchEvent($name, $data);
        return $this;
    }
    
    static private function _initSource($file, $key)
    {
        if (!Mage::registry('aitoc_test_marker')) {
            $module = self::get()->platform()->getModule($key)->initSource();
            return true;
        }
        Mage::unregister('aitoc_test_marker');
        return false;
    }
    
    static public function initSource($file, $key)
    {
        return self::_initSource($file, $key);
    }
    
    static public function initTemplate($file, $key)
    {
        return self::_initSource($file, $key);
    }
    
    protected function _getCachedCount($type)
    {
        if(is_null($this->_valueCache)) {
            $this->_valueCache = $this->getCache()->load('aitsys_service_counters', array());
        }
        return isset($this->_valueCache[$type]) ? $this->_valueCache[$type] : null;
    }
    
    protected function _updateCachedCount($type, $value)
    {
        $this->_valueCache[$type] = $value;
        $this->getCache()->save($this->_valueCache, 'aitsys_service_counters');
        return $this->_valueCache[$type];
    }
    
    public function getStoreCount()
    {
        if (null === $count = $this->_getCachedCount('store')) {
            $connection = $this->_getReadConnection();      
            $select = $connection->select();
            $select->from(array('CS'=>Mage::getModel('core/store')->getResource()->getMainTable()), 'group_id');
            if(Mage::helper("aitsys")->hasStagingModule()) {
                $select->joinLeft(array('ES' => Mage::getModel('enterprise_staging/staging')->getResource()->getMainTable()), 'CS.website_id=ES.staging_website_id', array('master_website_id', 'staging_website_id'))
                       ->where('ES.staging_website_id is NULL');
            }
            $select->where('website_id<>?', 0)
                   ->where('is_active=?', 1)                    
                   ->group('group_id');
            $count = count($connection->fetchAll($select));
            $this->_updateCachedCount('store', $count);
        }
        return $count;
    }
    
    public function getAdminCount()
    {
        if (null === $count = $this->_getCachedCount('admin')) {
            $connection = $this->_getReadConnection();      
            $select = $connection->select();
            
            $select->from(Mage::getModel('admin/user')->getResource()->getMainTable(), 'COUNT(*)')
                   ->where('is_active=?', 1);
        
            $count = $connection->fetchOne($select);
            $this->_updateCachedCount('admin', $count);
        }
        return $count;
    }
    
    public function getProductCount()
    {
        if (null === $count = $this->_getCachedCount('product')) {
            /*preparing low level resources*/        
            $db = Mage::getResourceModel('core/config');
            $connection = $db->getReadConnection();
            
            /*getting required entity type id*/
            $field  = 'entity_type_code';
            $value  = 'catalog_product';
            $table  = $db->getTable('eav/entity_type');
            $field  = $connection->quoteIdentifier(sprintf('%s.%s', $table, $field));
            $select = $connection->select()
                        ->from($table)
                        ->where($field . '=?', $value);
            $entity = $connection->fetchRow($select);
            
            
            /*getting attribute data*/
            $bind   = array(':entity_type_id' => $entity['entity_type_id']);
            $field  = 'attribute_code';
            $value  = 'status';
            $table  = $db->getTable('eav/attribute');
            $field  = $connection->quoteIdentifier(sprintf('%s.%s', $table, $field));
            $select = $connection->select()
                        ->from($table)
                        ->where($field . '=?', $value);
            
            $select = $select->where('entity_type_id = :entity_type_id');
            
            $attribute = $connection->fetchRow($select, $bind);
            
            /*getting number of enabled products*/
            $select = $connection->select()
                        ->from($db->getTable('catalog/product') . '_' . $attribute['backend_type'], 'COUNT(DISTINCT `entity_id`)')
                        ->where('entity_type_id=?', $attribute['entity_type_id'])
                        ->where('attribute_id=?', $attribute['attribute_id'])
                        ->where('value=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            
            $count = $connection->fetchOne($select);
            $this->_updateCachedCount('product', $count);
        }
        return $count;
    }
    
    public function getTotalCountByRule($ruleCode)
    {
        $count = 0;
        switch ($ruleCode) {
            case 'product':
                $count = $this->getProductCount();
                break;
            case 'store':
                $count = $this->getStoreCount();
                break;
            case 'admin':
                $count = $this->getAdminCount();
                break;
        }
        return $count;
    }
    
    public function getWaitTimeout()
    {
        if (null === $count = $this->_getCachedCount('wait_timeout')) {
            $connection = $this->_getReadConnection();
            $result = $connection->fetchAll('Show variables Where Variable_name=\'wait_timeout\'');
            
            $count = isset($result[0]['Value']) ? $result[0]['Value'] : 0;
            $this->_updateCachedCount('wait_timeout', $count);
        }
        return $count;
    }
    
    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getReadConnection()
    {
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('core_read');
    }
    
    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getReadConnection()
    {
        return $this->_getReadConnection();
    }
    
    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getWriteConnection()
    {
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('core_write');
    }
    
    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getWriteConnection()
    {
        return $this->_getWriteConnection();
    }
    
    /**
     * @return string
     */
    public function getUrlFileName()
    {
        return 'tmp.tml';
    }
    
    protected function _getUrlFromSource()
    {
        $conn = $this->_getReadConnection();
        $table = Mage::getModel('core/config_data')->getResource()->getMainTable();
        $select = $conn->select()
                    ->from($table, array('value'))
                    ->where('path = ?', 'web/unsecure/base_url')
                    ->where('scope_id = ?', 0)
                    ->where('scope = ?', 'default');
        return $conn->fetchOne($select);
    }
    
    public function getRealBaseUrl($clearDomain = true)
    {
        if (!$this->_realBaseUrl) { // && !($this->_realBaseUrl = $this->getCache()->load('aitsys_service_base_url', null, false))) { // removed from 2.20
            $this->_realBaseUrl = $this->_getUrlFromSource();
            // $this->getCache()->save($this->_realBaseUrl, 'aitsys_service_base_url', false, 0); // cache permanently // removed from 2.20
        }
        return $clearDomain ? $this->cleanDomain($this->_realBaseUrl) : $this->_realBaseUrl;
    }

    public function isMagentoVersion($sourceVersion, $mageVersion = null)
    {
        $mageVersion = $mageVersion ? $mageVersion : Mage::getVersion();
        return $this->getVersionComparer()->isMagentoVersion($sourceVersion, $mageVersion);
    }
    
    public function getApiUrl()
    {
        if ($this->platform()->hasData('_api_url')) {
            return $this->platform()->getData('_api_url');
        }
        $url = 'https://www.aitoc.com/api/xmlrpc/';
        if (false === strpos($url, 'AITOC_SERVICE_URL')) {
            return $url;
        }
    }
    
    public function getAitocUrl()
    {
        if (!$this->_aitocUrl) {
            $this->_aitocUrl = 'https://www.aitoc.com/en/'; // default value
            $url = 'https://www.aitoc.com/en/';
            if (false === strpos($url, 'AITOC_STORE_URL')) {
                $this->_aitocUrl = $url;
            }
        }
        return $this->_aitocUrl;
    }
    
    /**
     * Calls for a debugger's method if debugging is allowed.
     * Otherwise throws an Exception
     * 
     * @param string $method Method name
     * @param array $args Array of arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        if ($this->debugger()->isAllowedTestMethod($method)) {
            return call_user_func_array(array($this->debugger(), $method), $args);
        }
        throw new Exception("Call undefined method: " . $method);
    }
    
    /**
     * @return Aitoc_Aitsys_Helper_License
     */
    public function getLicenseHelper($module = null)
    {
    	if (!$module) {
        	$module = Mage::registry('aitoc_module');
    	}

    	if ($module && ($module instanceof Aitoc_Aitsys_Model_Module) && file_exists($module->getInstall()->getSourceDir() . 'Helper' . DS . 'License.php')) {
            $key = $module->getKey();
        } elseif ($module && is_string($module) && file_exists($this->filesystem()->getLocalDir().str_replace('_', DS, $module) . DS . 'Helper' . DS . 'License.php')) {
        	$key = $module;
        } else {
            $key = 'Aitoc_Aitsys';
        }

        if (!array_key_exists($key, $this->_licenseHelpers)) {
            $helperClass = $key . '_Helper_License';
            $this->_licenseHelpers[$key] = new $helperClass;
        }
        
        return $this->_licenseHelpers[$key];
    }
    
    /**
     * Check whether PHP works in cli mode
     * 
     * @return bool
     */
    public function isPhpCli()
    {
        return (bool)('cli' == @php_sapi_name());
    }
    
    /**
     * @return Aitoc_Aitsys_Abstract_Helper
     */
    public function getHelper($type = 'Data')
    {
        $class = 'Aitoc_Aitsys_Helper_' . $type;  
        if (!isset($this->_helpers[$class])) {
            $this->_helpers[$class] = new $class();
        }
        return $this->_helpers[$class];
    }
}