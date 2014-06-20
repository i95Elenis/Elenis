<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_LightboxEffects extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => 'fade', 'label' => $hlp->__('Effect of disappearance')),
			array('value' => 'elastic', 'label' => $hlp->__('Effect of motion')),
            array('value' => 'none', 'label' => $hlp->__('None')),
		);
	}
	
}