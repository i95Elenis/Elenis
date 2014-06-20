<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/14/13
 * Time   : 2:50 PM
 * File   : Transactions.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_Sales_Order_View_Tab_Transactions
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sagepayments_transactions_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('transaction_date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollection()
    {
        $collection = Mage::getModel('ebizmarts_sagepaymentspro/transaction')->getCollection();

        $order = Mage::registry('current_order');
        $orderParam = $this->getRequest()->getParam('order_id');

        if ($order) {
            $collection->addFieldToFilter('order_id', $order->getId());
        }else if($orderParam){
            $collection->addFieldToFilter('order_id', $orderParam);
        }

        return $collection;
    }

    /**
     * Prepare related orders collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('type', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Type'),
            'type'  => 'text',
            'index' => 'type',
            'filter' => false,
            'sortable' => false,
        ));
        $this->addColumn('trn_securitykey', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Security Key'),
            'type'  => 'text',
            'filter' => false,
            'sortable' => false,
            'index' => 'trn_securitykey',
        ));
        $this->addColumn('response_status', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Status'),
            'type'  => 'text',
            'filter' => false,
            'sortable' => false,
            'index' => 'response_status',
        ));
        $this->addColumn('response_status_detail', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Status Detail'),
            'type'  => 'text',
            'filter' => false,
            'sortable' => false,
            'index' => 'response_status_detail',
        ));
        $this->addColumn('amount', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Amount'),
            'type'  => 'text',
            'filter' => false,
            'sortable' => false,
            'index' => 'amount',
        ));
        $this->addColumn('transaction_date', array(
            'header'=> Mage::helper('ebizmarts_sagepaymentspro')->__('Date'),
            'type'  => 'datetime',
            'filter' => false,
            'sortable' => false,
            'index' => 'transaction_date',
        ));
        return parent::_prepareColumns();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('SagePayments Transactions');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('SagePayments Transactions');
    }

    /**
     * Return row url for js event handlers
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    public function getGridUrl($params = array())
    {
        return $this->getAbsoluteGridUrl($params);
    }

    public function getAbsoluteGridUrl($params = array())
    {
        return null;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        try{
            $regOrder = Mage::registry('current_order');
            if($regOrder && $regOrder->getPayment()->getMethod() != 'sagepaymentspro'
                || ($this->_getCollection()->getSize() == 0)){
                return true;
            }
        }catch(Exception $ee){
            return false;
        }
        return false;
    }

}