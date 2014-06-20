<?php

class Webdziner_Ajaxsearch_Model_Mysql4_Ajaxsearch extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ajaxsearch_id refers to the key field in your database table.
        $this->_init('ajaxsearch/ajaxsearch', 'ajaxsearch_id');
    }
}