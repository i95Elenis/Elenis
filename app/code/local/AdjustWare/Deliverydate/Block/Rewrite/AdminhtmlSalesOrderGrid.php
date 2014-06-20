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
class AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setCollection($collection){
        $collection->joinAttribute('delivery_date', 'order/delivery_date', 'entity_id', null, 'left');
        $this->_collection = $collection;    
    }

    protected function _prepareColumns()
    {
        $res = parent::_prepareColumns();

        $action = $this->_columns['action'];
        unset($this->_columns['action']);
        
        $this->addColumn('delivery_date', array(
            'header' => Mage::helper('adjdeliverydate')->__('Delivery Date'),
            'index'  => 'delivery_date',
            //'type'   => 'date',
            'renderer' => 'adminhtml/widget_grid_column_renderer_date',
            'filter' => 'adjdeliverydate/adminhtml_filter_delivery', //AdjustWare_Deliverydate_Block_Adminhtml_Filter_Delivery
            'width'  => '100px', 
        ));
        
        $this->_columns['action'] = $action;
        $this->_columns['action']->setId('action');
        $this->_lastColumnId = 'action';
 
        
        return $res;
    }

}