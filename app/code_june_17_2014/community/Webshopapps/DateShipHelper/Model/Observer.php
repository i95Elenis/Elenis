<?php
/**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_DateShipHelper
 * User         karen
 * Date         23/05/2013
 * Time         11:55
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

class Webshopapps_DateShipHelper_Model_Observer extends Mage_Core_Model_Abstract {


    public function postError() { //TODO
        if (!Mage::helper('wsacommon')->checkItems('c2hpcHBpbmcvd2Vic2hvcGFwcHNfZGF0ZXNoaXBoZWxwZXIvc2hpcF9vbmNl',
            'Y2FmZWxhdHRl','c2hpcHBpbmcvd2Vic2hvcGFwcHNfZGF0ZXNoaXBoZWxwZXIvc2VyaWFs')) {
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIERhdGUgU2hpcHBpbmc=')));
        }
    }

}