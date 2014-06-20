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
 * Description of ajaxController
 *
 * @author lyskovets
 */
class AdjustWare_Deliverydate_Adminhtml_AjaxController
    extends Mage_Adminhtml_Controller_Action
{
    public function dateValidateAction()
    {
        $result = $this->_getValidator();
        if (isset($result['error']))
        {
            $result['valid_date'] = $this->_getValidDate();
        }
        $this->_sendResponse($result);   
    }
    
    private function _getValidDate()
    {
        $format = Mage::getStoreConfig('checkout/adjdeliverydate/format');
        $date = Mage::getModel('adjdeliverydate/holiday')->getFirstAvailableDate('unix');
        $dateFormer = new Zend_Date($date);
        $formatedDate = $dateFormer ->toString($format);
        return $formatedDate;
    }
    
    protected function _sendResponse($data)
    {
        $ajaxResponse = Mage::helper('core')->jsonEncode($data);
        $this->getResponse()->setBody($ajaxResponse);
    }
    
    private function _getValidator()
    {
        return Mage::getModel('adjdeliverydate/step')->process('shippingMethod');
    }
    
}