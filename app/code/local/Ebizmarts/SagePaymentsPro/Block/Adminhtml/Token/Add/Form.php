<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 7/4/13
 * Time   : 3:08 PM
 * File   : Form.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Token_Add_Form  extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        Mage::log(__METHOD__);
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('adminhtml/token/saveadd'), 'method' => 'post'));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('ebizmarts_sagepaymentspro')->__('Card Data')));


        $fieldset->addField('token_customerid','hidden',array(
            'name' => 'token[customer_id]',
            'value' => $this->getRequest()->getParam('customer_id'),
            'label' => 'customerid'
        ));
//
//        $fieldset->addField('inventory_store','hidden',array(
//            'name' => 'inventory[store_id]',
//            'value' => $this->getRequest()->getParam('store'),
//            'label' => 'store'
//        ));
//
        $fieldset->addField('token_name', 'text', array(
            'name'  => 'token[name]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Name on Card'),
            'id'    => 'token_name',
            'title' => Mage::helper('ebizmarts_sagepaymentspro')->__('Name on Card'),
            'required' => true,
        ));
        $fieldset->addField('token_cardtype','select',array(
            'name' => 'token[CardType]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Credit Card Type'),
            'id' => 'token_cardtype',
            'values' => Mage::getSingleton('ebizmarts_sagepaymentspro/system_config_creditCards')->toOptionArray()
        ));
        $fieldset->addField('token_number', 'text', array(
            'name'  => 'token[CardNumber]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Credit Card Number'),
            'id'    => 'token_name',
            'title' => Mage::helper('ebizmarts_sagepaymentspro')->__('Credit Card Number'),
            'required' => true,
        ));
        $fieldset->addField('token_month','select',array(
            'name' => 'token[ExpiryMonth]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Month'),
            'id' => 'token_month',
            'values' => $this->getCcMonths()
        ));
        $fieldset->addField('token_year','select',array(
            'name' => 'token[ExpiryYear]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Year'),
            'id' => 'token_year',
            'values' => $this->getCcYears()
        ));
        $fieldset->addField('token_cvv', 'text', array(
            'name'  => 'token[cvv]',
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Card Verification Number'),
            'id'    => 'token_cvv',
            'title' => Mage::helper('ebizmarts_sagepaymentspro')->__('Card Verification Number'),
            'required' => true,
        ));
//
//        $fieldset->addField('token_cardtype', 'textarea', array(
//            'name'  => 'inventory[description]',
//            'label' => Mage::helper('warehouse')->__('Description'),
//            'id'    => 'inventory_description',
//            'title' => Mage::helper('warehouse')->__('Description'),
//            'note'     => 'Reason of the movment. (optional)',
//        ));



        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        if (is_null($months)) {
            $months[0] =  $this->__('Month');
            $months = array_merge($months, $this->_getConfig()->getMonths());
//            $this->setData('cc_months', $months);
        }
        return $months;
    }
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }
    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if (is_null($years)) {
            $years = $this->_getConfig()->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('cc_years', $years);
        }
        return $years;
    }
}