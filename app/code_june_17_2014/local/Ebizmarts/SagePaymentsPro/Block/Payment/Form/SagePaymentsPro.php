<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 4:41 PM
 * File   : SagePaymentsPro.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Payment_Form_SagePaymentsPro extends Ebizmarts_SagePaymentsPro_Block_Payment_Form_SagePaymentsProToken
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebizmarts/sagepaymentspro/payment/form/sagepaymentsprowithtoken.phtml');
    }
    public function _prepareLayout()
    {
        $this->setChild('token.cards.li', $this->getLayout()->createBlock('ebizmarts_sagepaymentspro/payment_form_tokenList','token.cards.li')->setCanUseToken($this->canUseToken())->setPaymentMethodCode('sagepaymentspro'));
        return parent::_prepareLayout();
    }
    public function getSagePaymentsAvailableTypes()
    {
        $types = Mage::getSingleton('ebizmarts_sagepaymentspro/config')->getCcTypesSagePayments();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigDataSagePaymentsPro('cctypesSagePaymentsPro');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }
}