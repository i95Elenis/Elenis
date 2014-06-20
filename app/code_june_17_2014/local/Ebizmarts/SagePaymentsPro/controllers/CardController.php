<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/21/13
 * Time   : 2:29 PM
 * File   : CardController.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_CardController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if( !$this->_getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('sgusa/card');
        }
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('ebizmarts_sagepaymentspro')->__('SagePayments - Saved Credit Cards'));
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $resp = array('st'=>'nok', 'text'=>'');

        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            $resp ['text'] = $this->__('Please login, you session expired.');
            $this->getResponse()->setBody(Zend_Json::encode($resp));
            return;
        }
        $cardId  = (int)$this->getRequest()->getParam('card');

        $objCard = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->load($cardId);
        if($objCard->getId()){

            # Check if card is from this customer
            if($objCard->getCustomerId() != $this->_getCustomerId()){
                $resp ['text'] = $this->__('Invalid Card #');
                $this->getResponse()->setBody(Zend_Json::encode($resp));
                return;
            }

            try{
                $delete = Mage::getModel('ebizmarts_sagepaymentspro/sageMethod')->removeCard($objCard->getToken());

                if($delete['Status'] == 'OK'){

                    $objCard->delete();

                    $resp ['text'] = $this->__('Success!');
                    $resp ['st'] = 'ok';
                    $this->getResponse()->setBody(Zend_Json::encode($resp));
                    return;
                }else{
                    $resp ['text'] = $this->__('An error ocured, %s', $delete['StatusDetail']);
                    $this->getResponse()->setBody(Zend_Json::encode($resp));
                    return;
                }

            }catch(Exception $e){

                $resp ['text'] = $this->__($e->getMessage());
                $this->getResponse()->setBody(Zend_Json::encode($resp));
                return;

            }

        }

        $resp ['text'] = $this->__('The requested Card does not exist.');
        $this->getResponse()->setBody(Zend_Json::encode($resp));
        return;

    }
    public function defaultAction()
    {
        $resp = array('st'=>'nok', 'text'=>'');

        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            $resp ['text'] = $this->__('Please login, you session expired.');
            $this->getResponse()->setBody(Zend_Json::encode($resp));
            return;
        }
        $defCard = Mage::helper('ebizmarts_sagepaymentspro/token')->getDefaultToken();
        $cardId  = (int)$this->getRequest()->getParam('card');

        $objCard = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->load($cardId);
        if($objCard->getId()){

            # Check if card is from this customer
            if($objCard->getCustomerId() != $this->_getCustomerId()){
                $resp ['text'] = $this->__('Invalid Card #');
                $this->getResponse()->setBody(Zend_Json::encode($resp));
                return;
            }

            try{

                $objCard->setIsDefault(1)
                    ->save();
                $defCard->setIsDefault(0)
                    ->save();
                $resp ['text'] = $this->__('Success!');
                $resp ['st'] = 'ok';
                $this->getResponse()->setBody(Zend_Json::encode($resp));
                return;

            }catch(Exception $e){

                $resp ['text'] = $this->__($e->getMessage());
                $this->getResponse()->setBody(Zend_Json::encode($resp));
                return;

            }

        }

        $resp ['text'] = $this->__('The requested Card does not exist.');
        $this->getResponse()->setBody(Zend_Json::encode($resp));
        return;
    }
    public function registerAction()
    {

        /*
         * DIRECT POST
         */
        if($this->getRequest()->isPost()){ #DIRECT POST

            $post = $this->getRequest()->getPost();

            $post['ExpiryDate'] = str_pad($post['ExpiryMonth'], 2, '0', STR_PAD_LEFT) . substr($post['ExpiryYear'], 2);

//            if($post['StartMonth'] && $post['StartYear']){
//                $post['StartDate'] = str_pad($post['StartMonth'], 2, '0', STR_PAD_LEFT) . substr($post['StartYear'], 2);
//            }
            $rs = (array) Mage::getModel('ebizmarts_sagepaymentspro/sageMethod')->registerCard($post)->getData();

            if(empty($rs)){
                $rs['Status'] = 'ERROR';
                $rs['StatusDetail'] = Mage::helper('ebizmarts_sagepaymentspro')->__('A server to server communication error ocured, please try again later.');
            }
            $cards = Mage::getModel('ebizmarts_sagepaymentspro/config')->getCcTypesSagePayments();
            if($rs['success'] == 'true'){
                $save = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')
                    ->setToken($rs['guid'])
                    ->setStatus($rs['success'])
                    ->setCardType($cards[$post['CardType']])
                    ->setExpiryDate($post['ExpiryDate'])
                    ->setStatusDetail($rs['message'])
                    ->setCustomerId($this->_getCustomerId())
                    ->setLastFour(substr($post['CardNumber'], -4))
                    ->save();
                $rs['Status'] = 'OK';
                $rs = array_change_key_case($rs);
                $resp = $rs;
                $resp ['mark'] = array(
                    'cctype' => $save->getLabel(),
                    'id' => $save->getId(),
                    'defaultchecked' => ($save->getIsDefault() == 1 ? ' checked="checked"' : ''),
                    'ccnumber' => $save->getCcNumber(),
                    'exp' => $save->getExpireDate(),
                    'delurl' => Mage::getUrl('sgusa/card/delete', array('card'=>$save->getId()))
                );
                MAge::log($resp);
            }else{
                $rs = array_change_key_case($rs);
                $resp = $rs;
            }

            return $this->getResponse()->setBody(Zend_Json::encode($resp));

        }
        /*
         * DIRECT POST
         */

        $url = '';
        if(false === Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->customerCanAddCard($this->_getCustomerId())){

            $url = 'ERROR';
            $text = $this->__('You can\'t add more cards, please delete one if you want to add another.');

        }else{
            $text = $this->getLayout()->createBlock('ebizmarts_sagepaymentspro/customer_account_card_form')->toHtml();
        }
        return $this->getResponse()->setBody(Zend_Json::encode(array('text'=>$text, 'url'=>$url)));
    }
    protected function _getCustomerId()
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            return $customerData->getId();
        }
    }

}