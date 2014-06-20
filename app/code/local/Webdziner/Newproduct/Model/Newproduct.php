<?php

class Webdziner_Newproduct_Model_Newproduct extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('newproduct/newproduct');
    }
	public function getproids()
	{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result = $connection->query("select * from grid_newproduct order by position");
		while ($row = $result->fetch() ) {
		$ids[]=$row['product_id'];
		}
		return $ids;
	}
}