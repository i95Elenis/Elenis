<?php
class Elenis_VideoLink_Model_Mysql4_Videolink extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("videolink/videolink", "id");
    }
}