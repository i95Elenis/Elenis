<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 7/3/13
 * Time   : 4:32 PM
 * File   : Add.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Token_Add extends Mage_Adminhtml_Block_Widget_Form_Container {
    protected $_mode = 'add';
    public function __construct() {
        $this->_controller = 'adminhtml_token';
        $this->_blockGroup = 'ebizmarts_sagepaymentspro';

        parent::__construct();
        $this->_removeButton("delete");
        $this->_removeButton("back");
        $this->_removeButton("reset");
    }
    public function getHeaderText()
    {
        return Mage::helper('ebizmarts_sagepaymentspro')->__('Add new token');
    }
    public function _prepareForm() {

    }
}