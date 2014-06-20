<?php
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     AJifvLXz2Jhov40GDpSzkNqfs4dkmEPJtRhHJxJI2y
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcg_Block_Adminhtml_Tips extends Mage_Core_Block_Abstract
    {
        public function __construct()
        {
            /*$this->_controller = 'adminhtml_tips';
            $this->_blockGroup = 'aitcg';
            $this->_headerText = Mage::helper('aitcg')->__('Easy Tips');
            $this->_addButtonLabel = Mage::helper('aitcg')->__('Add Color Set');*/
            
            $this->setTemplate('aitcg/tips/cgfile.phtml');
            parent::__construct();
        }

        
}