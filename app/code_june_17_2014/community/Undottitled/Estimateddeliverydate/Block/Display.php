<?php

class Undottitled_Estimateddeliverydate_Block_Display extends Mage_Core_Block_Template
{
	const INITIAL_MSG_TEXT = "estimateddeliverydate/options/initial_message";
	const FAILED_MSG_TEXT = "estimateddeliverydate/options/failed_request_message";
	const MSG_ENABLED = "estimateddeliverydate/options/message_enabled";

	public function getInitialMessage() {
		return Mage::getStoreConfig(self::INITIAL_MSG_TEXT);
	}
	
	public function isEnabled() {
		return Mage::getStoreConfig(self::MSG_ENABLED);
	}

}