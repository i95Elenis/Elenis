<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_TypeOfZoom extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => 'inside', 'label' => $hlp->__('Inside')),
			array('value' => 'outside',  'label' => $hlp->__('Outside')),
		);
	}
	
}