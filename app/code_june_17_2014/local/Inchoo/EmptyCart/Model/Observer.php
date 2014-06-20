<?php

/**
 * @category   Inchoo
 * @package    Inchoo_EmptyCart
 * @author     Mladen Lotar - mladen.lotar@surgeworks.com
 */

class Inchoo_EmptyCart_Model_Observer
{
	const MODULE_NAME = 'Inchoo_EmptyCart';

	public function injectLinkEmptyCart($observer = NULL)
	{
		if (!$observer) {
			return;
		}

		if ('checkout.cart.methods.onepage.top' == $observer->getEvent()->getBlock()->getNameInLayout()) {

			if (!Mage::getStoreConfig('advanced/modules_disable_output/'.self::MODULE_NAME)) {
				$transport = $observer->getEvent()->getTransport();
				$block = new Inchoo_EmptyCart_Block_Injection();
				$block->setPassingTransport($transport['html']);
				$block->toHtml();
			}
		}

		return $this;
	}
}