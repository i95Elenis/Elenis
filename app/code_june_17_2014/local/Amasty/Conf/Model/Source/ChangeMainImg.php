<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Conf_Model_Source_ChangeMainImg extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amconf');
		return array(
			array('value' => 'hover', 'label' => $hlp->__('On Mouse Hover')),
			array('value' => 'click', 'label' => $hlp->__('On Click')),
            array('value' => 'disable', 'label' => $hlp->__('Disable')),
		);
	}
	
}