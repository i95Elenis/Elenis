<?php
class Magestore_Deliverytime_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {	
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function changetimeAction()
	{
		$value = (string)$this->getRequest()->getParam('value');
		$value = urldecode($value);
		Mage::getSingleton('deliverytime/session')->setDeliverytime($value);
	}
}