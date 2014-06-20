<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 6/27/13
 * Time   : 5:02 PM
 * File   : TokenController.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Adminhtml_TokenController extends Mage_Adminhtml_Controller_Action
{

    public function newAction()
    {
        $this->loadLayout();

        Mage::register('admin_tokenregister', $this->getRequest()->getParam('customer_id'));

        $result = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->registerCard();
        if(!isset($result['NextURL'])){
            $this->_getSession()->addError($this->__('Could not register token, please try again.'));
            $this->_redirectReferer();
            return;
        }

        $this->getResponse()->setBody(
            '<iframe style="width:100%;height:100%;padding:0;margin:0;border:none;" src="'.$result['NextURL'].'"></iframe>'
        );
    }
    public function addAction()
    {
        MAge::log(__METHOD__);
        if($this->getRequest()->getParam('popup')) {
            Mage::log('es un popup');
            $this->loadLayout('popup_sagepay');
        }
        $this->_title($this->__('Customer'))
            ->_title($this->__('Token'))
            ->_title($this->__('New token'));

        $this->_customerid = $this->getRequest()->getParam('id');
        $this->getLayout()->setBlock('','ebizmarts_sagepaymentspro/adminhtml_token_add');
        $this->renderLayout();
    }
    public function deleteAction()
    {
        Mage::log(__METHOD__);
    }
    public function saveaddAction()
    {
        Mage::log(__METHOD__);
        Mage::log($this->getRequest()->getPost());

        if($this->getRequest()->isPost()){ #DIRECT POST
            $post = $this->getRequest()->getPost('token');


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
            Mage::log($cards);
            if($rs['success'] == 'true'){
                $save = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')
                    ->setToken($rs['guid'])
                    ->setStatus($rs['success'])
                    ->setCardType($cards[$post['CardType']])
                    ->setExpiryDate($post['ExpiryDate'])
                    ->setStatusDetail($rs['message'])
                    ->setCustomerId($post['customer_id'])
                    ->setLastFour(substr($post['CardNumber'], -4))
                    ->setVendor(Mage::getStoreConfig(Ebizmarts_SagePaymentsPro_Model_Config::CONFIG_MID))
                    ->save();
                Mage::log($rs);
                return $this->getResponse()->setBody('<html>
                                                        <body>
                                                            <script type="text/javascript">
                                                                    window.parent.Control.Modal.close();
                                                            </script>
                                                        </body>
                                                    </html>');
            }else{
                return $this->getResponse()->setBody('<html>
                                                        <body>
                                                            <script type="text/javascript">
                                                                    alert("'.$rs['message'].'");
                                                                    window.parent.Control.Modal.close();
                                                            </script>
                                                        </body>
                                                    </html>');
            }


        }
    }
    public function massDeleteAction()
    {
        if($this->getRequest()->isPost()){ #Mass action
            $ok = $nok = 0;
            $ids = $this->getRequest()->getPost('cards', array());
            Mage::log($ids);
            foreach($ids as $cardId) {
                $card = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->load($cardId);
                if($card->getId()) {

                }
            }
        }
        $this->_redirectReferer();
        return;
    }
    protected function _getCustomerId()
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            return $customerData->getId();
        }
    }
}