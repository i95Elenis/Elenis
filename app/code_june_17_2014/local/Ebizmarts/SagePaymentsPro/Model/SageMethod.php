<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 3:39 PM
 * File   : SageMethod.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_SageMethod extends Mage_Payment_Model_Method_Cc
{
    protected $_code                    = 'sagepaymentspro';
    protected $_formBlockType           = 'ebizmarts_sagepaymentspro/payment_form_sagePaymentsPro';
    protected $_infoBlockType           = 'ebizmarts_sagepaymentspro/payment_info_sagePaymentsPro';
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canSaveCc 				= true;

    protected $_useToken                = true;
    const REQUEST_TYPE_PAYMENT          = 'PAYMENT';
    const REQUEST_TYPE_AUTHORIZE        = 'AHUTHORIZE';
    const CODE_PAYMENT                  = '01';
    const CODE_AUTHORIZE                = '02';
    const CODE_CAPTURE                  = '11';
    const CODE_VOID                     = '04';
    const CODE_REFUND                   = '06';
    const CODE_PRIOR_AUTH_SALE          = '05';
    const RESPONSE_CODE_APPROVED        = 'A';
    const RESPONSE_CODE_REJECTED        = 'X';
    const RESPONSE_CODE_NOTAUTHED       = 'E';
    const STATUS_OK                     = 'OK';
    // 'capture','authorize','refund','void','release'),
    const TRANSACTION_TYPE_CAPURE       = 'capture';
    const TRANSACTION_TYPE_AUTHORIZE    = 'authorize';
    const TRANSACTION_TYPE_REFUND       = 'refund';
    const TRANSACTION_TYPE_VOID         = 'void';
    const TRANSACTION_TYPE_RELEASE      = 'release';

    public function validate()
    {
        $data = $this->getInfoInstance();

        if(!is_null($data->getCcNumber()) && $data->getCcNumber() != '') {
            return parent::validate();
        }
    }
    /**
     * @param mixed $data
     * @return $this|Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();

        if(!is_null($data->getCcNumber()) && $data->getCcNumber() != '') {
            $info->setCcType($data->getCcType())
                ->setCcOwner($data->getCcOwner())
                ->setCcLast4(substr($data->getCcNumber(), -4))
                ->setCcNumber($data->getCcNumber())
                ->setCcCid($data->getCcCid())
                ->setCcExpMonth($data->getCcExpMonth())
                ->setCcExpYear($data->getCcExpYear())
                ->setCcSsIssue($data->getCcSsIssue())
                ->setCcSsStartMonth($data->getCcSsStartMonth())
                ->setCcSsStartYear($data->getCcSsStartYear());
            $info->setAdditionalInformation('remembertoken',!is_null($data->getRemembertoken()) ? 1 : 0);
        }
        else {
            $token = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->load($data->getSagepayTokenCcId());
            $info->setCcCid($data->getCcCid())
                ->setCcExpMonth(substr($token->getExpiryDate(),0,2))
                ->setCcExpYear('20'.substr($token->getExpiryDate(),-2))
                ->setCcType($token->getCardType())
                ->setCcLast4($token->getLastFour());
            $info->setAdditionalInformation('token', $token->getToken());
        }

        return $this;
    }
    /**
     * Capture a payment. This function is called to make a payment or to release a authorized payment
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {

        if($payment->getLastTransId()) {
            return $this->release($payment,$amount);
        }
        else {
            return $this->sale($payment,$amount);
        }


    }

    /**
     * Make a sale
     * @param Varien_Object $payment
     * @param $amount
     * @return $this
     */
    protected function sale(Varien_Object $payment, $amount)
    {
        Mage::getSingleton('checkout/session')->setMd(null)
            ->setAcsurl(null)
            ->setPareq(null);
        $error = false;

        $token = $this->getInfoInstance()->getAdditionalInformation('token');
        if($token) {
            $this->_useToken = true;
        }
        else {
            $remembertoken = $this->getInfoInstance()->getAdditionalInformation('remembertoken');
            if($remembertoken) {
                $this->_addToken($payment);
            }
        }

        $payment->setAnetTransType(self::REQUEST_TYPE_PAYMENT);
        $payment->setAmount($amount);
        $payment->setTransactionType(self::TRANSACTION_TYPE_CAPURE);
        if($this->_useToken) {
            $data = $this->_buildRequestForToken($payment,$amount,$token);
            $result = $this->_postRequestForToken($data, self::CODE_PAYMENT);
        }
        else {
            $data= $this->_buildRequest($payment);
            $result = $this->_postRequest($data, self::CODE_PAYMENT);
        }
        if ($result->getResponseStatus() == self::RESPONSE_CODE_APPROVED) {
            // set the payment data
            $payment->setLastTransId($result->getPostCodeResult());
            $this->_saveTransaction($payment,$result);
        }
        else{
            if ($result->getResponseStatusDetail()) {
                $error = '';
                if ($result->getResponseStatus() == self::RESPONSE_CODE_NOTAUTHED) {
                    $error = Mage::helper('sagepaymentspro')->__('Your credit card can not be authenticated: ');
                } else if ($result->getResponseStatus() == self::RESPONSE_CODE_REJECTED) {
                    $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Your credit card was rejected: ');
                }
                $error .= $result->getResponseStatusDetail();
            }
            else {
                $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Error in capturing the payment');
            }
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;

    }
    protected function release(Varien_Object $payment, $amount)
    {
        $error = false;
        $payment->setTransactionType(self::TRANSACTION_TYPE_RELEASE);
        if($amount>0) {
            $data = $this->_buildAuthoriseRequest($payment,$amount);
            $result = $this->_postRequest($data,self::CODE_PRIOR_AUTH_SALE);

            if(is_object($result)) {
                switch ($result->getResponseStatus()) {
                    case self::RESPONSE_CODE_APPROVED:
                        $this->_saveTransaction($payment,$result);
                        break;
                    default:
                        $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Error at SagePayments in authorizing the payment');
                        break;
                }
            }

        }else {
            $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            $error .= "\r\n". $result->getResponseStatusDetail();
            Mage::throwException($error);
        }

        return $this;
    }
    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        Mage::getSingleton('checkout/session')->setMd(null)
            ->setAcsurl(null)
            ->setPareq(null);
        $error = false;
		$this->_useToken = false;
        $token = $this->getInfoInstance()->getAdditionalInformation('token');
        if($token) {
            $this->_useToken = true;
        }
        else {
            $remembertoken = $this->getInfoInstance()->getAdditionalInformation('remembertoken');
            if($remembertoken) {
                $this->_addToken($payment);
            }
        }

        $payment->setAnetTransType(self::REQUEST_TYPE_AUTHORIZE);
        $payment->setAmount($amount);
        $payment->setTransactionType(self::TRANSACTION_TYPE_AUTHORIZE);
        if($this->_useToken) {
            $data = $this->_buildRequestForToken($payment,$amount,$token);
            $result = $this->_postRequestForToken($data, self::CODE_AUTHORIZE);
        }
        else {
            $data= $this->_buildRequest($payment);
            $result = $this->_postRequest($data, self::CODE_AUTHORIZE);
        }

        if ($result->getResponseStatus() == self::RESPONSE_CODE_APPROVED) {
            // set the payment data
            $payment->setLastTransId($result->getTrnSecuritykey());
            $this->_saveTransaction($payment,$result);
        }
        else{
            if ($result->getResponseStatusDetail()) {
                $error = '';
                if ($result->getResponseStatus() == self::RESPONSE_CODE_NOTAUTHED) {
                    $error = Mage::helper('sagepaymentspro')->__('Your credit card can not be authenticated: ');
                } else if ($result->getResponseStatus() == self::RESPONSE_CODE_REJECTED) {
                    $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Your credit card was rejected: ');
                }
                $error .= $result->getResponseStatusDetail();
            }
            else {
                $error = Mage::helper('ebizmarts_sagepaymentspro')->__('Error in capturing the payment');
            }
        }
        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return Mage_Payment_Model_Abstract|void
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $payment->setTransactionType(self::TRANSACTION_TYPE_REFUND);
        $error = false;
        if($amount>0) {
            //$sagePaymentsInfo = $this->_getSagePaymentsInfo($payment->getParentId());
            $data = $this->_buildRefundRequest($payment, self::CODE_REFUND, $amount);
            $result = $this->_postRequest($data,self::CODE_REFUND);

            if(is_object($result)) {
                switch ($result->getResponseStatus()) {
                    case self::RESPONSE_CODE_APPROVED:

                        // Add refund transaction to log
                        $this->_saveTransaction($payment,$result);
                        break;
                    default:
                        $error = Mage::helper('sagepaymentspro')->__('Error at Sage Pay in refunding the payment');
                        break;
                }
            }

        }else {
            $error = Mage::helper('sagepaymentspro')->__('Error in refunding the payment');
        }

        if ($error !== false) {
            Mage::throwException($error);
        }
        return $this;
    }

    /**
     * Communication routines
     */

    /**
     * @param Varien_Object $payment
     * @return array
     */
    protected function _buildRequest(Varien_Object $payment)
    {
        $order = $payment->getOrder();
        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);
        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $data['C_NAME'] = $billing->getData('lastname') . ' ' . $billing->getData('firstname');
                $data['C_ADDRESS'] = $billing->getStreet(1);
                $data['C_CITY'] = $billing->getCity();
                $data['C_STATE'] = $billing->getRegion();
                $data['C_ZIP'] = $billing->getPostcode();
                $data['C_COUNTRY'] = $billing->getCountry();
                if (!preg_match("/^([a-zA-Z0-9])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",
                    $order->getData('customer_email'))) {
                    $data['C_EMAIL']= "";
                } else {
                    $data['C_EMAIL']= $order->getData('customer_email');
                }
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $data['C_SHIPPING_NAME'] = $shipping->getData('lastname') . ' ' . $shipping->getData('firstname');
                $data['C_SHIPPING_ADDRESS'] = $shipping->getStreet(1);
                $data['C_SHIPPING_CITY'] = $shipping->getCity();
                $data['C_SHIPPING_STATE'] = $shipping->getRegion();
                $data['C_SHIPPING_ZIP'] = $shipping->getPostcode();
                $data['C_SHIPPING_COUNTRY'] = $shipping->getCountry();
                $data['C_TELEPHONE'] = $shipping->getTelephone();

            } else {
                #If the cart only has virtual products, I need to put an shipping address to Sage Pay.
                #Then the billing address will be the shipping address to
                $data['C_SHIPPING_NAME']= $billing->getLastName() . ' ' . $billing->getFirstName();
                $data['C_SHIPPING_NAME']= $billing->getStreet(1);
                $data['C_SHIPPING_CITY']= $billing->getCity();
                $data['C_SHIPPING_STATE']= $billing->getRegion();
                $data['C_SHIPPING_ZIP']= $billing->getPostcode();
                $data['C_SHIPPING_COUNTRY']= $billing->getCountry();
            }
        }

        if($payment->getCcNumber()){
            $data['C_CARDNUMBER']= $payment->getCcNumber();
            $data['C_EXP']= sprintf('%02d%02d', $payment->getCcExpMonth(), substr($payment->getCcExpYear(), strlen($payment->getCcExpYear()) - 2));
        }
        $data['T_AMT']= $order->getData('grand_total');
        $data['T_SHIPPING'] = '';
        $data['T_TAX'] = '';
        $data['T_ORDERNUM'] = $order->getIncrementId();
        $data['C_CVV'] = $payment->getCcCid();
        return $data;
    }

    /**
     * @param Varien_Object $payment
     * @param $amount
     * @return array
     */
    protected function _buildAuthoriseRequest(Varien_Object $payment, $amount)
    {

        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);

        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;
        $data['T_AMT'] = $amount;
        $data['T_REFERENCE'] = $payment->getLastTransId();
        return $data;
    }

    /**
     * @param Varien_Object $payment
     * @param $code
     * @param $amount
     * @return array
     */
    protected function _buildRefundRequest(Varien_Object $payment, $code, $amount)
    {
        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);

        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;
        $data['T_AMT'] = $amount;
        $data['T_REFERENCE'] = $payment->getLastTransId();
        return $data;
    }

    /**
     * @param $data
     * @param $operation
     * @return Ebizmarts_SagePaymentsPro_Model_Entity_Result
     */
    protected function _postRequest($data, $operation)
    {
		Mage::log(__METHOD__);
        if(isset($data['C_STATE']) && $data['C_STATE']=='') {
            $data['C_STATE'] = '__';
        }
        $auxdata = $data;
        if(isset($data['C_CARDNUMBER'])) {
            $auxdata['C_CARDNUMBER'] = substr_replace($auxdata['C_CARDNUMBER'],"XXXXXXXXXXXXX",0,strlen($auxdata['C_CARDNUMBER'])-3);
        }
        if(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LOG, $this->getStoreId())) {
            Mage::log($auxdata);
        }
        $storeId = $this->getStoreId();
        try {
            $client = new SoapClient(Mage::getStoreConfig('payment/sagepaymentspro/api',$storeId));
            switch($operation) {
                case self::CODE_PAYMENT:
                    $rc = $client->BANKCARD_SALE($data);
                    $res = $rc->BANKCARD_SALEResult->any;
                    break;
                case self::CODE_AUTHORIZE:
                    $rc = $client->BANKCARD_AUTHONLY($data);
                    $res = $rc->BANKCARD_AUTHONLYResult->any;
                    break;
                case self::CODE_REFUND:
                    $rc = $client->BANKCARD_CREDIT($data);
                    $res = $rc->BANKCARD_CREDITResult->any;
                    break;
                case self::CODE_PRIOR_AUTH_SALE:
                    $rc = $client->BANKCARD_PRIOR_AUTH_SALE($data);
                    $res = $rc->BANKCARD_PRIOR_AUTH_SALEResult->any;
                    break;
                case self::CODE_VOID:
                    $rc = $client->BANKCARD_VOID($data);
                    $res = $rc->BANKCARD_VOIDResult->any;
                default:
                    break;
            }
        }
        catch(exception $e) {
            Mage::throwException("Communication error: your server is not capable to communicate with Sage Payment Solutions server");
        }
        if(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LOG, $this->getStoreId())) {
            Mage::log($res);
        }
        $result = Mage::getModel('ebizmarts_sagepaymentspro/entity_result');
        $rDoc= new DOMDocument();
        $rDoc->loadXML($res);
        $approvalIndicator = $rDoc->getElementsByTagName('APPROVAL_INDICATOR');
        if($approvalIndicator->length>0) {
            $result->setResponseStatus($approvalIndicator->item(0)->nodeValue);
        }
        $code = $rDoc->getElementsByTagName('CODE');
        if($code->length>0) {
            $result->setPostCodeResult($code->item(0)->nodeValue);
        }
        $message = $rDoc->getElementsByTagName('MESSAGE');
        if($message->length >0) {
            $result->setResponseStatusDetail($message->item(0)->nodeValue);
        }
        $cvv_indicator = $rDoc->getElementsByTagName('CVV_INDICATOR');
        if($cvv_indicator->length>0) {
            $result->setCvvIndicator($cvv_indicator->item(0)->nodeValue);
        }
        $avs_indicator = $rDoc->getElementsByTagName('AVS_INDICATOR');
        if($avs_indicator->length>0) {
            $result->setAvsIndicator($avs_indicator->item(0)->nodeValue);
        }
        $risk_indicator = $rDoc->getElementsByTagName('RISK_INDICATOR');
        if($risk_indicator->length>0) {
            $result->setRiskIndicator($risk_indicator->item(0)->nodeValue);
        }
        $reference = $rDoc->getElementsByTagName('REFERENCE');
        if($reference->length>0) {
            $result->setTrnSecuritykey($reference->item(0)->nodeValue);
        }
        $order_number = $rDoc->getElementsByTagName('ORDER_NUMBER');
        if($order_number->length>0) {
            $result->setOrderNum($order_number->item(0)->nodeValue);
        }
        if(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LOG, $this->getStoreId())) {
            Mage::log($result);
        }
        return $result;

    }

    /**
     * Communication routines for token system
     */
    protected function _buildRequestForToken(Varien_Object $payment, $amount, $token)
    {
        $order = $payment->getOrder();
        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);
        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;

        if (!empty($order)) {
            $billing = $order->getBillingAddress();
            if (!empty($billing)) {
                $data['C_NAME'] = $billing->getData('lastname') . ' ' . $billing->getData('firstname');
                $data['C_ADDRESS'] = $billing->getStreet(1);
                $data['C_CITY'] = $billing->getCity();
                $data['C_STATE'] = $billing->getRegion();
                $data['C_ZIP'] = $billing->getPostcode();
                $data['C_COUNTRY'] = $billing->getCountry();
                if (!preg_match("/^([a-zA-Z0-9])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",
                    $order->getData('customer_email'))) {
                    $data['C_EMAIL']= "";
                } else {
                    $data['C_EMAIL']= $order->getData('customer_email');
                }
            }

            $shipping = $order->getShippingAddress();
            if (!empty($shipping)) {
                $data['C_SHIPPING_NAME'] = $shipping->getData('lastname') . ' ' . $shipping->getData('firstname');
                $data['C_SHIPPING_ADDRESS'] = $shipping->getStreet(1);
                $data['C_SHIPPING_CITY'] = $shipping->getCity();
                $data['C_SHIPPING_STATE'] = $shipping->getRegion();
                $data['C_SHIPPING_ZIP'] = $shipping->getPostcode();
                $data['C_SHIPPING_COUNTRY'] = $shipping->getCountry();
                $data['C_TELEPHONE'] = $shipping->getTelephone();

            } else {
                #If the cart only has virtual products, I need to put an shipping address to Sage Pay.
                #Then the billing address will be the shipping address to
                $data['C_SHIPPING_NAME']= $billing->getLastName() . ' ' . $billing->getFirstName();
                $data['C_SHIPPING_NAME']= $billing->getStreet(1);
                $data['C_SHIPPING_CITY']= $billing->getCity();
                $data['C_SHIPPING_STATE']= $billing->getRegion();
                $data['C_SHIPPING_ZIP']= $billing->getPostcode();
                $data['C_SHIPPING_COUNTRY']= $billing->getCountry();
            }
        }
        $data['GUID'] = $token;
        $data['T_AMT']= $order->getData('grand_total');
        $data['T_SHIPPING'] = '';
        $data['T_TAX'] = '';
        $data['T_ORDERNUM'] = $order->getIncrementId();
        return $data;

    }
    protected function _postRequestForToken($data, $operation)
    {
        if(isset($data['C_STATE']) && $data['C_STATE']=='') {
            $data['C_STATE'] = '__';
        }
        $storeId = $this->getStoreId();
        try {
            $client = new SoapClient(Mage::getStoreConfig('payment/sagepaymentspro/apitoken',$storeId));
            switch($operation) {
                case self::CODE_PAYMENT:
                    $rc = $client->VAULT_BANKCARD_SALE($data);
                    $res = $rc->VAULT_BANKCARD_SALEResult->any;
                    break;
                case self::CODE_AUTHORIZE:
                    $rc = $client->VAULT_BANKCARD_AUTHONLY($data);
                    $res = $rc->VAULT_BANKCARD_AUTHONLYResult->any;
                    break;
//                case self::CODE_REFUND:
//                    $rc = $client->BANKCARD_CREDIT($data);
//                    $res = $rc->BANKCARD_CREDITResult->any;
//                    break;
//                case self::CODE_PRIOR_AUTH_SALE:
//                    $rc = $client->BANKCARD_PRIOR_AUTH_SALE($data);
//                    $res = $rc->BANKCARD_PRIOR_AUTH_SALEResult->any;
//                    break;
//                case self::CODE_VOID:
//                    $rc = $client->BANKCARD_VOID($data);
//                    $res = $rc->BANKCARD_VOIDResult->any;
                default:
                    break;
            }
        }
        catch(exception $e) {
            Mage::throwException("Communication error: your server is not capable to communicate with Sage Payment Solutions server");
        }
        if(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LOG, $this->getStoreId())) {
            Mage::log($res);
        }
        $result = Mage::getModel('ebizmarts_sagepaymentspro/entity_result');
        $rDoc= new DOMDocument();
        $rDoc->loadXML($res);
        $approvalIndicator = $rDoc->getElementsByTagName('APPROVAL_INDICATOR');
        if($approvalIndicator->length>0) {
            $result->setResponseStatus($approvalIndicator->item(0)->nodeValue);
        }
        $code = $rDoc->getElementsByTagName('CODE');
        if($code->length>0) {
            $result->setPostCodeResult($code->item(0)->nodeValue);
        }
        $message = $rDoc->getElementsByTagName('MESSAGE');
        if($message->length >0) {
            $result->setResponseStatusDetail($message->item(0)->nodeValue);
        }
        $cvv_indicator = $rDoc->getElementsByTagName('CVV_INDICATOR');
        if($cvv_indicator->length>0) {
            $result->setCvvIndicator($cvv_indicator->item(0)->nodeValue);
        }
        $avs_indicator = $rDoc->getElementsByTagName('AVS_INDICATOR');
        if($avs_indicator->length>0) {
            $result->setAvsIndicator($avs_indicator->item(0)->nodeValue);
        }
        $risk_indicator = $rDoc->getElementsByTagName('RISK_INDICATOR');
        if($risk_indicator->length>0) {
            $result->setRiskIndicator($risk_indicator->item(0)->nodeValue);
        }
        $reference = $rDoc->getElementsByTagName('REFERENCE');
        if($reference->length>0) {
            $result->setTrnSecuritykey($reference->item(0)->nodeValue);
        }
        $order_number = $rDoc->getElementsByTagName('ORDER_NUMBER');
        if($order_number->length>0) {
            $result->setOrderNum($order_number->item(0)->nodeValue);
        }
        if(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LOG, $this->getStoreId())) {
            Mage::log($result);
        }
        return $result;

    }

    /**
     * Token routines
     */

    /**
     * @param Varien_Object $payment
     * @param Varien_Object $result
     * @return $this
     */
    protected function _saveTransaction(Varien_Object $payment, Varien_Object $result)
    {
        $transaction = Mage::getModel('ebizmarts_sagepaymentspro/transaction');
        $transaction->setOrderId($payment->getOrder()->getId())
                    ->setResponseStatus($result->getResponseStatus())
                    ->setPostCodeResult($result->getPostCodeResult())
                    ->setResponseStatusDetail($result->getResponseStatusDetail())
                    ->setCvvIndicator($result->getCvvIndicator())
                    ->setRiskIndicator($result->getRiskIndicator())
                    ->setAmount($payment->getAmount())
                    ->setType($payment->getTransactionType())
                    ->setTrnSecuritykey($result->getTrnSecuritykey());
        $transaction->save();
        return $this;
    }

    /**
     * @param Varien_Object $payment
     */
    protected function _addToken(Varien_Object $payment)
    {
        $data = array();
        if($payment->getCcNumber()){
            $data['CARDNUMBER']= $payment->getCcNumber();
            $data['EXPIRATION_DATE']= sprintf('%02d%02d', $payment->getCcExpMonth(), substr($payment->getCcExpYear(), strlen($payment->getCcExpYear()) - 2));
        }
        $token = $this->_obtainToken($data);
        $this->_persistToken($token,$payment);
    }

    /**
     * @param Varien_Object $payment
     * @return Ebizmarts_SagePaymentsPro_Model_Entity_Result
     */
    protected function _obtainToken(array $data)
    {
//        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);

        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;
//        if($payment->getCcNumber()){
//            $data['CARDNUMBER']= $payment->getCcNumber();
//            $data['EXPIRATION_DATE']= sprintf('%02d%02d', $payment->getCcExpMonth(), substr($payment->getCcExpYear(), strlen($payment->getCcExpYear()) - 2));
//        }
        $url = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN_URL,$storeId);
        $client = new SoapClient($url);
        $res = $client->INSERT_CREDIT_CARD_DATA($data);
        $rDoc= new DOMDocument();
        $rDoc->loadXML($res->INSERT_CREDIT_CARD_DATAResult->any);
        $result = Mage::getModel('ebizmarts_sagepaymentspro/entity_result');
        $success = $rDoc->getElementsByTagName('SUCCESS');
        if($success->length > 0) {
            $result->setSuccess($success->item(0)->nodeValue);
        }
        $guid = $rDoc->getElementsByTagName('GUID');
        if($guid->length > 0) {
            $result->setGuid($guid->item(0)->nodeValue);
        }
        $message = $rDoc->getElementsByTagName('MESSAGE');
        if($message->length > 0) {
            $result->setMessage($message->item(0)->nodeValue);
        }
        Mage::log($result);
        return $result;
    }

    /**
     * @param Varien_Object $tokenData
     * @param Varien_Object $payment
     * @return $this
     */
    protected function _persistToken(Varien_Object $tokenData, Varien_Object $payment)
    {
        $tokenCard = Mage::getModel('ebizmarts_sagepaymentspro/tokencard');
        $storeId = $this->getStoreId();
        $cards = Mage::getModel('ebizmarts_sagepaymentspro/Config')->getCcTypesSagePayments();
        Mage::log($payment->getCcType());

        $tokenCard->setCustomerId($payment->getOrder()->getCustomerId())
                ->setToken($tokenData->getGuid())
                ->setStatus($tokenData->getSuccess())
                ->setCardType($cards[$payment->getCcType()])
                ->setLastFour(substr($payment->getCcNumber(),-4))
                ->setExpiryDate(sprintf('%02d%02d', $payment->getCcExpMonth(), substr($payment->getCcExpYear(), strlen($payment->getCcExpYear()) - 2)))
                ->setStatusDetail($tokenData->getMessage())
                ->setVendor(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId))
                ->setIsDefault(false)
                ->setVisitorSessionId();
        $tokenCard->save();
        return $this;
    }

    /**
     * @param $token
     * @return array
     */
    public function removeCard($token)
    {
        $data = array();
        $storeId = $this->getStoreId();
        $m_id = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID,$storeId);
        $m_key = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MKEY,$storeId);
        $url = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_TOKEN_URL,$storeId);

        $data['M_ID'] = $m_id;
        $data['M_KEY'] = $m_key;
        $data['GUID'] = $token;
        $client = new SoapClient($url);
        $res = $client->DELETE_DATA($data);
        Mage::log($res);
        if($res->DELETE_DATAResult == 1) {
            $rc = 'OK';
        }
        else {
            $rc = "NOTOK";
        }
        return array('Status' => $rc);
    }
    public function registerCard($post)
    {
        $data = array();
        $data['CARDNUMBER']= $post['CardNumber'];
        $data['EXPIRATION_DATE']= sprintf('%02d%02d', $post['ExpiryMonth'], substr($post['ExpiryYear'], strlen($post['ExpiryYear']) - 2));
        $token = $this->_obtainToken($data);
        return $token;
    }
}
