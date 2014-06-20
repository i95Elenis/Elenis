<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/21/13
 * Time   : 8:50 PM
 * File   : Expiry.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Widget_Grid_Column_Renderer_Expiry extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {
        $format = ( $this->getColumn()->getFormat() ) ? $this->getColumn()->getFormat() : null;
        $defaultValue = $this->getColumn()->getDefault();

        // If no format and it column not filtered specified return data as is.
        $data = parent::_getValue($row);
        $string = is_null($data) ? $defaultValue : $data;

        $string = $this->helper('ebizmarts_sagepaymentspro')->getCardNiceDate($string);

        return htmlspecialchars($string);
    }
}