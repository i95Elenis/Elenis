<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @author Adjustware
 */
class AdjustWare_Deliverydate_Model_Step
{
    protected  $_errors = array();

    protected function _getCurrentOrder()
    {
        $orderId = Mage::getSingleton('adminhtml/session_quote')->getData('order_id');
        return Mage::getModel('sales/order')->load($orderId);
    }

    protected function _getCurrentQuote()
    {
        $type = Mage_Sales_Model_Quote_Address::TYPE_SHIPPING;
        $item = Mage::getModel('sales/quote_address')->getCollection();

        if (Mage::getSingleton('admin/session')->isLoggedIn())
        {
            $quoteId = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getId();
        }
        else
        {
            $quoteId = Mage::getSingleton('checkout/session')->getQuote()->getId();
        }
        $firstItem = $item->addFieldToFilter('address_type', $type)
            ->addFieldToFilter('quote_id', $quoteId)
            ->getFirstItem();

        return $firstItem;
    }

    protected function _getCurrentOrderOrQuote($index = null)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn())
        {
            if (Mage::getSingleton('adminhtml/session_quote')->getData('order_id')) //if edit order
            {
                return $this->_getCurrentOrder();
            }
            else //if create new order
            {
                return $this->_getCurrentQuote();
            }
        }
        else //creating order on front
        {
            if ($index != null) //if multishipping
            {
                $item = Mage::getModel('sales/quote_address')->getCollection();
                $firstItem = $item->addFieldToFilter('address_id', $index)
                    ->getFirstItem();
                return $firstItem;
            }
            else
            {
                return $this->_getCurrentQuote();
            }
        }
    }

    protected function _getCurrentQuoteFieldValue($fieldName, $index = null)
    {
        $firstItem = $this->_getCurrentOrderOrQuote($index);

        if ($fieldName == 'delivery_date')
        {
            if(Zend_Date::isDate($firstItem->getData($fieldName), Zend_Date::ISO_8601))
            {
                $dateTime = new Zend_Date($firstItem->getData($fieldName), Zend_Date::ISO_8601);
                return $dateTime->toString('yyyy-MM-dd');
            }
            else
            {
                return;
            }
        }
        elseif ($fieldName == 'delivery_time')
        {
            if(Zend_Date::isDate($firstItem->getData('delivery_date'), Zend_Date::ISO_8601))
            {
                $dateTime = new Zend_Date($firstItem->getData('delivery_date'), Zend_Date::ISO_8601);
                return explode(':', $dateTime->get(Zend_Date::TIME_SHORT));
            }
            else
            {
                return;
            }
        }

        return $firstItem->getData($fieldName);
    }

    public function getFields($storeId = null){
        
        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('AdjustWare_Deliverydate')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $storeId = Mage::getSingleton('adminhtml/sales_order_create')->getSession()->getData('store_id');
        if (!($ruler->checkRule('store',$storeId,'store') ))
        {
            return array();
        }
        {#AITOC_COMMENT_START#} */
        if (!Mage::getStoreConfig('checkout/adjdeliverydate/enabled'))
            return array();    
        
        $hlp = Mage::helper('adjdeliverydate');

        $form = new Varien_Data_Form(array(
            'field_name_suffix' => 'adj',
        ));
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        
        //todo add logic for getting fields by step    
        //$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $design = Mage::getDesign();
        if($design->getArea()=='adminhtml')
        {
            $skinUrl = $design->getSkinUrl('images/fam_calendar.gif',array('_default'=>true));
        }
        else
        {
            $skinUrl = $design->getSkinUrl('images/calendar.gif');
        }
        
        $form->addField('delivery_date', 'date', array(
            'name'   => 'delivery_date',
            'label'  => $hlp->__('Delivery Date'),
            'title'  => $hlp->__('Delivery Date'),
            'image'  => $skinUrl,
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => Mage::getStoreConfig('checkout/adjdeliverydate/format'),
            'no_span'      => 1,
            'validator'    => 'adjdeliverydate/validator_deliverydate',
            'value'        => $this->_getCurrentQuoteFieldValue('delivery_date'),
        ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_deliverydate'));
        
        if (Mage::getStoreConfig('checkout/adjdeliverydate/show_time')) // time field
        {
            $form->addField('delivery_time', 'time', array(
                'name'   => 'delivery_time',
                'label'  => $hlp->__('Delivery Time'),
                'title'  => $hlp->__('Delivery Time'),
                'no_span'      => 1,
                'class'  => 'adjtimeselect',
                'value'  => $this->_getCurrentQuoteFieldValue('delivery_time'),
                ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_time'));
        }
        
        if (Mage::getStoreConfig('checkout/adjdeliverydate/show_comment')){
            $form->addField('delivery_comment', 'text', array(
                'name'     => 'delivery_comment',
                'label'    => $hlp->__('Comments'),
                'title'    => $hlp->__('Comments'),
                'no_span'  => 1,
                'class'    => 'input-text delivery-comment',
                'value'    => $this->_getCurrentQuoteFieldValue('delivery_comment'),
            ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_default'));
        }
        
        return $form->getElements();
    }

    public function getFormsFields($_index)
    {
          
        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('AdjustWare_Deliverydate')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $storeId = Mage::getSingleton('adminhtml/sales_order_create')->getSession()->getData('store_id');
        if (!($ruler->checkRule('store',$storeId,'store') ))
        {
            return array();
        }
        {#AITOC_COMMENT_START#} */

        if (!Mage::getStoreConfig('checkout/adjdeliverydate/enabled') || !Mage::getStoreConfig('checkout/adjdeliverydate/multienabled'))
            return array();    
        $hlp = Mage::helper('adjdeliverydate');

        $form = new Varien_Data_Form(array(
            'field_name_suffix' => 'adj',
        ));
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        
        $form->addField('delivery_date'.$_index, 'date', array(
            'name'   => 'delivery_date'.$_index,
            'label'  => $hlp->__('Delivery Date'),
            'title'  => $hlp->__('Delivery Date'),
            'image'  => Mage::getDesign()->getSkinUrl('images/calendar.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => Mage::getStoreConfig('checkout/adjdeliverydate/format'),
            'no_span'      => 1,
            'validator'    => 'adjdeliverydate/validator_deliverydate',
            'value'        => $this->_getCurrentQuoteFieldValue('delivery_date', $_index),
        ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_deliverydate'));
        
        if (Mage::getStoreConfig('checkout/adjdeliverydate/show_time')) // time field
        {
            $form->addField('delivery_time'.$_index, 'time', array(
                'name'   => 'delivery_time'.$_index,
                'label'  => $hlp->__('Delivery Time'),
                'title'  => $hlp->__('Delivery Time'),
                'no_span'      => 1,
                'value'  => $this->_getCurrentQuoteFieldValue('delivery_time', $_index),
                ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_time'));
        }
        
        if (Mage::getStoreConfig('checkout/adjdeliverydate/show_comment')){
            $form->addField('delivery_comment'.$_index, 'text', array(
                'name'     => 'delivery_comment'.$_index,
                'label'    => $hlp->__('Comments'),
                'title'    => $hlp->__('Comments'),
                'no_span'  => 1,
                'class'    => 'input-text delivery-comment',
                'value'    => $this->_getCurrentQuoteFieldValue('delivery_comment', $_index),
            ))->setRenderer($layout->createBlock('adjdeliverydate/renderer_default'));
        }
        
        return $form->getElements();
    }

    public function validate(){
        $values = Mage::app()->getRequest()->getPost('adj');
        $fields = $this->getFields();
        $result = array();
        foreach ($fields as $field){
            $v = '';
            $formatted = '';
            if ($values && isset($values[$field->getId()])){
                $v         = $values[$field->getId()];
                $formatted = $v;
                if ($field->getInputFormat()){ // date type
                    $field->setValue($v, $field->getFormat());
                    $formatted = $field->getValue('yyyy-MM-dd');
                }
                $values[$field->getId()] = $formatted;
            } 

            $validatorName = $field->getValidator();
            
            if (!$validatorName)
                continue;
            
            $errors = Mage::getModel($validatorName)
                ->validate($field, $v); // pass original value to the validator
                
            if ($errors)
                $result = array_merge($result, $errors);
        }
        
        return $result;
    }

    public function multivalidate(){
        $values = Mage::app()->getRequest()->getPost('adj');
        $fields = $this->getFields();
        $result = array();
        foreach ($fields as $field)
        {
            $v = '';
            $formatted = '';

            if ($values && count($values))
            {
                foreach ($values as $key=>$val)
                {
                    if(!strpos($key,'date'))
                    {
                        continue;
                    }
                    $v = $val;
                    $formatted = $v;
                    if ($field->getInputFormat())
                    { // date type
                        $field->setValue($v, $field->getFormat());
                        $formatted = $field->getValue('yyyy-MM-dd');
                    }
                    $values[$key] = $formatted;

                    $validatorName = $field->getValidator();
                    if (!$validatorName)
                        continue;

                    $errors = Mage::getModel($validatorName)
                        ->validate($field, $v); // pass original value to the validator

                    if ($errors)
                        $result = array_merge($result, $errors);
                }
            }
        }

        return $result;
    }
    
    // use this function in Checkout_Type_Onepage
    public function process(){

        /* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('AdjustWare_Deliverydate')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $storeId = Mage::getSingleton('adminhtml/sales_order_create')->getSession()->getData('store_id');
        if (!($ruler->checkRule('store',$storeId,'store') ))
        {
            return $errors;
        }
        {#AITOC_COMMENT_START#} */
        
        if (!Mage::getStoreConfig('checkout/adjdeliverydate/enabled')){
            return $this->_errors;
        }
        
        try {
            $this->_errors = $this->validate();
        }
        catch (Exception $e){
            $this->_errors[] = $e->getMessage();
        }
        
        return $this->_checkErrors($this->_errors);
    }
    
    public function multiprocess(){

	/* {#AITOC_COMMENT_END#}
        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('AdjustWare_Deliverydate')->getLicense()->getPerformer();
        $ruler = $performer->getRuler();
        $storeId = Mage::getSingleton('adminhtml/sales_order_create')->getSession()->getData('store_id');
        if (!($ruler->checkRule('store',$storeId,'store') ))
        {
            return $errors;
        }
        {#AITOC_COMMENT_START#} */

        if (!Mage::getStoreConfig('checkout/adjdeliverydate/enabled')  || !Mage::getStoreConfig('checkout/adjdeliverydate/multienabled')){
            return $this->_errors;
        }
        
        try {
            $this->_errors = $this->multivalidate();
        }
        catch (Exception $e){
            $this->_errors[] = $e->getMessage();
        }

        return $this->_checkErrors($this->_errors);
    }

    protected function _checkErrors ($errors)
    {
        if ($errors){
            $errors = array(
                'error'   => -1,
                'message' => join("\n", $errors),
            );
        }
        
        return $errors;
    }
}