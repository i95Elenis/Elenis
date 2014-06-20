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
class Aitoc_Aitcg_Helper_License extends Aitoc_Aitsys_Helper_License
{
    public function getRuleTotalCount($ruleCode)
    {
        switch ($ruleCode)
        {
            case 'product':
                $count = $this->getProductCount();
                break;
            default:
                $count = 0;
                break;
        }
        return $count;
    }
    
    public function getProductCount()
    {
        $product = new Mage_Catalog_Model_Product_Option();

        $collection = $product->getCollection()
            //->addFieldToFilter('type', array('eq'=>Aitoc_Aitcg_Model_Rewrite_Catalog_Product_Option::OPTION_TYPE_AITCUSTOMER_IMAGE));
            ->addFieldToFilter('type', array('eq'=>'aitcustomer_image'));            
        $collection->getSelect()->group('product_id');
        $collection->load();
        return $collection->count();
    }
    
}