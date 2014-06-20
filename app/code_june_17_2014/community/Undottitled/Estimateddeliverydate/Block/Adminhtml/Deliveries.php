<?php
 
class Undottitled_Estimateddeliverydate_Block_Adminhtml_Deliveries extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected $_addButtonLabel = 'Add New Delivery';
	
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_deliveries';
        $this->_blockGroup = 'estimateddeliverydate';
        $this->_headerText = Mage::helper('estimateddeliverydate')->__('Product Delivery Management');
    }


	protected function _prepareLayout()
	{
		$this->setChild( 'grid',
		   $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
		   $this->_controller . '.grid')->setSaveParametersInSession(true) );
	   return parent::_prepareLayout();
	}
}