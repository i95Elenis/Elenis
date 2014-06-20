<?php
/**
 * Inchoo FirePhp Model
 *
 * @category    Inchoo
 * @package     Inchoo_Developer
 * @copyright   Copyright (c) 2009 Inchoo d.o.o. (http://inchoo.net)
 * @author		Ivan Weiler, Branko Ajzele
 * @license     http://opensource.org/licenses/gpl-license.php  GNU General Public License (GPL)
 */
class Inchoo_Developer_Model_Firephp
{
	public function __construct()
	{
		//compatible with older Zend in older Magento(1.3.2.x)
		$this->_initChannel();
	}
	
	public function send($var, $label=null, $style=null, $options=array())
	{
		if(!(Mage::getStoreConfig('dev/debug/firephp') && Mage::helper('core')->isDevAllowed())){
			return;
		}

		$this->getFirePhp()->send($var, $label, $style, $options);
		$this->getChannel()->flush();
		$this->getResponse()->sendHeaders();
	}
	
	public function getFirePhp()
	{
		return Zend_Wildfire_Plugin_FirePhp::getInstance();
	}
	
	protected function _initChannel()
	{
		Zend_Wildfire_Channel_HttpHeaders::getInstance()
				->setRequest($this->getRequest())
				->setResponse($this->getResponse());
	}
	
	public function getChannel()
	{
		return Zend_Wildfire_Channel_HttpHeaders::getInstance();
	}
	
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    public function getResponse()
    {
        return Mage::app()->getResponse();
    }
	
	
	


}