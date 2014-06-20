<?php
class Elenis_OrderGrid_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareColumns()
    {
       $this->addColumnAfter('action1',
            array(
                'header'    => Mage::helper('sales')->__('Netdot File'),
                'width'     => '50px',
                'type'      => 'action',
                 'align' => 'center',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('Export'),
                        'url'     => Mage::getBaseUrl()."Netdot-Export.php?order_id=",
                        'field'   => 'order_id',
                    )
                ),
               
                'renderer' => 'Elenis_OrderGrid_Block_Adminhtml_Sales_Order_Renderer_NetdotValues',
       
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'action1',
                'is_system' => true,
        ),'action');
          return parent::_prepareColumns();
    }
}
			