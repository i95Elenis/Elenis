<?php

/**
 * API model access for SagePay
 *
 * @category   Ebizmarts
 * @package    Ebizmarts_SagePaySuite
 * @author     Ebizmarts <info@ebizmarts.com>
 */
class Ebizmarts_SagePaySuite_Model_Api_Payment extends Mage_Payment_Model_Method_Cc {

    protected $_code = '';
    protected $_canManageRecurringProfiles = false;
    protected $_quote = null;
    protected $_canEdit = TRUE;

    const BASKET_SEP = ':';
    const RESPONSE_DELIM_CHAR = "\r\n";
    const REQUEST_BASKET_ITEM_DELIMITER = ':';
    const RESPONSE_CODE_APPROVED = 'OK';
    const RESPONSE_CODE_REGISTERED = 'REGISTERED';
    const RESPONSE_CODE_DECLINED = 'OK';
    const RESPONSE_CODE_ABORTED = 'OK';
    const RESPONSE_CODE_AUTHENTICATED = 'OK';
    const RESPONSE_CODE_REJECTED = 'REJECTED';
    const RESPONSE_CODE_INVALID = 'INVALID';
    const RESPONSE_CODE_ERROR = 'ERROR';
    const RESPONSE_CODE_NOTAUTHED = 'NOTAUTHED';
    const RESPONSE_CODE_3DAUTH = '3DAUTH';
    const RESPONSE_CODE_MALFORMED = 'MALFORMED';

    const REQUEST_TYPE_PAYMENT = 'PAYMENT';
    const REQUEST_TYPE_VOID = 'VOID';

    const XML_CREATE_INVOICE = 'payment/sagepaydirectpro/create_invoice';

    const REQUEST_METHOD_CC = 'CC';
    const REQUEST_METHOD_ECHECK = 'ECHECK';

    const ACTION_AUTHORIZE_CAPTURE = 'payment';

    protected $ACSURL = NULL;
    protected $PAReq = NULL;
    protected $MD = NULL;
    private $_sharedConf = array('sync_mode', 'email_on_invoice', 'trncurrency', 'referrer_id', 'vendor', 'timeout_message', 'connection_timeout', 'send_basket', 'sagefifty_basket', 'basket_format');
    /**
     * Flag to set if request can be retried.
     *
     * @var boolean
     */
    private $_canRetry = true;    /**
     * BasketXML related error codes.
     *
     * @var type
     */
    private $_basketErrors = array(3021, 3195, 3177);

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->_canEdit;
    }

	protected function _getCoreUrl()
	{
		return Mage::getModel('core/url');
	}

    public function getTransactionDetails($orderId) {
        return Mage::getModel('sagepaysuite2/sagepaysuite_transaction')->loadByParent($orderId);
    }

    public function getNewTxCode() {
        return substr(time(), 0, 39);
    }

    public function getDate($format = 'Y-m-d H:i:s') {
        return Mage::getSingleton('core/date')->date($format);
    }

    public function getVpsProtocolVersion($mode = "live") {

        $protocol = '3.00';

        if("simulator" === strtolower($mode)) {
            $protocol = '2.23';
        }

        return $protocol;
    }

    public function getCustomerQuoteId() {
        $id = null;

        if (Mage::getSingleton('adminhtml/session_quote')->getQuoteId()) { #Admin
            $id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        } else if (Mage::getSingleton('customer/session')->getCustomerId()) { #Logged in frontend
            $id = Mage::getSingleton('customer/session')->getCustomerId();
        } else { #Guest/Register
            $vdata = Mage::getSingleton('core/session')->getVisitorData();
            return (string) $vdata['session_id'];
        }

        return (int) $id;
    }

    public function getCustomerLoggedEmail() {
        $s = Mage::getSingleton('customer/session');
        if ($s->getCustomerId()) {
            return $s->getCustomer()->getEmail();
        }
        return null;
    }

    public function setMcode($code) {
        $this->_code = $code;
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null) {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }

        if (!in_array($field, $this->_sharedConf)) {
            $_code = $this->getCode();
        } else {
            if (($field == 'vendor') && (strpos($this->getCode(), 'moto') !== FALSE)) {
                $_code = $this->getCode();
            } else {
                $_code = 'sagepaysuite';
            }
        }

        if (($_code != 'sagepaysuite') && (strpos($this->getCode(), 'moto') !== FALSE)) {
            $_code = $this->getCode();
            if (Mage::getSingleton('adminhtml/session_quote')->getStoreId()) {
                $storeId = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
            }
        }

        $path = 'payment/' . $_code . '/' . $field;

        $value = Mage::getStoreConfig($path, $storeId);

        if ($field == 'timeout_message') {
            $store = Mage::app()->getStore($storeId);
            $value = $this->_sageHelper()->__(str_replace(array('{store_name}', '{admin_email}'), array($store->getName(), Mage::getStoreConfig('trans_email/ident_general/email', $storeId)), Mage::getStoreConfig('payment/sagepaysuite/timeout_message', $storeId)));
        }

        $confValue = new stdClass;
        $confValue->value = $value;

        Mage::dispatchEvent('sagepaysuite_get_configvalue_' . $field, array('confobject' => $confValue, 'path' => $path));

        return $confValue->value;
    }

    public function getUrl($key, $tdcall = false, $code = null, $mode = null) {
        if ($tdcall) {
            $key = $key.='3d';
        }

        $_code = (is_null($code) ? $this->getCode() : $code);
        $_mode = (is_null($mode) ? $this->getConfigData('mode') : $mode);

        $urls = Mage::helper('sagepaysuite')->getSagePayUrlsAsArray();

        return $urls[$_code][$_mode][$key];
    }

    public function getTokenUrl($key, $integration) {
        $confKey = ($integration == 'direct') ? 'sagepaydirectpro' : 'sagepayserver';
        $urls = Mage::helper('sagepaysuite')->getSagePayUrlsAsArray();
        return $urls['sagepaytoken'][Mage::getStoreConfig('payment/' . $confKey . '/mode', Mage::app()->getStore()->getId())][$integration . $key];
    }

    public function getSidParam() {
        $coreSession = Mage::getSingleton('core/session');
        $sessionIdQueryString = $coreSession->getSessionIdQueryParam() . '=' . $coreSession->getSessionId();

        return $sessionIdQueryString;
    }

    public function getTokenModel() {
        return Mage::getModel('sagepaysuite/sagePayToken');
    }

    public static function log($data, $level = null, $file = null) {
        Sage_Log::log($data, $level, $file);
    }

    protected function _tokenPresent() {
        try {
            $present = (bool) ((int) $this->getInfoInstance()->getSagepayTokenCcId() !== 0);
        } catch (Exception $e) {

            if ((int) $this->getSageSuiteSession()->getLastSavedTokenccid() !== 0) {
                $present = true;
            } else {
                $present = false;
            }
        }

        return $present;
    }

    protected function _createToken() {
        try {
            $create = (bool) ((int) $this->getInfoInstance()->getRemembertoken() !== 0);
        } catch (Exception $e) {

            if((int)$this->getSageSuiteSession()->getRemembertoken(true) === 1) {
                $create = true;
            }
            else {
                $create = false;
            }

        }

        return $create;
    }

    public function assignData($data) {

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();

        if (!$data->getSagepayTokenCcId() && $this->getSageSuiteSession()->getLastSavedTokenccid()) {
            $data->setSagepayTokenCcId($this->getSageSuiteSession()->getLastSavedTokenccid());
        } else {
            if ($data->getSagepayTokenCcId()) {

                //This check is because OSC set_methods_separate posts data and its not complete sometimes
                //Attention: Server with OSC will still have this problem since cv2 is asked on iframe
                if (($data->getMethod() == 'sagepayserver' || $data->getMethod() == 'sagepayserver_moto')
                        || $data->getTokenCvv()) {
                    $this->getSageSuiteSession()->setLastSavedTokenccid($data->getSagepayTokenCcId());
                }
            }
        }

        $this->getSageSuiteSession()->setTokenCvv($data->getTokenCvv());

	//Direct GiftAidPayment flag
	$dgift = (!is_null($data->getCcGiftaid()) ? 1 : NULL);

        //Remember token
        $info->setRemembertoken((!is_null($data->getRemembertoken()) ? 1 : 0));

        $info->setCcType($data->getCcType())
                ->setCcOwner($data->getCcOwner())
                ->setCcLast4(substr($data->getCcNumber(), -4))
                ->setCcNumber($data->getCcNumber())
                ->setCcCid($data->getCcCid())
                ->setSagepayTokenCcId($data->getSagepayTokenCcId())
                ->setCcExpMonth($data->getCcExpMonth())
                ->setCcExpYear($data->getCcExpYear())
                ->setCcIssue($data->getCcIssue())
                ->setSaveTokenCc($data->getSavecc())
                ->setTokenCvv($data->getTokenCvv())
                ->setCcStartMonth($data->getCcStartMonth())
                ->setCcStartYear($data->getCcStartYear())
                ->setCcGiftaid($dgift);
        return $this;
    }

    protected function _getQuote() {

        $opQuote = Mage::getSingleton('checkout/type_onepage')->getQuote();
        $adminQuote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

        $rqQuoteId = Mage::app()->getRequest()->getParam('qid');
        if ($adminQuote->hasItems() === false && (int) $rqQuoteId) {
            $opQuote->setQuote(Mage::getModel('sales/quote')->loadActive($rqQuoteId));
        }

        return ($adminQuote->hasItems() === true) ? $adminQuote : $opQuote;
    }

    public function getQuote() {
        return $this->_getQuote();
    }

    public function getQuoteDb($sessionQuote) {

        $resource = $sessionQuote->getResource();
        $dbQuote = new Mage_Sales_Model_Quote;
        $resource->loadActive($dbQuote, $sessionQuote->getId());

        return $dbQuote;
    }

    /**
     * Check if current quote is multishipping
     */
    protected function _isMultishippingCheckout() {
        return (bool) Mage::getSingleton('checkout/session')->getQuote()->getIsMultiShipping();
    }

    public function cleanInput($strRawText, $strType) {
        if ($strType == "Number") {
            $strClean = "0123456789.";
            $bolHighOrder = false;
        } else
        if ($strType == "VendorTxCode") {
            $strClean = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
            $bolHighOrder = false;
        } else {
            $strClean = " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,'/{}@():?-_&�$=%~<>*+\"";
            $bolHighOrder = true;
        }

        $strCleanedText = "";
        $iCharPos = 0;

        do {
            // Only include valid characters
            $chrThisChar = substr($strRawText, $iCharPos, 1);

            if (strspn($chrThisChar, $strClean, 0, strlen($strClean)) > 0) {
                $strCleanedText = $strCleanedText . $chrThisChar;
            } else
            if ($bolHighOrder == true) {
                // Fix to allow accented characters and most high order bit chars which are harmless
                if (bin2hex($chrThisChar) >= 191) {
                    $strCleanedText = $strCleanedText . $chrThisChar;
                }
            }

            $iCharPos = $iCharPos + 1;
        } while ($iCharPos < strlen($strRawText));

        $cleanInput = ltrim($strCleanedText);
        return $cleanInput;
    }

    protected function _cleanString($text) {
        $pattern = '|[^a-zA-Z0-9\-\._]+|';
        $text = preg_replace($pattern, '', $text);

        return $text;
    }

    protected function _cphone($phone) {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $phone);
    }

    public function cleanString($text) {
        return $this->_cleanString($text);
    }

    protected function _getAdminSession() {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Check if admin is logged in
     * @return bool
     */
    protected function _getIsAdmin() {
        return (bool) (Mage::getSingleton('admin/session')->isLoggedIn());
    }

    /**
     * Check if current transaction is from the Backend
     * @return bool
     */
    protected function _getIsAdminOrder() {
        return (bool) (Mage::getSingleton('admin/session')->isLoggedIn() &&
                Mage::getSingleton('adminhtml/session_quote')->getQuoteId());
    }

    /**
     * Return commno data for *all* transactions.
     * @return array Data
     */
    public function _getGeneralTrnData(Varien_Object $payment) {
        $order = $payment->getOrder();
        $quoteObj = $this->_getQuote();

        $vendorTxCode = $this->_getTrnVendorTxCode();

        if ($payment->getCcNumber()) {
            $vendorTxCode .= $this->_cleanString(substr($payment->getCcOwner(), 0, 10));
        }
        $payment->setVendorTxCode($vendorTxCode);

        $request = new Varien_Object;
        $request->setVPSProtocol((string) $this->getVpsProtocolVersion($this->getConfigData('mode')))
                ->setReferrerID($this->getConfigData('referrer_id'))
                ->setVendor($this->getConfigData('vendor'))
                ->setVendorTxCode($vendorTxCode);

        $request->setClientIPAddress($this->getClientIp());

        if ($payment->getIntegra()) {

            $this->getSageSuiteSession()->setLastVendorTxCode($vendorTxCode);
            $request->setIntegration($payment->getIntegra());
            $request->setData('notification_URL', $this->getNotificationUrl() . '&vtxc=' . $vendorTxCode);
            $request->setData('success_URL', $this->getSuccessUrl());
            $request->setData('redirect_URL', $this->getRedirectUrl());
            $request->setData('failure_URL', $this->getFailureUrl());
        }

        if ($this->_getIsAdminOrder()) {
            $request->setAccountType('M');
        }

        if ($payment->getAmountOrdered()) {
            $this->_setRequestCurrencyAmount($request, $quoteObj);
        }

        if (!empty($order)) {

            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $request->setBillingAddress($billing->getStreet(1) . ' ' . $billing->getCity() . ' ' .
                                $billing->getRegion() . ' ' . $billing->getCountry()
                        )
                        ->setBillingSurname($this->ss($billing->getLastname(), 20))
                        ->setBillingFirstnames($this->ss($billing->getFirstname(), 20))
                        ->setBillingPostCode($this->ss($billing->getPostcode(), 10))
                        ->setBillingAddress1($this->ss($billing->getStreet(1), 100))
                        ->setBillingAddress2($this->ss($billing->getStreet(2), 100))
                        ->setBillingCity($this->ss($billing->getCity(), 40))
                        ->setBillingCountry($billing->getCountry())
                        ->setContactNumber(substr($this->_cphone($billing->getTelephone()), 0, 20));

                if ($billing->getCountry() == 'US') {
                    $request->setBillingState($billing->getRegionCode());
                }

                $request->setCustomerEMail($billing->getEmail());
            }

            if (!$request->getDescription()) {
                $request->setDescription('.');
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $request->setDeliveryAddress($shipping->getStreet(1) . ' ' . $shipping->getCity() . ' ' .
                                $shipping->getRegion() . ' ' . $shipping->getCountry()
                        )
                        ->setDeliverySurname($this->ss($shipping->getLastname(), 20))
                        ->setDeliveryFirstnames($this->ss($shipping->getFirstname(), 20))
                        ->setDeliveryPostCode($this->ss($shipping->getPostcode(), 10))
                        ->setDeliveryAddress1($this->ss($shipping->getStreet(1), 100))
                        ->setDeliveryAddress2($this->ss($shipping->getStreet(2), 100))
                        ->setDeliveryCity($this->ss($shipping->getCity(), 40))
                        ->setDeliveryCountry($shipping->getCountry())
                        ->setDeliveryPhone($this->ss(urlencode($this->_cphone($shipping->getTelephone())), 20));

                if ($shipping->getCountry() == 'US') {
                    $request->setDeliveryState($shipping->getRegionCode());
                }
            } else {
                #If the cart only has virtual products, I need to put an shipping address to Sage Pay.
                #Then the billing address will be the shipping address to
                $request->setDeliveryAddress($billing->getStreet(1) . ' ' . $billing->getCity() . ' ' .
                                $billing->getRegion() . ' ' . $billing->getCountry()
                        )
                        ->setDeliverySurname($this->ss($billing->getLastname(), 20))
                        ->setDeliveryFirstnames($this->ss($billing->getFirstname(), 20))
                        ->setDeliveryPostCode($this->ss($billing->getPostcode(), 10))
                        ->setDeliveryAddress1($this->ss($billing->getStreet(1), 100))
                        ->setDeliveryAddress2($this->ss($billing->getStreet(2), 100))
                        ->setDeliveryCity($this->ss($billing->getCity(), 40))
                        ->setDeliveryCountry($billing->getCountry())
                        ->setDeliveryPhone($this->ss(urlencode($this->_cphone($billing->getTelephone())), 20));

                if ($billing->getCountry() == 'US') {
                    $request->setDeliveryState($billing->getRegionCode());
                }
            }
        }
        if ($payment->getCcNumber()) {
            $request->setCardNumber($payment->getCcNumber())
                    ->setExpiryDate(sprintf('%02d%02d', $payment->getCcExpMonth(), substr($payment->getCcExpYear(), strlen($payment->getCcExpYear()) - 2)))
                    ->setCardType($payment->getCcType())
                    ->setCV2($payment->getCcCid())
                    ->setCardHolder($payment->getCcOwner());

            if ($payment->getCcIssue()) {
                $request->setIssueNumber($payment->getCcIssue());
            }
            if ($payment->getCcStartMonth() && $payment->getCcStartYear()) {
                $request->setStartDate(sprintf('%02d%02d', $payment->getCcStartMonth(), substr($payment->getCcStartYear(), strlen($payment->getCcStartYear()) - 2)));
            }
        }

        //$totals = $shipping->getTotals();
        //$shippingTotal = isset($totals['shipping']) ? $totals['shipping']->getValue() : 0;
        /*if ($this->getSendBasket()) {
            $request->setBasket($this->_getBasketContents($quoteObj));
        }*/

        if (!$request->getDeliveryPostCode()) {
            $request->setDeliveryPostCode('000');
        }
        if (!$request->getBillingPostCode()) {
            $request->setBillingPostCode('000');
        }

        return $request;
    }

    /**
     * Invoice existing order
     *
     * @param int $id Order id
     * @param string $captureMode Mode capture, OFFLINE-ONLINE-NOTCAPTURE
     */
    public function invoiceOrder($id = null, $captureMode = Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE, $silent = true) {

        if (is_object($id)) {
            $order = $id;
        }
        else {
            $order = Mage::getModel('sales/order')->load($id);
        }

        try {
            if (!$order->canInvoice()) {
                $emessage = $this->_getCoreHelper()->__('Cannot create an invoice.');
                if (!$silent) {
                    Mage::throwException($emessage);
                }
                Sage_Log::log($emessage);
                return false;
            }

            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                $emessage = $this->_getCoreHelper()->__('Cannot create an invoice without products.');
                if (!$silent) {
                    Mage::throwException($emessage);
                }
                Sage_Log::log($emessage);
                return false;
            }

            Mage::register('current_invoice', $invoice);

            $invoice->setRequestedCaptureCase($captureMode);

            # New in 1.4.2.0, if there is not such value, only REFUND OFFLINE shows up
            # TODO: @see Mage_Sales_Model_Order_Payment::registerCaptureNotification
            //$invoice->setTransactionId($order->getSagepayInfo()->getId());
            $invoice->setTransactionId(time());

            $invoice->register();

            //Send email
            $sendemail = (bool) $this->getConfigData('email_on_invoice');
            $invoice->setEmailSent($sendemail);
            $invoice->getOrder()->setCustomerNoteNotify($sendemail);

            $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());

            $transactionSave->save();

            if ($sendemail) {
                try {
                    $invoice->sendEmail(TRUE, '');
                } catch (Exception $em) {
                    Mage::logException($em);
                }
            }

            return true;
        } catch (Mage_Core_Exception $e) {
            if (!$silent) {
                Mage::throwException($e->getMessage());
            }
            Sage_Log::logException($e);
            return false;
        }
    }

    public function getClientIp() {
        return Mage::helper('core/http')->getRemoteAddr();
    }

    /**
     * Get product customize options
     *
     * @return array || false
     */
    protected function _getProductOptions($item) {
        $options = array();

        //This HELPER does not exist on all Magento versions
        $helperClass = Mage::getConfig()->getHelperClassName('catalog/product_configuration');
        if (FALSE === class_exists($helperClass, FALSE)) {
            return $options;
        }

        $helper = Mage::helper('catalog/product_configuration');
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $options = $helper->getCustomOptions($item);
        } elseif ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $options = $helper->getConfigurableOptions($item);
        }

        return $options;
    }

    protected function _getCoreHelper() {
        return Mage::helper('core');
    }

    protected function _sageHelper() {
        return Mage::helper('sagepaysuite');
    }

    /**
     * Return Multishipping Checkout ACTIVE Step.
     */
    public function getMsActiveStep() {
        return Mage::getSingleton('checkout/type_multishipping_state')->getActiveStep();
    }

    public function isMsOnOverview() {
        return ($this->_getQuote()->getIsMultiShipping() && $this->getMsActiveStep() == 'multishipping_overview');
    }

    protected function _getReservedOid() {

        if ($this->isMsOnOverview() && ($this->_getQuote()->getPayment()->getMethod() == 'sagepayserver')) {
            return null;
        }

        $orderId = $this->getSageSuiteSession()->getReservedOrderId();

        if (!$orderId) {

            // we need to check here if the orderId has already been used by other payment method
            if (!$this->_getQuote()->getReservedOrderId() || $this->_orderIdAlreadyUsed($this->_getQuote()->getReservedOrderId())) {
                $this->_getQuote()->reserveOrderId()->save();
                // Commenting ->save() if save is performed and Amasty_Promo is installed an exception is thrown.
                //$this->_getQuote()->reserveOrderId();
            }

            $orderId = $this->_getQuote()->getReservedOrderId();
            $this->getSageSuiteSession()->setReservedOrderId($orderId);
        }

        if ($this->isMsOnOverview()) {
            $this->getSageSuiteSession()->setReservedOrderId(null);
        }
        return $orderId;
    }

    protected function _orderIdAlreadyUsed($orderId) {
        // just in case there is no orderId provided
        if (!$orderId)
            return false;

        // let's check now if it has been used for another order
        $potentialExistingOrder = Mage::getModel("sales/order")->loadByIncrementId($orderId);

        // if there is an order we should have it loaded by now
        if (!$potentialExistingOrder->getId()) {
            return false;
        }

        return true;
    }

    protected function _getTrnVendorTxCode() {
        //@ToDo: If Amasty_Promo is present, _getReserverOid() creates this error
        /*
        An error occurred with Sage Pay:
        SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row:
         * a foreign key constraint fails (`shop_rush_test`.`sales_flat_quote_item_option`, CONSTRAINT `FK_5F20E478CA64B6891EA8A9D6C2735739`
         * FOREIGN KEY (`item_id`) REFERENCES `sales_flat_quote_item` (`item_id`) ON DELETE CASCADE ON UP)
         */
        $rsOid = $this->_getReservedOid();
        return ($rsOid) ? substr($rsOid . '-' . date('Y-m-d-H-i-s'), 0, 40) : substr(date('Y-m-d-H-i-s-') . time(), 0, 40);
    }

    protected function _getRqParams() {
        return Mage::app()->getRequest()->getParams();
    }

    protected function _getBuildPaymentObject($quoteObj, $params = array('payment' => array())) {
        $payment = new Varien_Object;
        if (isset($params['payment']) && !empty($params['payment'])) {
            $payment->addData($params['payment']);
        }

        if (Mage::helper('sagepaysuite')->creatingAdminOrder()) {
            $payment->addData($quoteObj->getPayment()->toArray());
        }

        $payment->setTransactionType(strtoupper($this->getConfigData('payment_action')));
        $payment->setAmountOrdered($this->formatAmount($quoteObj->getGrandTotal(), $quoteObj->getQuoteCurrencyCode()));
        $payment->setRealCapture(true); //To difference invoice from capture
        $payment->setOrder( (clone $quoteObj) );
        $payment->setAnetTransType(strtoupper($this->getConfigData('payment_action')));
        $payment->getOrder()->setOrderCurrencyCode($quoteObj->getQuoteCurrencyCode());
        $payment->getOrder()->setBillingAddress($quoteObj->getBillingAddress());

        if($quoteObj->isVirtual()) {
            $payment->getOrder()->setShippingAddress($quoteObj->getBillingAddress());
        }
        else {
            $payment->getOrder()->setShippingAddress($quoteObj->getShippingAddress());
        }

        return $payment;
    }

    public function getConfigCurrencyCode($quoteObj) {
        $code = null;

        $currencyCode = (string) $this->getConfigData('trncurrency', $quoteObj->getStoreId());

        if ($currencyCode == 'store') {
            $code = $quoteObj->getQuoteCurrencyCode();
        } else if ($currencyCode == 'switcher') {
            $code = Mage::app()->getStore()->getCurrentCurrencyCode();
        }
        else {
            $code = $quoteObj->getBaseCurrencyCode();
        }

        return $code;
    }

    public function saveAction($orderId, $request, $result) {
        $model = Mage::getModel('sagepaysuite2/sagepaysuite_action')->setParentId($orderId);

        $model->setStatus($result['Status'])
                ->setStatusDetail($result['StatusDetail'])
                ->setActionCode(strtolower($request['TxType']))
                ->setActionDate($this->getDate());

        return $model->save();
    }

    /**
     * Returns real payment method code.
     * @param string $dbName Name on db, direct/server
     * @return string Real module code
     */
    protected function _getIntegrationCode($dbName) {
        switch ($dbName) {
            case 'direct':
                return 'sagepaydirectpro';
                break;
            case 'server':
                return 'sagepayserver';
                break;
            case 'form':
                return 'sagepayform';
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Cancel payment
     *  - DEFERRED  -> ABORT
     *  - PAYMENT or RELEASE -> VOID
     *  - REGISTERED or AUTHENTICATE -> CANCEL
     * @param   Varien_Object $invoicePayment
     * @return  Ebizmarts_SagePaySuite_Model_Api_Payment
     */
    public function cancelOrder(Varien_Object $payment) {
        $order = $payment->getOrder();
        $trn = $this->getTransactionDetails($order->getId());

        if (!$trn->getId()) {

            $msg = $this->_getCoreHelper()->__('Sagepay local transaction does not exist, order id -> %s', $order->getId());
            $this->_getAdminSession()->addError($msg);

            self::log($msg);
            Mage::logException(new Exception($msg));
            return $this;
        }

		$t = strtoupper($trn->getTxType());
        if($t == 'PAYMENT'){
            $this->voidPayment($trn);
        }

        return $this;
    }

    public function abortPayment($trn) {

        /**
         * SecurityKey from the "Admin & Access API"
         */
        if (!$trn->getSecurityKey() && strtoupper($trn->getIntegration()) == 'FORM') {
            $this->_addSecurityKey($trn);
        }

        $data = array();
        $data['VPSProtocol'] = $trn->getVpsProtocol();
        $data['TxType'] = self::REQUEST_TYPE_ABORT;
        $data['ReferrerID'] = $this->getConfigData('referrer_id');
        $data['Vendor'] = $trn->getVendorname();
        $data['VendorTxCode'] = $trn->getVendorTxCode();
        $data['VPSTxId'] = $trn->getVpsTxId();
        $data['SecurityKey'] = $trn->getSecurityKey();
        $data['TxAuthNo'] = $trn->getTxAuthNo();

        try {
            $result = $this->requestPost($this->getUrl('abort', false, $this->_getIntegrationCode($trn->getIntegration()), $trn->getMode()), $data);
        } catch (Exception $e) {
            Sage_Log::logException($e);
            Mage::throwException($this->_getHelper()->__('Transaction could not be aborted at SagePay. You may want to delete it from the local database and check the transaction at the SagePay admin panel.'));
        }

        if ($result['Status'] != 'OK') {
            Sage_Log::log($result['StatusDetail']);
            Mage::throwException(Mage::helper('sagepaysuite')->__($result['StatusDetail']));
        }

        $this->saveAction($trn->getOrderId(), $data, $result);

        $trn->setAborted(1)->save();
    }

    public function voidPayment($trn) {

        /**
         * SecurityKey from the "Admin & Access API"
         */
        if (!$trn->getSecurityKey() && strtoupper($trn->getIntegration()) == 'FORM') {
            $this->_addSecurityKey($trn);
        }

        $data = array();
        $data['VPSProtocol'] = $trn->getVpsProtocol();
        $data['TxType'] = self::REQUEST_TYPE_VOID;
        $data['ReferrerID'] = $this->getConfigData('referrer_id');
        $data['Vendor'] = $trn->getVendorname();
        $data['VendorTxCode'] = $trn->getVendorTxCode();
        $data['VPSTxId'] = $trn->getVpsTxId();
        $data['SecurityKey'] = $trn->getSecurityKey();
        $data['TxAuthNo'] = $trn->getTxAuthNo();

        try {
            $result = $this->requestPost($this->getUrl('void', false, $this->_getIntegrationCode($trn->getIntegration()), $trn->getMode()), $data);
        } catch (Exception $e) {
            Mage::throwException($this->_getHelper()->__('Transaction could not be voided at SagePay. You may want to delete it from the local database and check the transaction at the SagePay admin panel.'));
        }

        if ($result['Status'] != 'OK') {
            Sage_Log::log($result['StatusDetail']);
            Mage::throwException(Mage::helper('sagepaysuite')->__($result['StatusDetail']));
        }

        $this->saveAction($trn->getOrderId(), $data, $result);

        $trn->setVoided(1)->save();
    }

    private function _cancel($trn) {

        /**
         * SecurityKey from the "Admin & Access API"
         */
        if (!$trn->getSecurityKey() && strtoupper($trn->getIntegration()) == 'FORM') {
            $this->_addSecurityKey($trn);
        }

        $data = array();
        $data['VPSProtocol'] = $trn->getVpsProtocol();
        $data['TxType'] = self::REQUEST_TYPE_CANCEL;
        $data['ReferrerID'] = $this->getConfigData('referrer_id');
        $data['Vendor'] = $trn->getVendorname();
        $data['VendorTxCode'] = $trn->getVendorTxCode();
        $data['VPSTxId'] = $trn->getVpsTxId();
        $data['SecurityKey'] = $trn->getSecurityKey();

        $result = $this->requestPost($this->getUrl('cancel', false, $this->_getIntegrationCode($trn->getIntegration()), $trn->getMode()), $data);

        if ($result['Status'] != 'OK') {
            Sage_Log::log($result['StatusDetail']);
            Mage::throwException(Mage::helper('sagepaysuite')->__($result['StatusDetail']));
        }

        $this->saveAction($trn->getOrderId(), $data, $result);

        $trn->setCanceled(1)->save();
    }

    protected function _getAdminQuote() {
        return Mage::getSingleton('adminhtml/session_quote')->getQuote();
    }

    public function loadQuote($quoteId, $storeId) {
        return Mage::getModel('sales/quote')->setStoreId($storeId)->load($quoteId);
    }

    /**
     * Recover a transaction, creates an order in Magento and adds payment data
     * from an approved transaction that has no order.
     *
     * @param type $vendorTxCode
     * @return type
     */
    public function recoverTransaction($vendorTxCode) {

        $trn = Mage::getModel('sagepaysuite2/sagepaysuite_transaction')
                ->loadByVendorTxCode($vendorTxCode);

        if (is_null($trn->getId())) {
            Mage::throwException($this->_sageHelper()->__('Transaction "%s" not found.', $vendorTxCode));
        }

        if (!is_null($trn->getOrderId())) {
            Mage::throwException($this->_sageHelper()->__('Transaction "%s" is already associated to an order, no need to recover it.', $vendorTxCode));
        }

        $quote = $this->loadQuote($trn->getQuoteId(), $trn->getStoreId());
        if (!$quote->getId()) {
            Mage::throwException($this->_sageHelper()->__('Quote could not be loaded for "%s".', $vendorTxCode));
        }

        Mage::register('Ebizmarts_SagePaySuite_Model_Api_Payment::recoverTransaction', $vendorTxCode);

        $o = Mage::getModel('sagepaysuite/createOrder', $quote)->create();

        return $o;

    }

    public function showPost() {
        $this->_code = 'direct';
        $showPostUrl = 'https://test.sagepay.com/showpost/showpost.asp';

        $data = array();
        $data ['SuiteModuleVersion'] = (string) Mage::getConfig()->getNode('modules/Ebizmarts_SagePaySuite/version');
        $data ['Vendor'] = uniqid();

        $this->requestPost($showPostUrl, $data, true);

        return $data['Vendor'];
    }

    /**
     * Send a post request with cURL
     * @param string $url URL to POST to
     * @param array $data Data to POST
     * @return array|string $result Result of POST
     */
    public function requestPost($url, $data, $returnRaw = false) {

        //$storeId = $this->getStoreId();
        $aux = $data;

        if (isset($aux['CardNumber'])) {
            $aux['CardNumber'] = substr_replace($aux['CardNumber'], "XXXXXXXXXXXXX", 0, strlen($aux['CardNumber']) - 3);
        }
        if (isset($aux['CV2'])) {
            $aux['CV2'] = "XXX";
        }

        $rd = '';
        foreach ($data as $_key => $_val) {
            if ($_key == 'billing_address1')
                $_key = 'BillingAddress1';
            $rd .= $_key . '=' . urlencode(mb_convert_encoding($_val, 'ISO-8859-1', 'UTF-8')) . '&';
        }

        $userAgent = $this->_sageHelper()->getUserAgent();

        self::log($url, null, 'SagePaySuite_REQUEST.log');
        self::log("User-Agent: " . Mage::helper('core/http')->getHttpUserAgent(false), null, 'SagePaySuite_REQUEST.log');
        self::log($userAgent, null, 'SagePaySuite_REQUEST.log');
        self::log($aux, null, 'SagePaySuite_REQUEST.log');

        $_timeout = (int) $this->getConfigData('connection_timeout');
        $timeout = ($_timeout > 0 ? $_timeout : 90);

        $output = array();

        $curlSession = curl_init();

        curl_setopt($curlSession, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_POST, 1);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $rd);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);

        //curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);
        //Support for value 1 removed in cURL 7.28.1
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);

        $rawresponse = curl_exec($curlSession);

        if (true === $returnRaw) {
            return $rawresponse;
        }

        self::log($rawresponse, null, 'SagePaySuite_RawResponse.log');

        //Split response into name=value pairs
        $response = explode(chr(10), $rawresponse);

        // Check that a connection was made
        if (curl_error($curlSession)) {

            self::log(curl_error($curlSession), Zend_Log::ALERT, 'SagePaySuite_REQUEST.log');
            self::log(curl_error($curlSession), Zend_Log::ALERT, 'Connection_Errors.log');

            $output['Status'] = 'FAIL';
            $output['StatusDetail'] = htmlentities(curl_error($curlSession)) . '. ' . $this->getConfigData('timeout_message');

            return $output;
        }

        curl_close($curlSession);

        // Tokenise the response
        for ($i = 0; $i < count($response); $i++) {
            // Find position of first "=" character
            $splitAt = strpos($response[$i], "=");
            // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
            $arVal = (string) trim(substr($response[$i], ($splitAt + 1)));
            if (!empty($arVal)) {
                $output[trim(substr($response[$i], 0, $splitAt))] = $arVal;
            }
        }

        //Resend same request if fails because of basket related errors.
        if( $this->_canRetry && isset($output['StatusDetail']) && (isset($output['Status']) && ($output['Status'] == 'INVALID')) ) {

            for ($i = 0; $i < count($this->_basketErrors); $i++) {
                if(1 === preg_match('/^' . $this->_basketErrors[$i] . '/i', $output['StatusDetail'])) {

                    if(isset($data['BasketXML'])) {
                        unset($data['BasketXML']);

                        self::log($output, null, 'SagePaySuite_REQUEST.log');
                        self::log("Basket ERROR, retrying without BasketXML in POST ...", null, 'SagePaySuite_REQUEST.log');

                        return $this->requestPost($url, $data, false);
                    }

                }
            }

        }

        self::log($output, null, 'SagePaySuite_REQUEST.log');

        return $output;
    }

    public function getSendBasket() {
        return ((int) $this->getConfigData('send_basket') === 1 ? true : false);
    }

    protected function _getRequest() {
        return Mage::getModel('sagepaysuite/sagepaysuite_request');
    }

    public function getSageSuiteSession() {
        return Mage::getSingleton('sagepaysuite/session');
    }

    protected function _isInViewOrder() {
        $r = Mage::getModel('core/url')->getRequest();
        return (bool) ($r->getActionName() == 'view' && $r->getControllerName() == 'sales_order');
    }

    public function getTitle() {
        $mode = $this->getConfigData('mode');
        if ($mode == 'live' || $this->_isInViewOrder() === true || $this->getCode() == 'sagepaypaypal') {
            return parent::getTitle();
        }

        return parent::getTitle() . ' - ' . Mage::helper('sagepaysuite')->__('%s mode', strtoupper($mode));
    }

    public function isServer() {
        return (bool) ($this->getCode() == 'sagepayserver');
    }

    public function isDirect() {
        return (bool) ($this->getCode() == 'sagepaydirectpro');
    }

    /**
     * Trim $string to certaing $length
     */
    public function ss($string, $length) {
        return substr($string, 0, $length);
    }

    protected function _addSecurityKey($trn) {
        $trnDetails = Mage::getModel('sagepayreporting/sagepayreporting')->getTransactionDetails($trn->getVendorTxCode(), null);
        if ($trnDetails->getErrorcode() != '0000') {
            Mage::throwException($trnDetails->getError());
        }
        $formSecKey = (string) $trnDetails->getSecuritykey();
        $trn->setSecurityKey($formSecKey)
                ->save();
    }

    /**
     * Format amount based on currency
     *
     * @param float $amount
     * @param string $currency
     * @return float|int
     */
    public function formatAmount($amount, $currency) {
        $_amount = 0.00;

        //JPY, which only accepts whole number amounts
        if ($currency == 'JPY') {
            $_amount = round($amount, 0, PHP_ROUND_HALF_EVEN);
        }
        else {
            $_amount = number_format(Mage::app()->getStore()->roundPrice($amount), 2, '.', '');
        }

        return $_amount;
    }

    public function getSageBasket($quote) {

        $itemsCollection = $quote->getItemsCollection();

        $orderCurrencyCode = $this->getConfigCurrencyCode($quote);

        $basket = '';

        $_currency = Mage::getModel('directory/currency')->load($orderCurrencyCode);

        if ($itemsCollection->getSize() > 0) {

            $numberOfdetailLines = $itemsCollection->getSize() + 1;
            $todelete = 0;

            foreach ($itemsCollection as $item) {
                if ($item->getParentItem()) { # Configurable products
                    $numberOfdetailLines--;
                }
            }

            $basket .= $numberOfdetailLines . self::BASKET_SEP;

            foreach ($itemsCollection as $item) {

                //Avoid duplicates SKUs on basket
                if (strpos($basket, ($this->_cleanString($item->getSku()) . '|')) !== FALSE) {
                    continue;
                }
                if ($item->getParentItem()) {
                    continue;
                }

                $tax = ($item->getBaseTaxBeforeDiscount() ? $item->getBaseTaxBeforeDiscount() : ($item->getBaseTaxAmount() ? $item->getBaseTaxAmount() : 0));

                //Options
                $options = $this->_getProductOptions($item);
                $_options = '';
                if (count($options) > 0) {
                    foreach ($options as $opt) {
                        $_options .= $opt['label'] . '-' . $opt['value'] . '.';
                    }
                    $_options = '_' . substr($_options, 0, -1) . '_';
                }

                //[SKU]|Name
                $line = str_replace(':', '-', '[' . $this->_cleanString($item->getSku()) . ']|' . $this->_cleanString($item->getName()))
                        . $this->_cleanString($_options) . self::BASKET_SEP;

                //Quantity
                $line .= ( $item->getQty() * 1) . self::BASKET_SEP;


                //if ($this->getConfigData('sagefifty_basket')) {
                $taxAmount = ($item->getTaxAmount() / ($item->getQty() * 1));

                //Item value
                $line .= $item->getCalculationPrice() . self::BASKET_SEP;

                //Item tax
                $line .= number_format($taxAmount, 2) . self::BASKET_SEP;

                //Item total
                $line .= number_format($item->getCalculationPrice() + $taxAmount, 2) . self::BASKET_SEP;

                //Line total
                $line .= (($item->getRowTotal() + $tax) - $item->getDiscountAmount()) . self::BASKET_SEP;
                /*} else {
                    //Item value
                    $line .= $_currency->formatPrecision($item->getCalculationPrice(), 2, array(), false) . self::BASKET_SEP;

                    //Item tax
                    $line .= $_currency->formatPrecision($item->getTaxAmount(), 2, array(), false) . self::BASKET_SEP;

                    //Item total
                    $line .= $_currency->formatPrecision($item->getTaxAmount() + $item->getCalculationPrice(), 2, array(), false) . self::BASKET_SEP;

                    //Line total
                    $line .= $_currency->formatPrecision((($item->getRowTotal() + $tax) - $item->getDiscountAmount()), 2, array(), false) . self::BASKET_SEP;
                }*/

                if (strlen($basket . $line) < 7498) {
                    $basket .= $line;
                }
                else {
                    $todelete++;
                }
            }
        }

        //Delivery data
        //if ($this->getConfigData('sagefifty_basket')) {
        $deliveryValue = $quote->getShippingAddress()->getShippingAmount();
        $deliveryTax = $quote->getShippingAddress()->getShippingTaxAmount();
        $deliveryAmount = $quote->getShippingAddress()->getShippingInclTax();
        /*} else {
            $deliveryValue = $_currency->formatPrecision($quote->getShippingAddress()->getShippingAmount(), 2, array(), false);
            $deliveryTax = $_currency->formatPrecision($quote->getShippingAddress()->getShippingTaxAmount(), 2, array(), false);
            $deliveryAmount = $_currency->formatPrecision($quote->getShippingAddress()->getShippingInclTax(), 2, array(), false);
        }*/

        $deliveryName = $quote->getShippingAddress()->getShippingDescription() ? $quote->getShippingAddress()->getShippingDescription() : 'Delivery';
        $delivery = $this->_cleanString($deliveryName) . self::BASKET_SEP . '1' . self::BASKET_SEP . $deliveryValue . self::BASKET_SEP . $deliveryTax
                . self::BASKET_SEP . $deliveryAmount . self::BASKET_SEP . $deliveryAmount;

        if (strlen($basket . $delivery) < 7498) {
            $basket .= $delivery;
        }
        else {
            $todelete++;
        }

        $numberOfLines = substr($basket, 0, strpos($basket, ':'));

        if ($todelete > 0) {
            $num = $numberOfLines - $todelete;
            $basket = str_replace($numberOfLines, $num, $basket);
        }

        /**
         * Verify that items count is correct
         */
        $items = explode(':', $basket);
        //Remove line number from basket
        array_shift($items);
        //Split into rows
        $rows = count(array_chunk($items, 6));
        if ($rows != $numberOfLines) {
            $basket = str_replace($numberOfLines, $rows, $basket);
        }
        /**
         * Verify that items count is correct
         */

        return $basket;
    }

    /**
     * The basket can be passed as an xml document with extra information that
     * can be used for more accurate fraud screening through ReD and the supplying of TRIPs information.
     *
     * @return string Basket in XML format
     */
    public function getBasketXml($quote) {

        $basket = new Ebizmarts_Simplexml_Element('<basket />');
        if($this->_getIsAdmin()) {

            $uname = trim(Mage::getSingleton('admin/session')->getUser()->getUsername());

            $validAgent = preg_match_all("/[a-zA-Z0-9\s]+/", $uname, $matchesUname);
            if($validAgent !== 1) {
                $uname = implode("", $matchesUname[0]);
            }

            //<agentId>
            $basket->addChildCData('agentId', substr($uname, 0, 16));
        }

        $shippingAdd = $quote->getShippingAddress();
        $billingAdd  = $quote->getBillingAddress();

        $itemsCollection   = $quote->getItemsCollection();

        foreach ($itemsCollection as $item) {

            if ($item->getParentItem()) {
                continue;
            }

            $node = $basket->addChild('item', '');

            $itemDesc = trim( substr($item->getName(), 0, 100) );
            $validDescription = preg_match_all("/.*/", $itemDesc, $matchesDescription);
            if($validDescription === 1) {
                //<description>
                $node->addChildCData('description', $itemDesc);
            }
            else {
                //<description>
                $node->addChildCData('description', substr(implode("", $matchesDescription[0]), 0, 100));
            }

            $validSku = preg_match_all("/[\p{L}0-9\s\-]+/", $item->getSku(), $matchesSku);
            if($validSku === 1) {
                //<productSku>
                $node->addChildCData('productSku', substr($item->getSku(), 0, 12));
            }

            //<productCode>
            $node->addChild('productCode', $item->getId());

            //<quantity>
            $node->addChild('quantity', $item->getQty());

            /* Item price data */
                //$priceInclTax = $item->getPriceInclTax()-$item->getDiscountAmount();

                $weeTaxApplied = $item->getWeeeTaxAppliedAmount();

                //$unitTaxAmount = number_format($priceInclTax - $item->getPrice(), 2, '.', '');
                $unitTaxAmount = number_format($item->getTaxAmount(), 2, '.', '');

                $unitNetAmount = number_format(($item->getPrice()+$weeTaxApplied)-$item->getDiscountAmount(), 2, '.', '');

                $unitGrossAmount = $unitNetAmount + $unitTaxAmount;
                //$unitGrossAmount = number_format($priceInclTax, 2, '.', '');

                $totalGrossAmount = $unitGrossAmount * $item->getQty();
                //$totalGrossAmount = number_format($item->getRowTotalInclTax()-$item->getDiscountAmount(), 2, '.', '');

                //<unitNetAmount>
                $node->addChild('unitNetAmount', $unitNetAmount);
                //<unitTaxAmount>
                $node->addChild('unitTaxAmount', $unitTaxAmount);
                //<unitGrossAmount>
                $node->addChild('unitGrossAmount', $unitGrossAmount);
                //<totalGrossAmount>
                $node->addChild('totalGrossAmount', $totalGrossAmount);
            /* Item price data */

            //<recipientFName>
            $node->addChildCData('recipientFName', substr(trim($shippingAdd->getFirstname()), 0, 20));

            //<recipientLName>
            $node->addChildCData('recipientLName', substr(trim($shippingAdd->getLastname()), 0, 20));

            //<recipientMName>
            if($shippingAdd->getMiddlename()) {
                $node->addChildCData('recipientMName', substr(trim($shippingAdd->getMiddlename()), 0, 1));
            }

            //<recipientSal>
            if($shippingAdd->getPrefix()) {
                $node->addChildCData('recipientSal', substr(trim($shippingAdd->getPrefix()), 0, 4));
            }

            //<recipientEmail>
            if($shippingAdd->getEmail()) {
                $node->addChildCData('recipientEmail', substr(trim($shippingAdd->getEmail()), 0, 45));
            }

            //<recipientPhone>
            $node->addChildCData('recipientPhone', substr(trim($shippingAdd->getTelephone()), 0, 20));

            $address1 = substr(trim($shippingAdd->getStreet(1)), 0, 100);
            //<recipientAdd1>
            $node->addChildCData('recipientAdd1', $address1);

            //<recipientAdd2>
            if($shippingAdd->getStreet(2)) {
                $node->addChildCData('recipientAdd2', substr(trim($shippingAdd->getStreet(2)), 0, 100));
            }

            //<recipientCity>
            $node->addChildCData('recipientCity', substr(trim($shippingAdd->getCity()), 0, 40));

            //<recipientState>
            if($shippingAdd->getCountry() == 'US') {
                if ($quote->getIsVirtual()) {
                    $node->addChild('recipientState', substr(trim($billingAdd->getRegionCode()), 0, 2));
                }
                else {
                    $node->addChild('recipientState', substr(trim($shippingAdd->getRegionCode()), 0, 2));
                }
            }

            //<recipientCountry>
            $node->addChild('recipientCountry', substr(trim($shippingAdd->getCountry()), 0, 2));

            //<recipientPostCode>
            $_postCode = '000';
            if($shippingAdd->getPostcode()) {
                $_postCode = $shippingAdd->getPostcode();
            }
            $node->addChildCData('recipientPostCode', substr(trim($_postCode), 0, 9));

        }

        //Sum up shipping totals when using SERVER with MAC
        if($this->_isMultishippingCheckout() && ($quote->getPayment()->getMethod() == 'sagepayserver') ) {

            $shippingInclTax = $shippingTaxAmount = 0.00;

            $addresses = $quote->getAllAddresses();
            foreach($addresses as $address) {
                $shippingInclTax   += $address->getShippingInclTax();
                $shippingTaxAmount += $address->getShippingTaxAmount();
            }

        }
        else {
            $shippingInclTax   = $shippingAdd->getShippingInclTax();
            $shippingTaxAmount = $shippingAdd->getShippingTaxAmount();
        }

        //if(0 != round($shippingAdd->getShippingInclTax())) {

            //<deliveryNetAmount>
            $basket->addChild('deliveryNetAmount', number_format($shippingInclTax, 2, '.', ''));

            //<deliveryTaxAmount>
            $basket->addChild('deliveryTaxAmount', number_format($shippingTaxAmount, 2, '.', ''));

            //<deliveryGrossAmount>
            $basket->addChild('deliveryGrossAmount', number_format($shippingInclTax, 2, '.', ''));

        //}

        //<shippingFaxNo>
        if($shippingAdd->getFax()) {
            $basket->addChildCData('shippingFaxNo', substr(trim($shippingAdd->getFax()), 0, 20));
        }

        //Mage::log("\n\n" . $basket->asNiceXml());

        $xmlBasket = str_replace("\n", "", trim($basket->asXml()));

        return $xmlBasket;
    }

    /**
     * Return customer data in xml format.
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return string Xml data
     */
    public function getCustomerXml($quote) {

        $_xml = null;
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod();

        if ($checkoutMethod) {

            $customer = new Varien_Object;
            switch ($checkoutMethod) {
                case 'register':
                case 'guest':
                    $customer->setMiddlename($quote->getBillingAddress()->getMiddlename());
                    $customer->setPreviousCustomer('0');
                    break;
                case 'customer':
                    //Load customer by Id
                    $customer = $quote->getCustomer();
                    $customer->setPreviousCustomer('1');
                    break;
                default:
                    $customer = $quote->getCustomer();
                    $customer->setPreviousCustomer('0');
                    break;
            }

            $customer->setWorkPhone($quote->getBillingAddress()->getFax());
            $customer->setMobilePhone($quote->getBillingAddress()->getTelephone());

            $xml = new Ebizmarts_Simplexml_Element('<customer />');

            if ($customer->getMiddlename()) {
                $xml->addChild('customerMiddleInitial', substr($customer->getMiddlename(), 0, 1));
            }

            if ($customer->getDob()) {
                $_dob = substr($customer->getDob(), 0, strpos($customer->getDob(), ' '));
                $xml->addChildCData('customerBirth', $_dob); //YYYY-MM-DD
            }

            if ($customer->getWorkPhone()) {
                $xml->addChildCData('customerWorkPhone', substr(str_pad($customer->getWorkPhone(), 11, '0', STR_PAD_RIGHT), 0, 19));
            }

            if ($customer->getMobilePhone()) {
                $xml->addChildCData('customerMobilePhone', substr(str_pad($customer->getMobilePhone(), 11, '0', STR_PAD_RIGHT), 0, 19));
            }

            $xml->addChild('previousCust', $customer->getPreviousCustomer());

            if($customer->getId()) {
                $xml->addChild('customerId', $customer->getId());
            }

            //$xml->addChild('timeOnFile', 10);

            $_xml = str_replace("\n", "", trim($xml->asXml()));
        }

        return $_xml;
    }

}
