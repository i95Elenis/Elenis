<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/21/13
 * Time   : 8:36 PM
 * File   : CustomerId.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Widget_Grid_Column_Renderer_CustomerId extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
        $result = parent::render($row);

        $customer = Mage::getModel('customer/customer')->load($row->getCustomerId());

        if($customer->getId()) {
            $href   = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $customer->getId()));
            $result = '<a href="' . $href . '" target="_blank">' . $customer->getName() . '</a>';
        }

        return $result;
    }

}