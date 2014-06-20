<?php

 /**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
class FME_Quickrfq_Block_Adminhtml_Quickrfq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
     $id  = $this->getRequest()->getParam('id');
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('quickrfq_form', array('legend'=>Mage::helper('quickrfq')->__('Senders information')));
     /* get the status of this rfq */
      $tableName=Mage::getSingleton('core/resource')->getTableName('quickrfq/quickrfq');
      
      $getstatus_query="SELECT status FROM $tableName where quickrfq_id=$id";
    
	$status_data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($getstatus_query);
	$status=$status_data[0]['status'];
	if($status=='No')
	{
	  /* this variable will set the saved status of this rfq */
	  $origin="New";
	}
	else if($status=='process')
	{
	  $origin="Under Process";
	}
	else if($status=='pending')
	{
	  $origin="Pending";
	}
	else 
	{
	  $origin="Done";
	}
	/* display the status of this rfq */
     $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('quickrfq')->__('Change Status '),
          'name'      => 'status',
	  'values'    => array(
             array(
                  'value'     => 'No',
                  'label'     => Mage::helper('quickrfq')->__('New'),
              ),
	       array(
                  'value'     => 'process',
                  'label'     => Mage::helper('quickrfq')->__('Under Process'),
              ),

              array(
                  'value'     => 'pending',
                  'label'     => Mage::helper('quickrfq')->__('Pending'),
              ),
	      array(
                  'value'     => 'Yes',
                  'label'     => Mage::helper('quickrfq')->__('Done'),
              ),
	      
	      
	    ),
	  'after_element_html' => '<small>' . Mage::helper('quickrfq')->__('    ......Originally It was ') . '</small><span Style="text-decoration: none;">'. $origin .'</span>',
      ));
     /* display the date of creation of this rfq */
     $fieldset->addField('create_date', 'label', array(
          'label'     => Mage::helper('quickrfq')->__('Order Form requested date: '),
         
          'required'  => false,
          'name'      => 'create_date',
	  'after_element_html' => '<small>' . Mage::helper('quickrfq')->__('    ......YYYY-MM-DD') . '</small>',
      ));
     /* display the date of last update of this rfq */
     $fieldset->addField('update_date', 'label', array(
          'label'     => Mage::helper('quickrfq')->__('Order Form updated date: '),
         
          'required'  => false,
          'name'      => 'update_date',
	  'after_element_html' => '<small>' . Mage::helper('quickrfq')->__('    ......YYYY-MM-DD') . '</small>',
      ));
     /* display the company name of this rfq */
      $fieldset->addField('company', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('First Name: '),
         
          'required'  => false,
          'name'      => 'company',
      ));
      /* display the date of Person's name of this rfq */
      $fieldset->addField('contact_name', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Last Name: '),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'contact_name',
      ));
      /* display the Person's Phone No. of this rfq */
      $fieldset->addField('phone', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Phone Number'),
         
          'required'  => false,
          'name'      => 'phone',
      ));
       $fieldset->addField('prefered_methods', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Prefered Method: '),
         
          'required'  => false,
          'name'      => 'prefered_methods',
      ));
        $fieldset->addField('occasion', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Occasion or Theme: '),
         
          'required'  => false, 
          'name'      => 'occasion',
      ));
       /* get and display the Person's email of this rfq */
	$getemail_query="SELECT email FROM $tableName where quickrfq_id=$id";
	$email_data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($getemail_query);
	$email_address=$email_data[0]['email'];
	
	$fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Email Address'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
	  'after_element_html' => '<a href="mailto:' . $email_address . '" Style="text-decoration: none;"><small>' . Mage::helper('quickrfq')->__('		.....(Send Email)  ') . '</small></a>',
      ));
	/* display the date quote needed by the customer */
        $fieldset->addField('date', 'label', array(
          'label'     => Mage::helper('quickrfq')->__('Date Quote Needed by the Client: '),
          'required'  => false,
          'name'      => 'date',
	  'after_element_html' => '<small>' . Mage::helper('quickrfq')->__('    ......YYYY-MM-DD') . '</small>',
      ));
	 /* display the project title of this rfq */
        $fieldset->addField('project_title', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('Number of Cookies or Cupcakes: '),
          
          'required'  => false,
          'name'      => 'project_title',
      ));
	
 /* get and display the uploads of this rfq */
 $getupload_query="SELECT prd FROM $tableName where quickrfq_id=$id";
 
 $file_data = Mage::getSingleton('core/resource') ->getConnection('core_read')->fetchAll($getupload_query);
 $file_name=$file_data[0]['prd'];
  $filePath = Mage::getBaseUrl('media') . 'quickrfq' . DS . $file_name;
 if (!empty($file_name)) 
 {
  
  $fieldset->addField('prd', 'label', array(
          'label'     => Mage::helper('quickrfq')->__('Attached File: '),
          'required'  => false,
          'name'      => 'prd',
	  'after_element_html' => '<a href="' . $filePath . '" Style="text-decoration: none;"><small>' . Mage::helper('quickrfq')->__('        .....(Open File)  ') . '</small></a>',
      ));
  
 }
  /* display the budget of this rfq */
        $fieldset->addField('budget', 'text', array(
          'label'     => Mage::helper('quickrfq')->__('These Cookies or Cupcakes being used for: '),
          'name'      => 'budget',
         /* 'values'    => array(
              array(
                  'value'     => 'Serve at an event',
                  'label'     => Mage::helper('quickrfq')->__('Serve at an event'),
              ),

              array(
                  'value'     => 'Party Favors',
                  'label'     => Mage::helper('quickrfq')->__('Party Favors'),
              ),
	      
	       array(
                  'value'     => 'Gift',
                  'label'     => Mage::helper('quickrfq')->__('Gift'),
              ),
	       
	        array(
                  'value'     => 'Promotinal Material',
                  'label'     => Mage::helper('quickrfq')->__('Promotinal Material'),
              ),
          ),
           
          */
      ));
      /* display the overview of this rfq */
      $fieldset->addField('overview', 'editor', array(
          'name'      => 'overview',
          'label'     => Mage::helper('quickrfq')->__('Brief Overview: '),
          'title'     => Mage::helper('quickrfq')->__('Overview'),
          'style'     => 'width:600px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getQuickrfqData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getQuickrfqData());
          Mage::getSingleton('adminhtml/session')->setQuickrfqData(null);
      } elseif ( Mage::registry('quickrfq_data') ) {
          $form->setValues(Mage::registry('quickrfq_data')->getData());
      }
      return parent::_prepareForm();
  }
}