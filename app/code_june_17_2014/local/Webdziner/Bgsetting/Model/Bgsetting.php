<?php

class Webdziner_Bgsetting_Model_Bgsetting extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bgsetting/bgsetting');
    }
}