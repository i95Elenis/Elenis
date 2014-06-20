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

class FME_Quickrfq_Block_Adminhtml_Quickrfq_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('quickrfq_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('quickrfq')->__('Qoute Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('quickrfq')->__('senders Information'),
          'title'     => Mage::helper('quickrfq')->__('senders Information'),
          'content'   => $this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_edit_tab_form')->toHtml(),
      ));
      //$id  = $this->getRequest()->getParam('id');
      //
      //    if($id !=0) {
      //$this->addTab('attachment_section', array(
      //    'label'     => Mage::helper('quickrfq')->__('Attachments'),
      //    'title'     => Mage::helper('quickrfq')->__('Attachments'),
      //    'content'   => $this->getLayout()->createBlock('quickrfq/adminhtml_quickrfq_edit_tab_attachment')->toHtml(),
      //));
	  ///}
     
      return parent::_beforeToHtml();
  }
}