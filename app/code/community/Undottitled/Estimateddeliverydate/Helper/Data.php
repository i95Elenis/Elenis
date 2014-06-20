<?php
class Undottitled_Estimateddeliverydate_Helper_Data extends Mage_Core_Helper_Abstract 
{
	public function formatDate($date) {
		
		$dateFormat = "d/m/Y"; // Needs changing to configurable in admin
		
		if(empty($dateFormat)):
			$dateFormat = Mage::getModel('core/locale')->getDateFormat();
		endif;
		
		return date($dateFormat,strtotime($date));
	}
}