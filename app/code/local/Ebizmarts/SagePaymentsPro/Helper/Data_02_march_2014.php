<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 10:15 AM
 * File   : Data.php
 * Module : Ebizmarts_SagePaymentsPro
 */ 
class Ebizmarts_SagePaymentsPro_Helper_Data extends Mage_Core_Helper_Abstract {
    protected $_ccCards = array();

    public function F91B2E37D34E5DC4FFC59C324BDC1157C() {
        if (false === Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_ENABLE)) {
            return true;
        } $R8409EAA6EC0CE2EA307354B2E150F8C2 = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $REBBCEB7D5CE9F8309DCC3226F5DAC53B = Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_LICENSE);
        $R1A634B62E7FB6CBC3AD8309D17FDC73C = substr(strrev($R8409EAA6EC0CE2EA307354B2E150F8C2), 0, strlen($R8409EAA6EC0CE2EA307354B2E150F8C2));
        $R7BCAA4FB61D5AD641E1B67637D894EC1 = crypt($R8409EAA6EC0CE2EA307354B2E150F8C2 . 'Ebizmarts_SagePayments', $R1A634B62E7FB6CBC3AD8309D17FDC73C);
        $R835CC35CB400C713B188267E7C10C798 = ($R7BCAA4FB61D5AD641E1B67637D894EC1 === $REBBCEB7D5CE9F8309DCC3226F5DAC53B);
        return $R835CC35CB400C713B188267E7C10C798;
    }

    public function creatingAdminOrder() {
        $controllerName = Mage::app()->getRequest()->getControllerName();
        return ($controllerName == 'sales_order_create' || $controllerName == 'adminhtml_sales_order_create' || $controllerName == 'sales_order_edit');
    }

    public function getCardNiceDate($string) {
        $newString = $string;

        if (strlen($string) == 4) {
            $date = str_split($string, 2);
            $newString = $date[0] . '/' . '20' . $date[1];
        }

        return $newString;
    }

    public function getCcImage($cname) {
        return Mage::getModel('core/design_package')->getSkinUrl('sagepaymentspro/images/cc/' . str_replace(' ', '_', strtolower($cname)) . '.gif');
    }

    public function getCardLabel($value, $concatImage = true) {
        if (empty($this->_ccCards)) {
            $this->_ccCards = Mage::getModel('ebizmarts_sagepaymentspro/config')->getCcTypesSagePayments();
        }

        $label = '';
//        $cardLabel = (isset($this->_ccCards[$value]) ? $this->_ccCards[$value] : '');
        $cardLabel = $value;

        if ($concatImage) {
            $label = '<img src="' . $this->getCcImage($cardLabel) . '" title="' . $cardLabel . ' logo" alt="' . $cardLabel . ' logo" />  ';
        }

        $label .= $cardLabel;

        return $label;
    }
    public function getSagePaymentsConfigJson()
    {
        $conf = array();
        $conf ['global']['valid'] = (int) Mage::helper('ebizmarts_sagepaymentspro')->F91B2E37D34E5DC4FFC59C324BDC1157C();
        $conf ['global']['not_valid_message'] = $this->__('This SagePayments module\'s license is NOT valid.');
        return Zend_Json::encode($conf);
    }
}