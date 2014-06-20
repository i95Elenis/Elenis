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
class Aitoc_Aitcg_Helper_Data extends Aitoc_Aitcg_Helper_Abstract
{
    const MODULE_NAME = 'Aitoc_Aitcg';
    
    public function isRequirePreview()
    {
        return Mage::getStoreConfig('catalog/aitcg/aitcg_preview_is_required');
    }
    
    public function recursiveDelete($str)
    {
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                $this->recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
    
    public function uniqueFilename($strExt = '.tmp') {
            // explode the IP of the remote client into four parts
            $arrIp = explode('.', $_SERVER['REMOTE_ADDR']);
            // get both seconds and microseconds parts of the time
            list($usec, $sec) = explode(' ', microtime());
            // fudge the time we just got to create two 16 bit words
            $usec = (integer) ($usec * 65536);
            $sec = ((integer) $sec) & 0xFFFF;
            // fun bit--convert the remote client's IP into a 32 bit
            // hex number then tag on the time.
            // Result of this operation looks like this xxxxxxxx-xxxx-xxxx
            $strUid = sprintf("%08x-%04x-%04x", ($arrIp[0] << 24) | ($arrIp[1] << 16) | ($arrIp[2] << 8) | $arrIp[3], $sec, $usec);
            // tack on the extension and return the filename
            return $strUid . $strExt;
    }
    
    final public function getModuleName()
    {
        return self::MODULE_NAME;
    }

    public function getSecureUnsecureUrl($jsonData)
    {
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, Mage::app()->getStore()->isCurrentlySecure());
        $allStores = Mage::app()->getStores();
        $urlForReplace = array();
        foreach ($allStores as $storeId => $val)
        {
            $urlForReplace[] = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, false);
            $baseUrlStoreSecure = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
            $urlForReplace[] = $baseUrlStoreSecure;
            $urlForReplace[] = str_replace('https:','http:', $baseUrlStoreSecure);
        }
        $urlForReplace = array_unique($urlForReplace);
        foreach($urlForReplace as $key => $value)
        {
            if(strlen(trim($value)) < 5)//validation that url is real
            unset($urlForReplace[$key]);
        }
        $jsonData = str_replace($urlForReplace, $baseUrl, $jsonData);
        return $jsonData;
    }

    public function getSharedImgUrl($sharedImgId)
    {
        return Mage::getUrl('aitcg/index/sharedimage', array('id' => $sharedImgId));
    }

    public function getSharedImgWasCreatedUrl()
    {
        return Mage::getUrl('aitcg/ajax/sharedimgwascreated', array());
    }

    /* params
    // Mage_Catalog_Model_Product $product
    // string $sharedImgId
    */
    public function getEmailToFriendUrl(Mage_Catalog_Model_Product $product, $sharedImgId = '0')
    {
        return Mage::helper('catalog/product')->getEmailToFriendUrl($product) . 'aitcg_shared_img_id/' . $sharedImgId . '/';
    }

    public function getImgCreatePath()
    {
        return Mage::getUrl('aitcg/ajax/createimage', array());
    }

    public function getAitcgMainJsClass()
    {
        if(Mage::getStoreConfig('catalog/aitcg/aitcg_use_social_networks_sharing')) 
            return 'Aitcg.Main.SocialWidgets';
        
        return 'Aitcg.Main';
    }

    /**
     *
     * @param number
     *
     */
    public function getSharedImgId($rand)
    {
        return time() . $rand;
    }   

    public function sharedImgWasCreated($sharedImgId = 0)
    {
        $model = Mage::getModel('aitcg/sharedimage');
        $model->load($sharedImgId);
        if(is_null($model->getId()))
            return false;

        if($model->imagesNotExist())
            return false;

        return true;
    }

    //remove Social Widgets code from Admin area and from Cart Sidebar at frontend
    public function removeSocialWidgetsFromHtml($html = '')
    {
        $array = explode('<!-- aitoc social widgets DO NOT TOUCH THIS LINE !!! -->', $html);
        if(count($array) > 1)
        {
            $html = '';
            foreach($array as $val)
            {
                if(!strstr($val, '<!-- aitoc social widgets inner html DO NOT TOUCH THIS LINE ALSO !!! -->'))
                {
                    $html .= $val;
                }
            }
        }
        else
        {
            return $html;
        }

        return $html;
    }
}