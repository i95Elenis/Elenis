<?php

/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_$(PROJECT_NAME)
 * User         joshstewart
 * Date         05/06/2013
 * Time         12:43
 * @copyright   Copyright (c) $(YEAR) Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, $(YEAR), Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */
class Webshopapps_Timegrid_Block_Adminhtml_Timegrid_Edit_Tab_Dispatch extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('timegrid_data'); //todo change me to unique

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('dispatch_');

        $fieldset = $form->addFieldset('dispatch_fieldset', array('legend' => Mage::helper('timegrid')->__('WebShopApps Date Shipping')));

        $defaultSlots = Mage::helper('webshopapps_dateshiphelper')->getDefaultSlots();

        $fieldset->addField('1_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Monday Dispatch Slots'),
            'name' => '1_dispatch',
            'value' => $defaultSlots
        ));


        $fieldset->addField('2_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Tuesday Dispatch Slots'),
            'name' => '2_dispatch',
            'value' => $defaultSlots
        ));

        $fieldset->addField('3_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Wednesday Dispatch Slots'),
            'name' => '3_dispatch',
            'value' => $defaultSlots
        ));

        $fieldset->addField('4_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Thursday Dispatch Slots'),
            'name' => '4_dispatch',
            'value' => $defaultSlots
        ));

        $fieldset->addField('5_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Friday Dispatch Slots'),
            'name' => '5_dispatch',
            'value' => $defaultSlots
        ));

        $fieldset->addField('6_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Saturday Dispatch Slots'),
            'name' => '6_dispatch',
            'value' => $defaultSlots
        ));

        $fieldset->addField('7_dispatch', 'text', array(
            'label' => Mage::helper('timegrid')->__('Sunday Dispatch Slots'),
            'name' => '7_dispatch',
            'value' => $defaultSlots,
            'note' => 'Set to -1 for infinite availability, 0 for no availability or a integer for custom availability'
        ));

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}