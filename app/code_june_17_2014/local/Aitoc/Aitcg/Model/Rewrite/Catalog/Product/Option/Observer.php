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
class Aitoc_Aitcg_Model_Rewrite_Catalog_Product_Option_Observer extends Mage_Catalog_Model_Product_Option_Observer
{
    /**
     * Copy quote custom option files to order custom option files
     *
     * @param Varien_Object $observer
     * @return Mage_Catalog_Model_Product_Option_Observer
     */
    public function copyQuoteFilesToOrderFiles($observer)
    {
        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        $quoteItem = $observer->getEvent()->getItem();

        if (is_array($quoteItem->getOptions())) {
            foreach ($quoteItem->getOptions() as $itemOption) {
                $code = explode('_', $itemOption->getCode());
                if (isset($code[1]) && is_numeric($code[1]) && ($option = $quoteItem->getProduct()->getOptionById($code[1]))) {
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE ||
                        Mage::helper('aitcg/options')->checkAitOption( $option )
                        //$option->getType() == Aitoc_Aitcg_Model_Rewrite_Option::OPTION_TYPE_AITCUSTOMER_IMAGE
                       ) {
                        /* @var $_option Mage_Catalog_Model_Product_Option */
                        try {
                            $group = $option->groupFactory($option->getType())
                                ->setQuoteItemOption($itemOption)
                                ->copyQuoteToOrder();

                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }
        return $this;
    }
}