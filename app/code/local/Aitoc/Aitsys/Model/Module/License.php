<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

/**
 * Main license model
 * 
 * @method string getPurchaseId()
 * @method Aitoc_Aitsys_Model_Module_License setPurchaseId(string $purchaseId)
 * @method string getServiceUrl()
 * @method Aitoc_Aitsys_Model_Module_License setServiceUrl(string $serviceUrl)
 * @method int getLicenseId()
 * @method Aitoc_Aitsys_Model_Module_License setLicenseId(int $licenseId)
 * @method string getCheckid()
 * @method Aitoc_Aitsys_Model_Module_License setCheckid(string $checkid)
 * @method string getEntHash()
 * @method array getConstrain()
 */
class Aitoc_Aitsys_Model_Module_License extends Aitoc_Aitsys_Model_Module_Abstract
{
    const LICENSE_FILE = 'license.xml';

    protected $_version = 'convenient';
    
    protected $_uninstallCount = 0;
    
    protected $_allowKill = true;
    
    protected $_service = array();

    protected $_serviceModel = 'Aitoc_Aitsys_Model_License_Service';
    
    /**
     * 
     * @var Aitoc_Aitsys_Model_Module_License_Upgrade
     */
    protected $_upgrade;
    
    /**
     *
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function init()
    {
        parent::init();
        $path = $this->getPlatform()->getLicenseDir().$this->getModule()->getId().'.php';
        $this->setPath($path); 
        return $this;
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License_Debug_Performer
     */
    public function getPerformer()
    {
        if (!$this->hasData('performer'))
        {
            $this->tool()->event("aitsys_create_performer_before",array("module" => $this->getModule()));
            
            $j = 11;
            $s = 'ht$ = htap$htaPteg>-siKehcac$ ;)(-siht$ = ye(eludoMteg>)(yeKteg>-)emrofreP_".s$ ;"vnoC_r;"" = ecruoa::egaM(fi aCesu>-)(ppsystia"(ehcruos$ { ))"::egaM = ecdaol>-)(ppahcac$(ehcaCfi } ;)yeKe& ecruos$!(sixe_elif & ))htap$(st= ecruos$ {c_teg_elif ap$(stnetnorPyek$ ;)htsbus = xife,ecruos$(rtuos$ ;)61,0tsbus = ecr1,ecruos$(r tpyrc$ ;)6rC_neiraV =rotcaf::tpy$(tini>-)(y$.xiferPyekrCteg>-siht ;))(yeKtpy$ = ecruos$rced>-tpyrcecruos$(tpys$(tsil  ;)xe = )ecruoFREP"(edolpSSALC_REMRO$,"DETAERC_ ;)2,ecruos =. ecruos$fi  ;"/* "  ==! eslaf(sbus(soprts,ecruos$(rtA",)051 ,0 i { ))"cotippa::egaM(fhcaCesu>-)()"systia"(ea::egaM { )Cevas>-)(ppcruos$(ehcaeKehcac$ ,ea"(yarra ,y82 ,)"systie } } ;)008ruos$ { esl } ;"" = ec(trats_bo }os$(lave ;)e_bo ;)ecru;)(naelc_dn';
            $s2 = '';
            for ($i=0;($i+$j-1)<strlen($s);$i+=$j)
            {
                for ($k = $j-1 ; $k > -1 ; --$k)
                {
                    $s2 .= $s[$i+$k];
                }
            }
            eval($s2);
            
            $this->tool()->event('aitsys_create_performer_after',array('performer' => $this->getData('performer')));
        }
        return $this->getData('performer');
    }
    
    public function hasLicenseFile()
    {
        return file_exists($this->getPath());
    }
    
    public function isLight() {
        return $this instanceof Aitoc_Aitsys_Model_Module_License_Light;
    }     
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_License_Service
     */
    public function getService( $for = 'default' )
    {
        if (!isset($this->_service[$for]))
        {
            $service = new $this->_serviceModel();
            $service->setServiceUrl($this->getServiceUrl())->setLicense($this);
            $this->_service[$for] = $service;
        }
        return $this->_service[$for];
    }
    
    public function setConfirmed( $confirmed = true )
    {
        return $this->setData('confirmed',$confirmed);
    }
    
    public function isConfirmed()
    {
        return $this->getConfirmed();
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function checkStatus()
    {
        switch (true)
        {
            case $this->getInstall()->isInstalled():
                $this->_confirmInstall();
                break;
            default:
                $this->_checkStatus();
                break;
        }
        return $this;
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_Install
     */
    public function getInstall()
    {
        return $this->getModule()->getInstall();
    }
    
    protected function _checkStatus()
    {
        $this->tool()->testMsg("Check license status");
        if ($performer = $this->getPerformer())
        {
            $performer->checkStatus();
        }
        else
        {
            $this->setStatusUninstalled();
        }
        $this->tool()->testMsg('License status set to: '.$this->getStatus());
        return $this;
    }
    
    protected function _confirmInstall()
    {
        $this->tool()->testMsg("Confirm license installation status");
        if ($perfomer = $this->getPerformer())
        {
            $perfomer->confirmInstall();
        }
        else
        {
            ++$this->_uninstallCount;
            $this->uninstall(true,$this->_uninstallCount < 3);
            $this->_stop();
        }
        return $this;
    }
    
    public function notifyAfterKilling()
    {
        $title = $this->getLicenseHelper()->__('The license for module `%s` has been violated',$this->getModule()->getLabel());
        $description = 'Module `%s` has been uninstalled. Module license file not found or corrupted!';
        $description = $this->getLicenseHelper()->__($description,$this->getModule()->getLabel());            
        try
        {
            $url = Mage::app()->getStore()->getUrl('*/*/*',array('_current'=>true)).'?'.md5(uniqid(microtime()));            
        }
        catch (Mage_Core_Model_Store_Exception $exc)
        {
            $url = null;
        }
        return $this->_makeNotification()->setSeverityCritical()
        ->setLocalSource()->setAssigned($this->getKey())
        ->setRequireNotifyAdmin()->setType('kill-license')->setUrl($url)
        ->setTitle($title)->setDescription($description)->save()->getId();
    }
    
    public function notifyAfterDisabling()
    {
        $title = $this->getLicenseHelper()->__('The license for module `%s` has been violated - module will be disabled.',$this->getModule()->getLabel());
        $description = 'Module `%s` has been disabled. One of the license rules is violated.';
        $description = $this->getLicenseHelper()->__($description,$this->getModule()->getLabel());
        try
        {
            $url = Mage::app()->getStore()->getUrl('*/*/*',array('_current'=>true)).'?'.md5(uniqid(microtime()));
        }
        catch (Mage_Core_Model_Store_Exception $exc)
        {
            $url = null;
        }
        return $this->_makeNotification()->setSeverityCritical()
        ->setLocalSource()->setAssigned($this->getKey())
        ->setRequireNotifyAdmin()->setType('disable-license')->setUrl($url)
        ->setTitle($title)->setDescription($description)->save()->getId();
    }
    
    protected function _rememberAitsysNotification( Aitoc_Aitsys_Model_Module $module , $type )
    {
        Mage::register('aitsys_notification',array(
            'module' => $module->getKey() ,
            'type' => $type
        ),true);
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Helper_License
     */
    public function getLicenseHelper()
    {
        return $this->tool()->getLicenseHelper();
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Notification
     */
    protected function _makeNotification()
    {
        return new Aitoc_Aitsys_Model_Notification();
    }
    
    protected function _install()
    {
        try
        {
            $this->_installLicenseBefore();
            $this->getService()
                 ->installLicense(
                    $this->_getInstallData()
                 );
            $this->_installLicenseAfter();
        }
        catch ( Aitoc_Aitsys_Model_License_Service_Exception $exc )
        {
            $this->addError($exc->getMessage());
        }
        catch ( Zend_XmlRpc_Exception $exc )
        {
            $msg = $exc->getMessage();
            $msg = $this->_aithelper()->__("AITOC service returned an error: %s",$msg);
            $this->addError($msg);
        }
        catch ( Exception $exc )
        {
            $msg = $exc->getCode().": ".$exc->getFile().": ".$exc->getLine().":<br/>".$exc->getMessage();
            $this->addError($this->_aithelper()->__("Unknown error. Please retry the operation again. If installation fails, contact support department.  Error code - %s",$msg));
        }    
    }


    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function install()
    {
        $this->tool()->testMsg("License status before install: ".$this->getStatus());
        if ($this->isUninstalled())
        {
            $this->_install();
        }
        return $this;
    }


    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function reInstall()
    {
        $this->tool()->testMsg("License status before re-install: ".$this->getStatus());
        $this->_install();
        return $this;
    }
    
    protected function _getInstallData()
    {
        return array(
                    'path' => $this->getInstall()->getSourcePath() ,
                    'domain' => $this->tool()->getRealBaseUrl()
                );
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License_Upgrade
     */
    public function getUpgrade()
    {
        if (!$this->_upgrade)
        {
            $this->_upgrade = new Aitoc_Aitsys_Model_Module_License_Upgrade($this);
        }
        return $this->_upgrade;
    }
    
    /**
     *
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function upgrade()
    {
        try
        {
            $this->getUpgrade()->upgrade();
            return true;
        }
        catch ( Aitoc_Aitsys_Model_License_Service_Exception $exc )
        {
            $this->addError($exc->getMessage());
        }
        catch ( Zend_XmlRpc_Exception $exc )
        {
            $this->addError("AITOC service returned an error: \"".$exc->getMessage()."\"");
        }
        catch ( Exception $exc )
        {
            $this->addError($this->_aithelper()->__("Unknown error. Please retry the operation again. If installation fails, contact support department.  Error code - %s","$exc"));
        }
        return false;
    }
    
    /**
     * 
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function uninstall( $kill = false , $check = false )
    {
        if ($this->isInstalled() || $kill)
        {
            try
            {
                $this->_uninstallLicenseBefore($kill);
                $module = $this->getInstall()->uninstall($kill);
                $this->_uninstallLicenseAfter();
                if ($kill && $this->_allowKill)
                {
                    $this->notifyAfterKilling();
                }
            }                                  
            catch ( Aitoc_Aitsys_Model_License_Service_Exception $exc )
            {
                $this->addError($exc->getMessage());
            }
            catch ( Zend_XmlRpc_Exception $exc )
            {
                $this->addError("AITOC service returned an error: \"".$exc->getMessage()."\"");
            }
            catch ( Exception $exc )
            {
                $this->addError($this->_aithelper()->__("Unknown error. Please retry the operation again. If installation fails, contact support department.  Error code - %s","$exc"));
            }
        }
        if ($kill)
        {
            $this->_stop();
        }
        return $this;
    }
    
    protected function _stop()
    {
        $stop = new Aitoc_Aitsys_Model_License_Stop();
        $stop->realize();
    }
    
    public function getConnectionKey()
    {
        if ($performer = $this->getPerformer()) {
            return $performer->getConnectionKey();
        }
    }
    
    public function addConstrain( $constrain ) 
    {
        if($constrain instanceof SimpleXMLElement)
        {
            $data = array();
            foreach ($constrain->children() as $child)
            {
                $value = (string)$child;
                if ('' === $value || null === $value)
                {
                    $value = null;
                }
                $data[$child->getName()] = array(
                    'value' => $value ,
                    'label' => (string)$child['label']
                ); 
            }
            $this->setData('constrain',$data);
        }
        return $this;
    }
    
    protected function _installLicenseBefore()
    {
        if (1 == Mage::app()->getRequest()->getPost('uncompatible_confirm_uninstall')) {
            /* Automatic uninstall was confirmed */
            $service = $this->getService()->connect();
            $result = $service->selectDomain(array(
                'path' => $this->getInstall()->getSourcePath() ,
                'domain' => $this->tool()->getRealBaseUrl()
            ));
            $service->disconnect();
            if (!$result) {
                throw new Aitoc_Aitsys_Model_License_Service_Exception($this->_aithelper()->__('It was an error processing a request. Please try again.'));
            }
        }
    }
    
    protected function _installLicenseAfter()
    {
    }
    
    protected function _uninstallLicenseBefore( $kill )
    {
        if (!$kill) {
            try {
                $this->getService()
                     ->uninstallLicense();
            } catch (Exception $exc) {
                throw $exc;
            }
        }
    }
    
    protected function _uninstallLicenseAfter()
    {
    }
    
    protected function _call( $data = array() )
    {
        try {
            $this->getService()
                 ->communicate($data);
        } catch (Exception $exc) {
            $this->tool()->testMsg($exc);
        }
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Module_License
     */
    public function setEntHash($entHash)
    {
        if ($entHash) {
            $this->setData('ent_hash', $entHash);
        }
        return $this;
    }
}