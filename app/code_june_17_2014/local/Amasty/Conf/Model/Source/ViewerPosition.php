<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_ViewerPosition extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => 'right', 'label' => $hlp->__('To the Right')),
			array('value' => 'left',  'label' => $hlp->__('To the Left')),
		);
	}
	
}
