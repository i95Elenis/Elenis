<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Timegrid
 * User         Karen Baker
 * Date         2nd May 2013
 * Time         3pm
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */


class Webshopapps_Timegrid_Block_Adminhtml_Timegrid_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('timegrid_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('timegrid')->__('WebShopApps Delivery Prices'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('timegrid')->__('Weekly Delivery Slots & Prices'),
          'title'     => Mage::helper('timegrid')->__('Weekly Delivery Slots & Prices'),
          'content'   => $this->getLayout()->createBlock('timegrid/adminhtml_timegrid_edit_tab_form')->toHtml(),
      ));

      $this->addTab('dispatch_section', array(
          'label'     => Mage::helper('timegrid')->__('Weekly Dispatch Slots'),
          'title'     => Mage::helper('timegrid')->__('Weekly Dispatch Slots'),
          'content'   => $this->getLayout()->createBlock('timegrid/adminhtml_timegrid_edit_tab_dispatch')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}