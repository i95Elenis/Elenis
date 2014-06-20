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
class Aitoc_Aitcg_Model_Image extends Mage_Core_Model_Abstract
{
    protected $_moduleFolderPath = 'custom_product_preview/';
    protected $_tempFolderName = 'quote';
    protected $_storeFolderName = 'order';  
    protected $_order_store_model = null;
    
    protected $_storePreviewName = 'preview_full';
    protected $_storePreviewThumbnailName = 'preview_thumb';    
    
    protected $_storeProductImageName = 'image_full';
    protected $_storeProductImageThumbnailName = 'image_thumb';
    
    protected $_saveImages = false;
    protected $_imagesDeleted = false;
    
    protected $_generatedThumbnailSize = 200;
    
    private static $_callbacks = array(
        IMAGETYPE_GIF  => array('output' => 'imagegif',  'create' => 'imagecreatefromgif'),
        IMAGETYPE_JPEG => array('output' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'),
        IMAGETYPE_PNG  => array('output' => 'imagepng',  'create' => 'imagecreatefrompng'),
        IMAGETYPE_XBM  => array('output' => 'imagexbm',  'create' => 'imagecreatefromxbm'),
        IMAGETYPE_WBMP => array('output' => 'imagewbmp', 'create' => 'imagecreatefromxbm'),
    );

    public function _construct()
    {
        parent::_construct();
        $this->_init('aitcg/image');
    }
    
    public function getFullTempPath() {
        // -> /custom_product_preview/temp
        return $this->_moduleFolderPath . $this->_tempFolderName;
    }
    
    public function getMediaTempPath() {
        // -> /home/local/.../media/custom_product_preview/temp
        return Mage::getBaseDir('media') . '/' . $this->getFullTempPath();
    }
    
    private function _getTempPath() {
        return '/' . $this->_moduleFolderPath . $this->_tempFolderName;
    }                
    
    private function _getStorePath() {
        return $this->_moduleFolderPath . $this->_storeFolderName;
    }

    private function _getMediaStorePath() {
        return Mage::getBaseDir('media') . '/' . $this->_moduleFolderPath. $this->_storeFolderName;
    }

    private function _getMediaStoreFolder() {
        return $this->_getMediaStorePath() .'/'. $this->getId() . '/';
    }    
    
    private function _getStoreFolder() {
        return $this->_getStorePath() .'/'. $this->getId() . '/';
    }    
    
    private function _getStoreMediaPath() {
        return Mage::getBaseDir('media') . '/' . $this->_getStoreFolder();
    }
    
    private function _getStorePreviewImagePath() {
        /*$ext = explode(".", $this->getFileName() );
        $ext = end($ext);*/
        return '/' . $this->_getStoreFolder() /*. $this->_storePreviewName . '.' . $ext*/;
    }
    
    private function _getMediaStorePreviewImagePath() {
        return Mage::getBaseDir('media') . $this->_getStorePreviewImagePath();
    }
    
    private function _getStoreUrl( $file ) {
        return Mage::getBaseUrl('media') . $this->_getStoreFolder() . $file;
    }
    
    private function _getImageUrl() {
        return Mage::getBaseUrl('media') . $this->getFullTempPath() . '/' . $this->getFileName();
    }
    
    public function getFileName() {
        return $this->getData('temp_id'). '_' . $this->getData('file_name');
    }
    
    private function _getPngThumbnailName() {
        return $this->getFileName() . '.png';
    }
    
    public function getImageFullPath() {
        return $this->getMediaTempPath() . '/' . $this->getFileName();
    }
    
    public function getFullData( ) {
        $return = $this->getData();
        
        if($this->getIsOrder() > 1 && $this->getIsOrder() != 10 && $this->getIsOrder() != 11) {
            $return['temp_image_url'] = $this->_getStoreFile( $this->getFileName(), $this->_storePreviewName );
            $return['temp_thumbnail_url'] = $this->_getStoreLastFile($this->_storePreviewThumbnailName);
            return $return;
        } else if ($this->getIsOrder() == 1 || $this->getIsOrder() >= 10) {
            $temp_image = $this->_getStorePreviewImagePath() . '.png';
        } else {
            $temp_image = '/' . $this->getFullTempPath() . '/' . $this->_getPngThumbnailName();
        }
        if(!file_exists(Mage::getBaseDir('media') . $temp_image) && empty($return['img_data'])) {
            $return['id'] = 0;
            return $return;
        }
        
        $return['temp_image_url'] = $this->_getImageUrl();        
        
        $img = $this->_getImageObject($temp_image);
                   
        $return['temp_thumbnail_url'] = $img->__toString();
        
        if($this->getIsOrder() == 1 || $this->getIsOrder() == 11) {
            $return['temp_image_url'] = $this->_getStoreFile( $this->getFileName(), $this->_storePreviewName );
            $return['temp_thumbnail_url'] = $this->_moveToStore( $img->getNewFile(), $this->_storePreviewThumbnailName, true, true);
            $this->setData('is_order', $this->getIsOrder()+1);
        }
     
        return $return;
    }
    protected function _getImageObject($temp_image)
    {
        $return = $this->getData();
            $img = Mage::helper('aitcg/image')
            ->loadLite( $temp_image, 'media' );

        $size = Mage::helper('aitcg/options')
                ->calculateThumbnailDimension( 100, 100, $return['preview_width'], $return['preview_height'] );

        $default_size_x = ($return["scale_x"] == 1) ? $size[0] : round($size[0] * $return["scale_x"]);
        $default_size_y = ($return["scale_y"] == 1) ? $size[1] : round($size[1] * $return["scale_y"]);
        $img->keepAspectRatio(false);

        if( $return["angle"] != 0 ) {
            $img->rotate( -1 * $return["angle"] );
            $img->backgroundColor(0,0,0,127);
        }

        $img->keepFrame(false)
            ->resize($default_size_x, $default_size_y);
                return $img;
    }
    
    private function _moveToStore($url, $to_name, $rename = false, $thumb = false) {
        if($url == "") {
            return $url;
        }
        $dest = $this->_getMediaStoreFolder();
        $ext = explode(".", $url);
        $ext = end($ext);
        $name = $to_name . "." . $ext;
        if($rename) {
            if($thumb) {
                $files = glob($dest . $to_name . '*'.$ext);
                if( sizeof($files) > 0 ) {
                    $name = $to_name . '_'.sizeof($files).'.' .$ext;
                }
            }
            $check = rename($url, $dest . $name);
        } else {
            $check = copy($url, $dest . $name);
        }                                    
        if($check) {
            return $this->_getStoreUrl( $name );
        } else {
            return $url;
        }
    }
    
    private function _getStoreFile( $file, $name ) {
        $ext = explode(".", $file);
        return $this->_getStoreUrl( $name . "." . end($ext) );
    }
    
    private function _getStoreLastFile( $name ) {
        $ext = 'png';
        $dest = $this->_getMediaStoreFolder();
        $files = glob($dest . $name . '*'.$ext);
        if( sizeof($files) > 1 ) {
            $name = $name . '_'.(sizeof($files)-1).'.' .$ext;
        } else {
            $name = $name . "." . $ext;
        }
        return $this->_getStoreUrl( $name );
    }
    
    public function getMediaImage( $product_id, $entity_id = 0 ) {
        if($this->getData("image_template_id") != 0 ) {
            $entity_id = $this->getData("image_template_id");
        }
        $media = Mage::getSingleton('aitcg/resource_template')
            ->getTemplateImage( $product_id, $entity_id );
        if($media === false) {
            return false;
        }
        if($this->getIsOrder() == 1 || $this->getIsOrder() == 2) {
            $media['thumbnail_url'] = $this->_moveToStore( $media['thumbnail_file'], $this->_storeProductImageThumbnailName);
            $media['full_image'] =    $this->_moveToStore( 
                Mage::getSingleton('catalog/product_media_config')->getMediaPath($media['value']), 
                $this->_storeProductImageName
            );
            $this->setData('is_order',3);
        } elseif($this->getIsOrder() >= 3) {
            $media['thumbnail_url'] = $this->_getStoreFile( $media['thumbnail_file'], $this->_storeProductImageThumbnailName);
            $media['full_image'] =    $this->_getStoreFile( $media['value'], $this->_storeProductImageName);
        }
        return $media;
    }
    
    public function storeImage() {
        if($this->getIsOrder()== 10) {
            $this->setData('is_order',11);
            return true;
        }
        if($this->getIsOrder() != 0) {
            return true;
        }
        $dest = $this->_getMediaStoreFolder();
        if(!is_dir($dest)) {
            mkdir($dest);
        }
        /*$moved = $this->copyPreviewFile();*/
        $moved = $this->copyImages();
        if($moved) {
            //$this->unlinkFile();
            $this->setData('is_order',1);
            $this->setData('create_time', now());
            $this->save();
            return true;
        }
        return false;
    }
    
    
    public function copyImages() {

        $imgData = Mage::helper('core')->jsonDecode($this->getImgData());
        
        foreach($imgData as $key => $img)
        {
            $mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, false);
            $mediaUrlSecure = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, true);
            $mediaUrlSecureIrreg = str_replace('https:','http:', $mediaUrlSecure);
            $sourceFile = str_replace(array($mediaUrl, $mediaUrlSecure, $mediaUrlSecureIrreg), Mage::getBaseDir('media').DS, $img['src']);
            $destFile = str_replace('/quote/', DS . 'order' . DS . $this->getId() . DS, $sourceFile);
            if(file_exists($sourceFile)) {
                copy($sourceFile, $destFile);
           /* } elseif(file_exists($destFile)) {
                $imgData['key']['src']
            */ }else {
      
                throw new Mage_Core_Exception(Mage::helper('aitcg')->__('The preview image was not found, please get back to the shopping cart and check all the required product options'));
            }            
            
        }
        $newImgData = str_replace('/quote/', '/order/'.$this->getId().'/', $this->getImgData());
        $this->setImgData($newImgData );
        return true;
    }    
    
    public function copyPreviewFile() {
        $source = $this->getMediaTempPath() . '/' . $this->getFileName();
        $dest = $this->_getMediaStorePreviewImagePath();
        
        if(file_exists($source)) {
            return rename($source, $dest) && rename($source.'.png', $dest.'.png');
        } elseif(file_exists($dest)) {
            return true;
        } else {
            throw new Mage_Core_Exception(Mage::helper('aitcg')->__('The preview image was not found, please get back to the shopping cart and check all the required product options'));
            return false;
        } 
    }
    
    public function unlinkStoreFiles( ) {
        $dir = $this->_getMediaStoreFolder();
        $files = glob($dir . '*');
        foreach($files as $file) {
            unlink($file);
        }
        rmdir($dir);
    }
    
    public function unlinkFile( $file_name = false ) {
        $file_name = $file_name!==false ? $file_name : $this->getFileName();
        $dest = $this->getMediaTempPath() . '/' . $file_name;
        if(file_exists($dest))
            unlink($dest);

        $png_name = $this->getMediaTempPath() . '/' . $this->_getPngThumbnailName();
        if(file_exists($png_name))
            unlink( $png_name );
    }
    
    public function deleteData() {
        if($this->getData('is_order') == 0) {
            if($this->getFileName() != "")
                $check = $this->unlinkFile();
            else if ($this->getImgData() != "")
            {
                $images = Mage::helper('core')->jsonDecode($this->getImgData());
                
                foreach($images as $image)
                {
                    $fileName = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), Mage::getBaseDir('media').DS, $image['src']);
                    unlink($fileName);
                }
            }        
            
            
        } else {
            $this->unlinkStoreFiles();
        }
        $this->_imagesDeleted = true;
        return parent::delete();
    }
    
    public function saveImage() {
        $this->_saveImages = true;
        return $this;
    }
    
    public function delete() {
        if($this->_saveImages == false && $this->getFileName() != "" && $this->_imagesDeleted == false)
            $check = $this->unlinkFile();
        else if ($this->_saveImages == false && $this->getImgData() != "" && $this->_imagesDeleted == false)
        {
            $images = Mage::helper('core')->jsonDecode($this->getImgData());
            
            foreach($images as $image)
            {
                $fileName = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), Mage::getBaseDir('media').DS, $image['src']);
                unlink($fileName);
            }
        }
        parent::delete();        
    }
    

    public function isOrder() {
        return $this->getData('is_order')==0?false:true;
    }
    
    public function getOrderIds() {
        if($this->_order_store_model != null) {
            return $this->getData('order_ids');
        }
        $this->_order_store_model = Mage::getModel('aitcg/image_store');
        $collection = $this->_order_store_model->getOrdersByImageId( $this->getId() );
        $ids = array();
        foreach($collection as $id) {
            $ids[] = $id->getData('order_id');
        }
        $this->setData('order_ids', $ids);
        return $ids;
    }
    
    public function setOrderId( $id ) {
        $ids = $this->getOrderIds();
        if(in_array($id,$ids)) {
            return true;
        }
        $ids[] = $id;
        $this->setData( 'order_ids', $ids );
        $this->_order_store_model->setData('image_id',$this->getId())
            ->setData('order_id', $id)
            ->setData('id', null);
        $this->_order_store_model->save();
    }





    protected function _getValueFromOption($name, $option)
    {
        preg_match('/'.$name.': ([0-9]*),/', $option['value'], $array_preg);
        return empty($array_preg[1])?0:$array_preg[1];
        
    }
    protected function _prepareDataForMergedImage($orderItem)
    {
        $date = array();
        if ($options = $orderItem->getProductOptions()) 
        {
            //foreach ($options['info_buyRequest']['options'] as $key => $option)
            foreach ($options['options'] as $key => $option)
            {
                //$optionObject = Mage::getModel('catalog/product_option')->load($key);
                //if ($optionObject->getType() == 'aitcustomer_image')
                if ($option['option_type'] == 'aitcustomer_image')
                {
                    //preg_match('/\[.*\]/', $option['option_value'], $data);//json_decode('('.$option['option_value'].')');
                    //$date[]=json_decode($data);
                    $option_request = $options['info_buyRequest']['options'][$option['option_id']];
                    $option_request['img_data'] = json_decode($option_request['img_data'],true);
                    //preg_match('/productImageSizeX: ([0-9]*),/', $option['value'], $array_preg);
                    $option_request['areaOffsetX'] = $this->_getValueFromOption('areaOffsetX',$option);
                    $option_request['areaOffsetY'] = $this->_getValueFromOption('areaOffsetY',$option);
                    $option_request['areaSizeX'] = $this->_getValueFromOption('areaSizeX',$option);
                    $option_request['areaSizeY'] = $this->_getValueFromOption('areaSizeY',$option);
                    
                    $date[] = $option_request;
                }
            }
        }
        return $date;
    }
    
    
    
    protected function _getImg($file)
    {
        $extension = strrchr($file, '.');
        $extension = strtolower($extension);

        switch($extension) {
                case '.jpg':
                case '.jpeg':
                        $im = @imagecreatefromjpeg($file);
                        break;
                case '.gif':
                        $im = @imagecreatefromgif($file);
                        break;
                case '.png':
                        $im = @imagecreatefrompng($file);
                        imageAlphaBlending($im, true);
                        imageSaveAlpha($im, true);
                        break;
                default:
                        $im = false;
                        break;
        }
        
        
        return $im;
    }
    
    
    public function resize($fileNameOld, $fileNameNew, $sizeX, $sizeY = null, $ifSquare = false)
    {
        if($ifSquare)
            $sizeY = $sizeX;
        
        $imgForResize = $this->_getImg($fileNameOld);
        
        $sizeXOld = imagesx($imgForResize);
        $sizeYOld = imagesy($imgForResize);
        
        //$sizeMult = ($sizeXOld/$sizeX > $sizeYOld/$sizeY)?$sizeXOld/$sizeX:$sizeYOld/$sizeY;
        
        if(empty($sizeY))
        {
            $sizeMult = $sizeXOld/$sizeX;
            $sizeY = $sizeYOld/$sizeMult;
        }
        else
            $sizeMult = ($sizeXOld/$sizeX > $sizeYOld/$sizeY)?$sizeXOld/$sizeX:$sizeYOld/$sizeY;
        
        
        $x = ($sizeX - $sizeXOld/$sizeMult)/2;
        $y = ($sizeY - $sizeYOld/$sizeMult)/2;
        
        $image=imagecreatetruecolor($sizeX,$sizeY);
        
        $col = imagecolorexactalpha ($image, 255, 255, 255, 127); 
        imagecolortransparent($image, $col);
        imagefill($image, 0, 0, $col); 
                
        imagecopyresampled($image, $imgForResize, $x, $y, 0, 0, $sizeXOld/$sizeMult, $sizeYOld/$sizeMult, $sizeXOld, $sizeYOld);
                
        imagejpeg($image, $fileNameNew);
        imagedestroy($image);
        
        return $fileNameNew;
    }
    protected function _transformImage($image, &$info, $MergedImage = null)
    {
        $h=imagesy($image);
        $w=imagesx($image);
        //$imageTransform = $image;
        if($h != round($info['height']) || $w != round($info['width']))
        {
            
            $imageTransform=imagecreatetruecolor($info['width'],$info['height']);
            
            imageAlphaBlending($imageTransform, false);
            imageSaveAlpha($imageTransform, true);
            
            imagecopyresampled($imageTransform,$image,0,0,0,0,round($info['width']),round($info['height']),$w,$h);
            $image = $imageTransform;
        }
        //$info['rotation'] = 90;
        if(!empty($info['rotation']))
        {
            $col = imagecolorexactalpha ($image, 57, 57, 57, 127); 
            
            $imageTransform = imagerotate($image, -$info['rotation'], $col);
            
            $ims = imagecreatetruecolor(imagesx($imageTransform),imagesy($imageTransform)); 
            
            imagecolortransparent($ims, $col);
            imagefill($ims, 0, 0, $col); 
            
            imagecopy($ims, $imageTransform, 0, 0, 0, 0, imagesx($imageTransform), imagesy($imageTransform));
            
            $info['x'] -= (imagesx($ims)-imagesx($image))/2;
            $info['y'] -= (imagesy($ims)-imagesy($image))/2;
            
            $image = $ims;
            //imagepng($image);die();
        }
        $info['width'] = imagesx($image);
        $info['height'] = imagesy($image);
        if (!empty($MergedImage))
        {
            $new_image = array();
            if($info['x'] > 0)
            {
                $info['x'] = $info['x'] + $MergedImage['areaOffsetX'];
                $info['beginX'] = 0;
            }
            else
            {
                $info['beginX'] = -$info['x'];
                $info['width'] = $info['width'] + $info['x'];
                $info['x'] = $MergedImage['areaOffsetX'];
            }
            if($info['y'] > 0)
            {
                $info['y'] =$info['y']+ $MergedImage['areaOffsetY'];
                $info['beginY'] = 0;
            }
            else
            {
                $info['beginY'] = -$info['y'];
                $info['height'] = $info['height'] + $info['y'];
                $info['y'] = $MergedImage['areaOffsetY'];
            }
            if($info['x']+$info['width'] > $MergedImage['areaSizeX']+$MergedImage['areaOffsetX'])
            {
                $info['width'] = $MergedImage['areaSizeX']+$MergedImage['areaOffsetX'] - $info['x']; 
            }
            if($info['y']+$info['height'] > $MergedImage['areaSizeY']+$MergedImage['areaOffsetY'])
            {
                $info['height'] = $MergedImage['areaSizeY']+$MergedImage['areaOffsetY'] - $info['y']; 
            }
        }
       // die();
        //imagepng($imageTransform);die();
        return $image;
    }
    
    public function _mergeImage($MergedImage)
    {
        
       // header("Content-type: image/png");
        $im = imagecreatefromjpeg(Mage::getBaseDir('media').'/custom_product_preview/order/'.$MergedImage['template_id'].'/image_full.jpg');
        imageAlphaBlending($im, true);
       // imageSaveAlpha($im, true);
        foreach($MergedImage['img_data'] as $image)
        {
            $imgForMerge = $this->_getImg($image['src']);
            $imgForMerge = $this->_transformImage($imgForMerge, $image, $MergedImage);
            
            if(!empty($image['opacity']))
            {
                $this->_imageCopyAlpha($im, $imgForMerge, $image['x'],$image['y'],$image['beginX'],$image['beginY'],$image['width']-1,$image['height']-1, $image['opacity']);
            }
            else
            {
                imagecopy($im, $imgForMerge, $image['x'],$image['y'],$image['beginX'],$image['beginY'],$image['width']-1,$image['height']-1);
            }
            
            $maskCreated = (string) $image['maskCreated'];
        }
        
        if(!empty($maskCreated))
            $im = $this->_combineImageByMask(imagecreatefromjpeg(Mage::getBaseDir('media').'/custom_product_preview/order/'.$MergedImage['template_id'].'/image_full.jpg'), $im,$maskCreated);
        $filePart = Mage::getBaseDir('media').'/custom_product_preview/product/'.$MergedImage['template_id'].'/'.rand().'/';
         if(!is_dir($filePart)) {
            mkdir($filePart, 0777, true);
        }
        //$fileName .= rand();
        $nameImage = array();
        imagejpeg($im, $filePart.'image_full.jpg');
        $nameImage['image'] = $filePart.'image_full.jpg';
        imagedestroy($im);
        $nameImage['small_image'] = $this->resize($filePart.'image_full.jpg', $filePart.'image_small.jpg', 135, 0, true);
        $nameImage['thumbnail'] = $this->resize($filePart.'image_full.jpg', $filePart.'image_thumbnail.jpg', 200, 0);
        return $nameImage;
    }
    
    
    protected function _combineImageByMask($originalImage, $newImage, $maskId)
    {
        $mask_created = Mage::getModel('aitcg/mask_created')->load($maskId);
        $mask_created_image = $this->_getImg($mask_created->getImagesPath().'mask_inverted.png');
        $img_size=getimagesize($mask_created->getImagesPath().'mask_inverted.png');
        $mask_created_image_resized = imagecreatetruecolor($mask_created->getWidth(), $mask_created->getHeight());
        $white = imagecolorallocatealpha ($mask_created_image_resized,   255, 255, 255, 127);
        imagecolortransparent($mask_created_image_resized,$white );
        imagefill($mask_created_image_resized, 0, 0, $white); 

        imagealphablending($mask_created_image_resized, false);
        imagesavealpha($mask_created_image_resized, true);
        imagecopyresampled($mask_created_image_resized, $mask_created_image, 0, 0, 0, 0, $mask_created->getWidth(), $mask_created->getHeight(), $img_size[0], $img_size[1]);
        /*imagepng($mask_created_image_resized, $mask_created->getImagesPath().'mask_inverted123.png');
        die();*/
        imagedestroy($mask_created_image);
        $originalImage_copy = $originalImage;
        $startX=$mask_created->getX();
        $startY=$mask_created->getY();
        $black = imagecolorallocate ($mask_created_image_resized,   0, 0, 0);
        for ($x = 0; $x < $mask_created->getWidth(); $x++)
            for ($y = 0; $y < $mask_created->getHeight(); $y++)
            {
                if(imagecolorat($mask_created_image_resized, $x, $y) != $black)
                {
                    $pixelX = $x+$startX;
                    $pixelY = $y+$startY;
                    
                    $color_new = imagecolorat($newImage, $pixelX, $pixelY);
                    
                    //$color_old = imagecolorat($originalImage_copy, $pixelX, $pixelY);
                    
                    $color_copy = imagecolorallocate($originalImage_copy, ($color_new >> 16 ) & 0xFF, ( $color_new >> 8 ) & 0xFF, $color_new & 0xFF);
                    imagesetpixel($originalImage_copy, $pixelX, $pixelY, $color_copy);
                    //echo $pixelX.'x'.$pixelY.'='.$color_new.'x'.$color_old.'<br>';
                }
            }
        imagedestroy($mask_created_image_resized);
        return $originalImage_copy;
    }
    
    public function createMergedImage($orderItem)
    {
        $dataForMergedImage = $this->_prepareDataForMergedImage($orderItem);
      
        $dataImage = array();
        foreach($dataForMergedImage as $MergedImage)
        {
            //$this->_mergeImage($canvas, $MergedImage);
            $dataImage[]=$this->_mergeImage($MergedImage);
        }
        return $dataImage;
       
    }

   protected function _imageCopyAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
        
        if(!isset($pct)){ 
            return false; 
        }
        $w = imagesx( $src_im ); 
        $h = imagesy( $src_im ); 
        imagealphablending( $src_im, false );
        $minalpha = 127; 
        for( $x = 0; $x < $w; $x++ ) 
        for( $y = 0; $y < $h; $y++ ){ 
            $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
            if( $alpha < $minalpha ){ 
                $minalpha = $alpha; 
            } 
        } 
        for( $x = 0; $x < $w; $x++ ){ 
            for( $y = 0; $y < $h; $y++ ){ 
                $colorxy = imagecolorat( $src_im, $x, $y ); 
                $alpha = ( $colorxy >> 24 ) & 0xFF; 
                if( $minalpha !== 127 ){ 
                    $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
                } else { 
                    $alpha += 127 * $pct; 
                } 
                $alphacolorxy = imagecolorallocatealpha( $src_im,
                                        ( $colorxy >> 16 ) & 0xFF,
                                        ( $colorxy >> 8 ) & 0xFF,
                                            $colorxy & 0xFF, $alpha
                                ); 
                if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
                    return false; 
                } 
            } 
        } 
        return imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h); 
    }

    public static function uploadFile($path, $nameOfParam, $arrayOfExt)
    {
        $uploader = new Varien_File_Uploader($nameOfParam);
        $uploader->setAllowedExtensions($arrayOfExt); 
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->save($path, preg_replace('/[^A-Za-z\d\.]/','_',$_FILES['filename']['name']));
        return $uploader->getUploadedFileName();
    }
    
    public function setFilenameWithUnlink($filename)
    {
        if($this->getFilename() && $this->getFilename() !=  $filename) 
        {
            $fullPath = $this->getImagesPath() . $this->getFilename();
            @unlink($fullPath);                    
            $fullPath = $this->getImagesPath() . 'preview' . DS . $this->getFilename();
            @unlink($fullPath);                           
        }                    
        $this->setFilename($filename);
        $thumb = new Varien_Image($this->getImagesPath() . $this->getFilename());
        $thumb->open();
        $thumb->keepAspectRatio(true);
        $thumb->keepFrame(true);
        $thumb->backgroundColor(array(255,255,255));
        #$thumb->keepTransparency(true);
        $thumb->resize(135);
        $thumb->save($this->getImagesPath() . 'preview' . DS . $this->getFilename());

    }
}