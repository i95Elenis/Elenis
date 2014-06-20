<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/15/13
 * Time   : 3:50 PM
 * File   : SagePaymentsPro.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Payment_Info_SagePaymentsPro extends Mage_Payment_Block_Info_Cc
{
    protected $_collection = null;

    protected function _construct()
    {
        parent::_construct();
//        $this->setTemplate('ebizmarts/sagepaymentspro/payment/info/sagepaymentspro.phtml');
    }
    public function getSpecificInformation()
    {
        $info_orig = parent::getSpecificInformation();
        $this->_collection = Mage::getModel('ebizmarts_sagepaymentspro/transaction')->getCollection();

        $order = Mage::registry('current_order');
        $orderParam = $this->getRequest()->getParam('order_id');

        if ($order) {
            $this->_collection->addFieldToFilter('order_id', $order->getId());
        }else if($orderParam){
            $this->_collection->addFieldToFilter('order_id', $orderParam);
        }
        else {
            $creditmemo = Mage::registry('current_creditmemo');
            if($creditmemo) {
                $this->_collection->addFieldToFilter('order_id', $creditmemo->getOrder()->getId());
            }
            else {
                return $info_orig;
            }
        }
        $info=array();
        foreach($this->_collection as $transaction) {
            $info['Type'] = ucfirst($transaction->getType());
            $info['Cvv Indicator'] = $transaction->getCvvIndicator();
            $info['Date'] = $transaction->getTransactionDate();
            $info['Risk Indicator'] = $transaction->getRiskIndicator();
            $info['Post Code Result'] = $transaction->getPostCodeResult();
            $info['Security Key'] = $transaction->getTrnSecuritykey();
        }
        return array_merge($info_orig,$info);
    }
    public function getRefunds() {
        $collection = new Varien_Data_Collection();
        foreach($this->_collection as $refund) {
            if($refund->getType() == 'refund') {
                $collection->addItem($refund);
            }
        }
        return $collection;
    }
    public function getAuthorizations() {
        $collection = new Varien_Data_Collection();
        foreach($this->_collection as $refund) {
            if($refund->getType() == 'release') {
                $collection->addItem($refund);
            }
        }
        return $collection;

    }
    public function getChildHtml($name = '',$useCache=true,$sorted=false)
    {
        return $this->setTemplate('ebizmarts/sagepaymentspro/payment/info/sagepaymentspro.phtml')->toHtml();
    }
}