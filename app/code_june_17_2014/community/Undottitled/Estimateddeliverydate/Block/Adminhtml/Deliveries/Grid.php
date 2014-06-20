<?php
 
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
		parent::__construct();
        $this->setId('deliveries_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('estimateddeliverydate/deliveries')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('estimateddeliverydate')->__('ID'),
            'width'     => '50px',
            'index'     => 'id',
            'type'  => 'number',
        ));
        
        $this->addColumn('pid', array(
            'header'    => Mage::helper('estimateddeliverydate')->__('Product ID'),
            'width'     => '50px',
            'index'     => 'pid',
            'type'  => 'number',
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('estimateddeliverydate')->__('SKU'),
            'index'     => 'sku',
            'type'  => 'text'
        ));
        
        $this->addColumn('date', array(
            'header'    => Mage::helper('estimateddeliverydate')->__('Delivery Date'),
            'index'     => 'date',
            'type'  => 'date',
        ));
        
        $this->addColumn('qty', array(
            'header'    => Mage::helper('estimateddeliverydate')->__('Quantity Due'),
            'index'     => 'qty',
            'type'  => 'number',
        ));
				
		$this->addColumn('status', array(
 
            'header'    => Mage::helper('estimateddeliverydate')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Delivered',
                0 => 'Pending',
            ),
        ));
 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}