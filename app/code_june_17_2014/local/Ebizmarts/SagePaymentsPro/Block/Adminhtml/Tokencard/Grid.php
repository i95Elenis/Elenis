<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/21/13
 * Time   : 7:46 PM
 * File   : Grid.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Tokencard_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _getNewTokenUrl()
    {
        return $this->getUrl('adminhtml/token/add/new', array('customer_id' => Mage::registry('current_customer')->getId(),'popup'=>1));
    }

    public function __construct() {
        parent::__construct();
        $this->setId('token_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('ebizmarts_sagepaymentspro/tokencard')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('id', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Card #'),
            'width' => '80px',
            'type' => 'number',
            'index' => 'id'
        ));
        $this->addColumn('customer2_id', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Customer #'),
            'index' => 'customer_id',
            'type' => 'text',
            'width' => '100px',
            'renderer' => 'ebizmarts_sagepaymentspro/adminhtml_widget_grid_column_renderer_customerId'
        ));

        $this->addColumn('token', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Token'),
            'index' => 'token',
            'type' => 'text',
            'width' => '100px'
        ));

////        $ccCards = Mage::getModel('ebizmarts_sagepaymentspro/sagepaysuite_source_creditCards')->toOption();
        $this->addColumn('card_type', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Card Type'),
            'index' => 'card_type',
            'type' => 'text',
            'width' => '100px',
//            'options' => $ccCards
        ));

        $this->addColumn('last_four', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Last 4 Digits'),
            'index' => 'last_four',
            'type' => 'text',
            'width' => '100px'
        ));

        $this->addColumn('expiry_date', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Expiry Date'),
            'index' => 'expiry_date',
            'type' => 'text',
            'width' => '100px',
            'renderer' => 'ebizmarts_sagepaymentspro/adminhtml_widget_grid_column_renderer_expiry'
        ));

        $this->addColumn('visitor_session_id', array(
            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Visitor Session ID'),
            'index' => 'visitor_session_id',
            'width' => '60px'
        ));

//        $this->addColumn('action', array(
//            'header' => Mage::helper('ebizmarts_sagepaymentspro')->__('Action'),
//            'width' => '50px',
//            'type' => 'action',
//            'align' => 'center',
//            'getter' => 'getId',
//            'actions' => array(
//                array(
//                    'caption' => Mage::helper('ebizmarts_sagepaymentspro')->__('Delete'),
//                    'url' => array('base' => 'adminhtml/token/delete'),
//                    'field' => 'id',
//                    'confirm' => Mage::helper('ebizmarts_sagepaymentspro')->__('Are you sure?')
//                )
//            ),
//            'filter' => false,
//            'sortable' => false,
//            'is_system' => true,
//        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ebizmarts_sagepaymentspro/adminhtml_tokencard_grid')->toHtml()
        );
    }
    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('cards');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('delete_tokens', array(
            'label' => Mage::helper('ebizmarts_sagepaymentspro')->__('Delete'),
            'url' => $this->getUrl('adminhtml/token/massDelete'),
            'confirm' => Mage::helper('ebizmarts_sagepaymentspro')->__('Are you sure?')
        ));

        return $this;
    }

}