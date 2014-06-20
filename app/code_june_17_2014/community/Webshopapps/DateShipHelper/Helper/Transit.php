<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_DateShipHelper
 * User         karen
 * Date         19/05/2013
 * Time         00:03
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_DateShipHelper_Helper_Transit extends Mage_Core_Helper_Abstract {

    /**
     *
     * Work out the maximum delivery days, use the shortest day found
     * @param $items
     * @return int|mixed|string
     */
    public function getMaxTransitDays($items) {
        $storeMaxTransitDays = Mage::getStoreConfig('shipping/webshopapps_dateshiphelper/max_delivery_days');

        $useParent = true;
        $shortestMaxTransitDays = -1;
        foreach($items as $item) {

            if ($item->getParentItem()!=null &&
                $useParent ) {
                // must be a bundle
                $product = $item->getParentItem()->getProduct();

            } else if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !$useParent ) {
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $product=$child->getProduct();
                        break;
                    }
                }
            } else {
                $product = $item->getProduct();
            }

            $maxProductTransitDays = $product->getData('max_transit');
            if (is_numeric($maxProductTransitDays) && ($shortestMaxTransitDays == -1 || $maxProductTransitDays<$shortestMaxTransitDays)) {
                $shortestMaxTransitDays = $maxProductTransitDays;
            }
        }
        if ($shortestMaxTransitDays == -1) {
            $shortestMaxTransitDays = $storeMaxTransitDays;
        }
        
        return $shortestMaxTransitDays;

    }

}
