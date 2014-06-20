<?php 
class Webdziner_Newproduct_Model_Grid extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('newproduct/grid');
	}
	
	public function isNewProduct($productId)
	{
		$newCollection=Mage::getModel('newproduct/grid')->getCollection();
		foreach($newCollection as $new)
		{
			if($new->getProductId() == $productId)
				return true;
		}

		return false;
	}
}