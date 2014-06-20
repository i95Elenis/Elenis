<?php
class Elenis_OrderGrid_Block_Adminhtml_Sales_Order_Renderer_NetdotValues extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        
         $value =  $row->getData('entity_id');
        return '<a href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."Netdot-Export.php?order_id=".base64_encode($value).'">Export</a>';
    }
}
			