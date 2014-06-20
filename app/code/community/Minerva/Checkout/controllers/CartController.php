<?php
 include_once("Mage/Checkout/controllers/CartController.php");
class Minerva_Checkout_CartController extends Mage_Checkout_CartController 
{


public function _construct() {
	mage::log(__CLASS__ . __FUNCTION__ );
	parent::_construct();
	}
    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
	mage::log(__CLASS__ . __FUNCTION__ );
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $warning = Mage::getStoreConfig('sales/minimum_order/description');
                $cart->getCheckoutSession()->addNotice($warning);
            }
			if (!$this->_getQuote()->validateMinimumQty()) {
                $warning = Mage::getStoreConfig('sales/minimum_orderqty/description');
                $cart->getCheckoutSession()->addNotice($warning);
            }
        }

        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                $cart->getCheckoutSession()->addMessage($message);
            }
        }

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        Varien_Profiler::start(__METHOD__ . 'cart_display');
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
        $this->renderLayout();
        Varien_Profiler::stop(__METHOD__ . 'cart_display');
    }
}