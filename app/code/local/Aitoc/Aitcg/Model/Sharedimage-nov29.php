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
class Aitoc_Aitcg_Model_Sharedimage extends Aitoc_Aitcg_Model_Image
{
    const RESULT_CODE_SUCCESS = 1;
    const RESULT_CODE_ERROR_IMG_SIZE = 2;
    const RESULT_CODE_ERROR = 3;

    protected $_eventPrefix = 'aitcg_sharedimage';

    protected $_sharedImagesFolderName = 'shared_images';

    protected $_widthForImagesOnProductViewPage = 400;

    public function _construct()
    {
        parent::_construct();
        $this->_init('aitcg/sharedimage');
    }

    public function getSharedImgPath()
    {
        $path = Mage::getBaseDir('media') . DS . $this->_moduleFolderPath . $this->_sharedImagesFolderName . DS . 
                    $this->getProductId() . DS . $this->getSharedImgId() . DS . $this->getSharedImgId() . '.png';

        return $path;        
    }

    public function getSharedSmallImgPath()
    {
        $path = Mage::getBaseDir('media') . DS . $this->_moduleFolderPath . $this->_sharedImagesFolderName . DS . 
                    $this->getProductId() . DS . $this->getSharedImgId() . DS . $this->getSharedImgId() . '_sm.png';

        return $path;        
    }

    public function getSharedThumbnailImgPath()
    {
        $path = Mage::getBaseDir('media') . DS . $this->_moduleFolderPath . $this->_sharedImagesFolderName . DS . 
                    $this->getProductId() . DS . $this->getSharedImgId() . DS . $this->getSharedImgId() . '_thumb.png';

        return $path;        
    }

    public function getUrlFullSizeSharedImg()
    {
        return Mage::getBaseUrl('media') . $this->_moduleFolderPath . $this->_sharedImagesFolderName . '/' .
            $this->getProductId() . '/' . $this->getId() . '/' . $this->getId() . '.png';
    }

    public function getUrlSmallSizeSharedImg()
    {
        return Mage::getBaseUrl('media') . $this->_moduleFolderPath . $this->_sharedImagesFolderName . '/' . 
            $this->getProductId() . '/' . $this->getId() . '/' . $this->getId() . '_sm.png';
    }

    public function getUrlThumbnailSharedImg()
    {
        return Mage::getBaseUrl('media') . $this->_moduleFolderPath . $this->_sharedImagesFolderName . '/' . 
            $this->getProductId() . '/' . $this->getId() . '/' . $this->getId() . '_thumb.png';
    }

    
    public function productNotExist()
    {
        if(!$this->getProductId())
        {
            return true;
        }
        $storeId = Mage::app()->getStore()->getId();
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->load($this->getProductId());
        if (!Mage::helper('catalog/product')->canShow($product)) 
        {
            return true;
        }
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) 
        {
            return true;
        }

        return false;
    }

    public function imagesNotExist()
    {
        $images = array('img_full' => $this->getSharedImgPath(), 
                            'img_small' => $this->getSharedSmallImgPath(), 
                            'img_thumbnail' => $this->getSharedThumbnailImgPath()
                        );
        foreach($images as $image)
        {
            if(!file_exists($image))
            {
                return true;
            }
        }
        
        return false;
    }

    /**
     * @param   integer $productId
     * @param   string $optionValue
     * @param   string $templateImgPath
     * @param   integer $areaSizeX
     * @param   integer $areaSizeY
     * @param   integer $areaOffsetX
     * @param   integer $areaOffsetY
     */
    public function createImage($productId, $optionValue, $templateImgPath, $areaSizeX, $areaSizeY, $areaOffsetX, $areaOffsetY)
    {
        $imagesData = json_decode($optionValue, true);
        
        $baseImgPath = Mage::getSingleton('catalog/product_media_config')->getMediaPath($templateImgPath);
        
        $destinationImage = $this->_getImg($baseImgPath);
        if(!$destinationImage)
            return self::RESULT_CODE_ERROR;

        if(!$this->checkImageDimensions(imagesx($destinationImage), imagesy($destinationImage)))
            return self::RESULT_CODE_ERROR_IMG_SIZE;

        $result = imageAlphaBlending($destinationImage, true);
        if(!$result)
            return self::RESULT_CODE_ERROR;

        $reservedImgId = 0;
        foreach($imagesData as $imageData)
        {
            $imageData['areaSizeX'] = $areaSizeX;
            $imageData['areaSizeY'] = $areaSizeY;
            $imageData['areaOffsetX'] = $areaOffsetX;
            $imageData['areaOffsetY'] = $areaOffsetY;
            $result = $this->_merge($imageData, $destinationImage);

            if(!$result)
                return self::RESULT_CODE_ERROR;
            elseif($result === self::RESULT_CODE_ERROR_IMG_SIZE || $result === self::RESULT_CODE_ERROR)
                return $result;

            $reservedImgId = (string) $imageData['social_widgets_reserved_img_id'];
            
            $maskCreated = (string) $imageData['maskCreated'];
        }
        if(!empty($maskCreated))
            $destinationImage = $this->_combineImageByMask($this->_getImg($baseImgPath), $destinationImage,$maskCreated);
        if($reservedImgId == 0)
            return self::RESULT_CODE_ERROR; 

        $result = $this->_writeImages($destinationImage, $productId, $reservedImgId);
        if($result !== self::RESULT_CODE_SUCCESS)
            return $result;
   
        $this->setData('shared_img_id',$reservedImgId);
        $this->setData('product_id',$productId);
        
        $this->save();

        if($this->imagesNotExist())
            return self::RESULT_CODE_ERROR;

        return self::RESULT_CODE_SUCCESS;
    }

    
    
    /**
     * @param   image $destinationImage
     * @param   integer $productId
     * @param   string $reservedImgId
     */
    protected function _writeImages($destinationImage, $productId, $reservedImgId)
    {
        $folderName = Mage::getBaseDir('media') . DS . $this->_moduleFolderPath . $this->_sharedImagesFolderName . DS . $productId . DS . $reservedImgId;
        if(!is_dir($folderName))
        {
            $result = mkdir($folderName, 0777, true);
            if(!$result)
                return self::RESULT_CODE_ERROR;
        }

        $createdFileName = $folderName . DS . $reservedImgId . '.png';
        $result = imagepng($destinationImage, $createdFileName);
        if(!$result)
            return self::RESULT_CODE_ERROR;

        $createdImgSizeX = imagesx($destinationImage);
        $createdImgSizeY = imagesy($destinationImage);
        if($createdImgSizeX > $this->_widthForImagesOnProductViewPage)
        {
            $this->resize($createdFileName, $folderName . DS . $reservedImgId . '_sm.png', 
                             $this->_widthForImagesOnProductViewPage, 0, true);
        }
        else
        {
            $result = copy($createdFileName, $folderName . DS . $reservedImgId . '_sm.png');
            if(!$result)
                return self::RESULT_CODE_ERROR;
        }
    
        $this->resize($createdFileName, $folderName . DS . $reservedImgId . '_thumb.png', 200, 0, true);

        return self::RESULT_CODE_SUCCESS;
    }

    /**
     * @param   array $imagesData
     * @param   image $destination
     */
    protected function _merge($imageData, $destination)
    {
        $src = $this->_getImg($imageData['src']);
        if(!$src)
            return self::RESULT_CODE_ERROR;

        if(!$this->checkImageDimensions(imagesx($src), imagesy($src)))
            return self::RESULT_CODE_ERROR_IMG_SIZE;

        $src = $this->_transformImage($src, $imageData, $imageData);
            
        if(!empty($imageData['opacity']))
        {
            $result = $this->_imageCopyAlpha($destination, $src, $imageData['x'], $imageData['y'],
                                                $imageData['beginX'], $imageData['beginY'], 
                                                $imageData['width']-1, $imageData['height']-1, $imageData['opacity']);
            if(!$result) 
                return self::RESULT_CODE_ERROR;
        }
        else
        {
            $result = imagecopy($destination, $src, $imageData['x'], $imageData['y'], $imageData['beginX'], 
                                   $imageData['beginY'], $imageData['width']-1, $imageData['height']-1);
            if(!$result)
                return self::RESULT_CODE_ERROR;
        }

        return true;
    }

    protected function checkImageDimensions($sizeX, $sizeY)
    {
        $allowedValueX = Mage::getStoreConfig('catalog/aitcg/aitcg_social_networks_sharing_max_img_width');
        $allowedValueY = Mage::getStoreConfig('catalog/aitcg/aitcg_social_networks_sharing_max_img_height');

        if(($sizeX > $allowedValueX) || ($sizeY > $allowedValueY))
        {
            return false;
        }

        return true;
    }

    //$url = 'http://local.aitoc.com/1702burim/media/custom_product_preview/quote/white_97.jpg';
    protected function _getSrcImgPathFromUrl($url)
    {
    $pos = strpos($url, '/media/' . $this->_moduleFolderPath);
    $newurl = substr($url, $pos+30);
    $newurl = Mage::getBaseDir('media') . DS . $this->_moduleFolderPath . $newurl;
    $newurl = str_replace('/', DS, $newurl);

    return $newurl;
    }
}