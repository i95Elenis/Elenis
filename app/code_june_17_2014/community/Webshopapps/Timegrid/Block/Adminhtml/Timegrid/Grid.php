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


class Webshopapps_Timegrid_Block_Adminhtml_Timegrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('timegridGrid');
        $this->setDefaultSort('timegrid_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $count = 0;
        $collection = Mage::getModel('timegrid/timegrid')->getCollection();
        foreach ($collection->getItems() as $timeSlot) {
            if ($timeSlot->getWeekCommencing() == '0000-00-00' || $timeSlot->getWeekCommencing() == '00-00-0000') {
                $timeSlot->setWeekCommencing('Default');
                $count++;
                if ($count > 3) {
                    break;
                }
            }

        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('week_commencing', array(
            'header' => Mage::helper('timegrid')->__('Week Commencing'),
            'align' => 'left',
            'index' => 'week_commencing',
        ));

        $this->addColumn('time_slot_id', array(
            'header' => Mage::helper('timegrid')->__('Time Slot'),
            'align' => 'left',
            'index' => 'time_slot_id',
            'type' => 'options',
            'options' => Mage::helper('calendarbase')->getTimeSlotOptions(),
        ));


        $this->addColumn('1_slots', array(
            'header' => Mage::helper('timegrid')->__('Monday Slots'),
            'align' => 'left',
            'index' => '1_slots',
        ));


        $this->addColumn('2_slots', array(
            'header' => Mage::helper('timegrid')->__('Tuesday Slots'),
            'align' => 'left',
            'index' => '2_slots',
        ));

        $this->addColumn('3_slots', array(
            'header' => Mage::helper('timegrid')->__('Wednesday Slots'),
            'align' => 'left',
            'index' => '3_slots',
        ));

        $this->addColumn('4_slots', array(
            'header' => Mage::helper('timegrid')->__('Thursday Slots'),
            'align' => 'left',
            'index' => '4_slots',
        ));

        $this->addColumn('5_slots', array(
            'header' => Mage::helper('timegrid')->__('Friday Slots'),
            'align' => 'left',
            'index' => '5_slots',
        ));

        $this->addColumn('6_slots', array(
            'header' => Mage::helper('timegrid')->__('Saturday Slots'),
            'align' => 'left',
            'index' => '6_slots',
        ));

        $this->addColumn('7_slots', array(
            'header' => Mage::helper('timegrid')->__('Sunday Slots'),
            'align' => 'left',
            'index' => '7_slots',
        ));


        $this->addColumn('action',
            array(
                'header' => Mage::helper('timegrid')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('timegrid')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('timegrid_id');
        $this->getMassactionBlock()->setFormFieldName('timegrid');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('timegrid')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('timegrid')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('timegrid/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}