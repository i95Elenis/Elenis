<?php
/**
 * Inchoo FirePhp Helper
 *
 * @category    Inchoo
 * @package     Inchoo_Developer
 * @copyright   Copyright (c) 2009 Inchoo d.o.o. (http://inchoo.net)
 * @author		Ivan Weiler, Branko Ajzele
 * @license     http://opensource.org/licenses/gpl-license.php  GNU General Public License (GPL)
 */
class Inchoo_Developer_Helper_Firephp_Data extends Mage_Core_Helper_Abstract
{
	
	public function send($var, $label='', $style ='LOG')
	{
		$options = array('traceOffset' => 3);
		$this->getFirePhp()->send($var, $label, $style, $options);
	}
	
	public function debug($var, $label='', $style ='LOG')
	{
		if($var instanceof Varien_Object){
			$var = $var->debug();
		}
		
		$options = array('traceOffset' => 3);
		$this->getFirePhp()->send($var, $label, $style, $options);
	}
	
	public function getFirePhp()
	{
		return Mage::getSingleton('inchoo_developer/firephp');
	}
	
	
}