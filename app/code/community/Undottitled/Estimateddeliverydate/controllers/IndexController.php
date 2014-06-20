<?php

class Undottitled_Estimateddeliverydate_IndexController extends Mage_Core_Controller_Front_Action
{

	protected function _getHelper()
    {
        return Mage::helper('estimateddeliverydate');
    }


	public function getdeliveriesAction() {
	
		$model = Mage::getModel('estimateddeliverydate/deliveries');

		$params = $this->getRequest()->getParams();
		if(!$params['qty'] > 0) $params['qty'] = 1;
		if(!empty($params['pid'])):
			$deliveryDate = $model->getNextDeliveryDate($params['pid'],$params['qty']);

			if($deliveryDate == false):
				echo $this->_getHelper()->__(Mage::getStoreConfig("estimateddeliverydate/options/failed_request_message"));			
			else:
				echo $this->_getHelper()->formatDate($deliveryDate);
			endif;
		else:
			echo $this->_getHelper()->__(Mage::getStoreConfig("estimateddeliverydate/options/failed_request_message"));
		endif;
	}
}