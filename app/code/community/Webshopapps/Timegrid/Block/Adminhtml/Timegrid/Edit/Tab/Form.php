<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Timegrid
 * User         Karen Baker
 * Date         2nd May 2013
 * Time         3pm
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */


class Webshopapps_Timegrid_Block_Adminhtml_Timegrid_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('timegrid_data');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('timegrid_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('catalogrule')->__('WebShopApps Date Shipping')));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if(Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Customcalendar')){
            $defaultPrice = Mage::helper('customcalendar')->getDefaultPrice();
            $type = 'text';
        } else {
            $defaultPrice = 0;
            $type = 'hidden';
        }

        $defaultSlots = Mage::helper('webshopapps_dateshiphelper')->getDefaultSlots();

        $fieldset->addField('week_commencing', 'date', array(
            'name' => 'week_commencing',
            'label' => Mage::helper('timegrid')->__('Week Commencing'),
            'title' => Mage::helper('timegrid')->__('Week Commencing'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
            'note' => 'Select MONDAY of week you wish to enter prices and slot values for. Leave empty to use as default price/slots',
        ));

        $fieldset->addField('time_slot_id', 'select', array(
            'label' => Mage::helper('timegrid')->__('Time Slot'),
            'required' => true,
            'name' => 'time_slot_id',
            'values' => Mage::helper('calendarbase')->getTimeSlots(),
            'note' => 'Time slots are configured under Matrixdays shipping methods',
        ));


        $fieldset->addField('1_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Monday Base Ship Price'),
            'name' => '1_price',
            'value' => $defaultPrice
        ));


        $fieldset->addField('2_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Tuesday Base Ship Price'),
            'name' => '2_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('3_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Wednesday Base Ship Price'),
            'name' => '3_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('4_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Thursday Base Ship Price'),
            'name' => '4_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('5_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Friday Base Ship Price'),
            'name' => '5_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('6_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Saturday Base Ship Price'),
            'name' => '6_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('7_price', $type, array(
            'label' => Mage::helper('timegrid')->__('Sunday Base Ship Price'),
            'name' => '7_price',
            'value' => $defaultPrice
        ));

        $fieldset->addField('1_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Monday Available Slots'),
            'name' => '1_slots',
            'value' => $defaultSlots
        ));


        $fieldset->addField('2_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Tuesday Available Slots'),
            'name' => '2_slots',
            'value' => $defaultSlots
        ));

        $fieldset->addField('3_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Wednesday Available Slots'),
            'name' => '3_slots',
            'value' => $defaultSlots
        ));

        $fieldset->addField('4_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Thursday Available Slots'),
            'name' => '4_slots',
            'value' => $defaultSlots
        ));

        $fieldset->addField('5_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Friday Available Slots'),
            'name' => '5_slots',
            'value' => $defaultSlots
        ));

        $fieldset->addField('6_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Saturday Available Slots'),
            'name' => '6_slots',
            'value' => $defaultSlots
        ));

        $fieldset->addField('7_slots', 'text', array(
            'label' => Mage::helper('timegrid')->__('Sunday Available Slots'),
            'name' => '7_slots',
            'value' => $defaultSlots,
            'note' => 'Set to -1 for infinite availability, 0 for no availability or a integer for custom availability'
        ));

        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}