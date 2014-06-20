<?php
/**
 * Manufacturers extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Manufacturers
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Quickrfq_Block_Adminhtml_Quickrfq_Edit_Tab_Attachment extends Mage_Adminhtml_Block_Widget_Form
{
    
	
	//const EXPLAIN_TEMPLATE = 'quickrfq/attachment.phtml';
	
	//protected function _construct()
	//{
	//	parent::_construct();
	//	$this->setTemplate( self::EXPLAIN_TEMPLATE );
	//}
	//
	//public function getManufacturerProducts()
	//{
	//	$id  = $this->getRequest()->getParam('id');
	//	$HTML = "";
	//	if ($id != 0) {
	//		$products = Mage::getModel('catalog/product')->getCollection();
	//		$products->addAttributeToFilter('product_manufacturer', array('in' => array($id)));
	//		$products->addAttributeToSelect('*');
	//		$products->load();     
	//		return 	$products;
	//	} else {
	//		$products = array();
	//		return 	$products;
	//	}
	//}
	protected function _prepareForm()
	{ $form1 = new Varien_Data_Form();
	  $this->setForm($form1);
	  $fieldset1 = $form1->addFieldset('quickrfq_form', array('legend'=>Mage::helper('quickrfq')->__('Attached File')));
	     
	     $fieldset1->addField('prd', 'text', array(
	      'label'     => Mage::helper('quickrfq')->__('Attached File'),
	      'class'     => 'required-entry',
	      'required'  => true,
	      'name'      => 'prd',
	  ));
	     
	      if ( Mage::getSingleton('adminhtml/session')->getQuickrfqData() )
      {
          $form1->setValues(Mage::getSingleton('adminhtml/session')->getQuickrfqData());
          Mage::getSingleton('adminhtml/session')->setQuickrfqData(null);
      } elseif ( Mage::registry('quickrfq_data') ) {
          $form1->setValues(Mage::registry('quickrfq_data')->getData());
      }
      return parent::_prepareForm();
	    
	}
}