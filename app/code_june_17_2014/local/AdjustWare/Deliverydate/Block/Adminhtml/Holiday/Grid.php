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
class AdjustWare_Deliverydate_Block_Adminhtml_Holiday_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('holidayGrid');
      $this->setDefaultSort('holiday_id');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('adjdeliverydate/holiday')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $hlp =  Mage::helper('adjdeliverydate'); 
    $this->addColumn('holiday_id', array(
      'header'    => $hlp->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'holiday_id',
    ));
    $this->addColumn('title', array(
        'header'    => $hlp->__('Title'),
        'index'     => 'title',
    ));
    $this->addColumn('y', array(
        'header'    => $hlp->__('Year'),
        'index'     => 'y',
        'type'      => 'options',
        'options'   => $hlp->getYearOptions(),
    ));
    $this->addColumn('m', array(
        'header'    => $hlp->__('Month'),
        'index'     => 'm',
        'type'      => 'options',
        'options'   => $hlp->getMonthOptions(),
    ));
    $this->addColumn('d', array(
        'header'    => $hlp->__('Day'),
        'index'     => 'd',
        'type'      => 'options',
        'options'   => $hlp->getDayOptions(),
    ));

    $this->addColumn('action', array(
        'header'    => $hlp->__('Action'),
        'width'     => '50px',
        'align'     => 'center',
        'type'      => 'action',
        'getter'     => 'getId',
        'actions'   => array(
            array(
                'caption' => $hlp->__('View'),
                'url'     => array('base'=>'*/*/edit'),
                'field'   => 'id'
            )
        ),
        'filter'    => false,
        'sortable'  => false,
        'index'     => 'id',
        //'is_system' => true,
    ));

    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  protected function _prepareMassaction(){
    $this->setMassactionIdField('holiday_id');
    $this->getMassactionBlock()->setFormFieldName('holiday');
    
    $this->getMassactionBlock()->addItem('delete', array(
         'label'    => Mage::helper('adjdeliverydate')->__('Delete'),
         'url'      => $this->getUrl('*/*/massDelete'),
         'confirm'  => Mage::helper('adjdeliverydate')->__('Are you sure?')
    ));
    
    return $this; 
  }

}