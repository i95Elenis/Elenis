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
class AdjustWare_Deliverydate_Block_Adminhtml_Holiday_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form(array(
      'id' => 'edit_form',
      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
      'method' => 'post'));
    
    $form->setUseContainer(true);
    $this->setForm($form);
    $hlp = Mage::helper('adjdeliverydate');
    
    $fldInfo = $form->addFieldset('adjdeliverydate_info', array('legend'=> $hlp->__('Holiday')));
    
    $fldInfo->addField('title', 'text', array(
      'label'     => $hlp->__('Title'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'title',
    ));
    $fldInfo->addField('y', 'select', array(
      'label'     => $hlp->__('Year'),
      'options'   => $hlp->getYearOptions(), 
      'name'      => 'y',
    ));
    $fldInfo->addField('m', 'select', array(
      'label'     => $hlp->__('Month'),
      'options'   => $hlp->getMonthOptions(), 
      'name'      => 'm',
    ));
    $fldInfo->addField('d', 'select', array(
      'label'     => $hlp->__('Day'),
      'required'  => true,
      'options'   => $hlp->getDayOptions(), 
      'class'     => 'required-entry',
      'name'      => 'd',
    ));
      
    //set form values
    $data = Mage::getSingleton('adminhtml/session')->getFormData();
    $model = Mage::registry('holiday_data');
    if ($data) {
        $form->setValues($data);
        Mage::getSingleton('adminhtml/session')->setFormData(null);
    }
    elseif ($model) {
        $form->setValues($model->getData());
    } 
    
    return parent::_prepareForm();
  }
}