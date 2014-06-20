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
class FME_Quickrfq_Block_Adminhtml_Quickrfq extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quickrfq';
    $this->_blockGroup = 'quickrfq';
    $this->_headerText = Mage::helper('quickrfq')->__('Order Form Manager');
    //$this->_addButtonLabel = Mage::helper('quickrfq')->__('Add New RFQ');
   
     parent::__construct();
                
                $this->_removeButton('add');
  }
}