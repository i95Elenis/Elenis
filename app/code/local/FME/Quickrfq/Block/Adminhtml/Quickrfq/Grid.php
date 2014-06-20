<?php

 /**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Quickrfq_Block_Adminhtml_Quickrfq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('quickrfqGrid');
      $this->setDefaultSort('quickrfq_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('quickrfq/quickrfq')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('quickrfq_id', array(
          'header'    => Mage::helper('quickrfq')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'quickrfq_id',
      ));

     $this->addColumn('company', array(
          'header'    => Mage::helper('quickrfq')->__('Fisrt Name'),
          'align'     =>'left',
          'index'     => 'company',
      ));

      $this->addColumn('contact_name', array(
          'header'    => Mage::helper('quickrfq')->__('Last Name'),
          'align'     =>'left',
          'index'     => 'contact_name',
      ));
      
      
      
       $this->addColumn('phone', array(
          'header'    => Mage::helper('quickrfq')->__('Phone'),
          'align'     =>'left',
          'index'     => 'phone',
      ));
	  
       $this->addColumn('email', array(
          'header'    => Mage::helper('quickrfq')->__('Email Address'),
          'align'     =>'left',
          'index'     => 'email',
      ));
      $this->addColumn('status', array(
          'header'    => Mage::helper('quickrfq')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
             
              'No' => 'New',
	      'process' => 'Under Process',
              'pending' => 'Pending',
	      'Yes' => 'Done',
          ),
      ));
      
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('quickrfq')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('quickrfq')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('quickrfq')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('quickrfq')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('quickrfq_id');
        $this->getMassactionBlock()->setFormFieldName('quickrfq');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('quickrfq')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('quickrfq')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('quickrfq/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('quickrfq')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('quickrfq')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}