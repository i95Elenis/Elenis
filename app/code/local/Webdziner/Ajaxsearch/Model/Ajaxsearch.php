<?php

class Webdziner_Ajaxsearch_Model_Ajaxsearch extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxsearch/ajaxsearch');
    }
}