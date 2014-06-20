<?php

class Webdziner_Bgsetting_Model_Mysql4_Bgsetting extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bgsetting_id refers to the key field in your database table.
        $this->_init('bgsetting/bgsetting', 'bgsetting_id');
    }
}