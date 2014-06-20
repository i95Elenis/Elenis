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
class Aitoc_Aitcg_AjaxController extends Mage_Core_Controller_Front_Action
{
    private function getModelType() {
        $type = Mage::app()->getRequest()->get('aittype');
        return ($type == 'temp')?'aitcg/image_temp':'aitcg/image';
    }
    
    public function fontPreviewAction()  {
      if ($this->getRequest()->isPost()) {                        
            $font_id = Mage::app()->getRequest()->get('font_id');
            $rand = Mage::app()->getRequest()->get('rand');
            $model = Mage::getModel('aitcg/font')->load($font_id);
            $response = array();
            $response['src'] = Mage::helper('aitcg/font')->getFontPreview($model->getFontsPath().$model->getFilename());
            $response['rand'] = $rand;
            
            $this->_setBodyJson($response);
       }
    }
    
    public function masksCategoryAction()  {
      if ($this->getRequest()->isPost()) {
            $category_id = Mage::app()->getRequest()->get('category_id');
            $rand = Mage::app()->getRequest()->get('rand');
            $response = array();
            $response['images'] = Mage::helper('aitcg/mask_category')->getCategoryMaskRadio($category_id, $rand);
            $response['rand'] = $rand;
            
            $this->_setBodyJson($response);
       }
    }    
    
    public function categoryPreviewAction()  {
      if ($this->getRequest()->isPost()) {                        
            $category_id = Mage::app()->getRequest()->get('category_id');
            $rand = Mage::app()->getRequest()->get('rand');
            $response = array();
            $response['images'] = Mage::helper('aitcg/category')->getCategoryImagesRadio($category_id, $rand);
            $response['rand'] = $rand;
            
            $this->_setBodyJson($response);
       }
    }    

    public function addTextAction()
    {
      if ($this->getRequest()->isPost()) {    
          $font = Mage::app()->getRequest()->get('font');
          $color = Mage::app()->getRequest()->get('color');
          $text = Mage::app()->getRequest()->get('text');
          $outline = Mage::app()->getRequest()->get('outline');
          if (!empty($outline))
          {
              $outline=array();
              $outline['color'] = Mage::app()->getRequest()->get('coloroutline');
              $outline['wight'] = (Mage::app()->getRequest()->get('widthoutline')>1 && is_numeric(Mage::app()->getRequest()->get('widthoutline')))?Mage::app()->getRequest()->get('widthoutline'):1;
          }
          $shadow = Mage::app()->getRequest()->get('shadow');
          if (!empty($shadow))
          {
              $shadow = array();
              $shadow['alpha'] = Mage::app()->getRequest()->get('shadowalpha');
              $shadow['alpha'] = (is_numeric($shadow['alpha']))?($shadow['alpha']<0?0:($shadow['alpha']>126?126:$shadow['alpha'])):50;
              $shadow['x'] = Mage::app()->getRequest()->get('shadowoffsetx');
              $shadow['x'] = (is_numeric($shadow['x']))?$shadow['x']:20;
              $shadow['y'] = Mage::app()->getRequest()->get('shadowoffsety');
              $shadow['y'] = (is_numeric($shadow['y']))?$shadow['y']:20;
              $shadow['color'] = Mage::app()->getRequest()->get('colorshadow');
          }
          $model = Mage::getModel('aitcg/font')->load($font);
          $filename = Mage::helper('aitcg/font')->getTextImage($model->getFontsPath().$model->getFilename(),$text,$color,$outline,$shadow);
          $this->_imageTmpSave($filename);
          $response = '\''.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'custom_product_preview/quote/'.$filename.'\'';          
          $this->getResponse()->setBody($response); 
      }
    }
    
    public function addPredefinedAction()
    {
      if ($this->getRequest()->isPost()) {    
            $imgId = Mage::app()->getRequest()->get('img_id');
            $filename = Mage::helper('aitcg/category')->copyPredefinedImage($imgId);

            
            $this->_imageTmpSave($filename);   
            
            $response = array();
            $response['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'custom_product_preview/quote/'.$filename;
            
            $this->_setBodyJson($response);           
          
      }        
    }
    
    
    public function addMaskAction()
    {
      if ($this->getRequest()->isPost()) {    
            $maskId = Mage::app()->getRequest()->get('mask_id');
            //$filename = Mage::helper('aitcg/mask_category')->copyPredefinedImage($maskId);
            //$this->_imageTmpSave($filename);   
            $image = Mage::getModel('aitcg/mask')->load($maskId);
        
            $response = array();
            $response['url'] = $image->getImagesUrl(). 'alpha/' . $image->getFilename();
            $response['url_base'] = $image->getImagesUrl(). 'alpha/';
            $response['url_white'] = $image->getImagesUrl(). 'alpha/' .  'white'.$image->getFilename();
            $response['resize'] = $image->getResize();
            $response['id'] = $image->getId();
            //$response['location'] = $image->getLocation();
            
            $this->_setBodyJson($response);           
          
      }        
    }
    
    
    
    public function createMaskAction()
    {
        if ($this->getRequest()->isPost()) {
            $request = Mage::app()->getRequest();
            //$mask = Mage::getModel('aitcg/mask')->load($request->get('mask_id'));
            $mask_created = Mage::getModel('aitcg/mask_created');
            /*$mask_created->setData('x', $request->get('x'));
            $mask_created->setData('y', $request->get('y'));
            $mask_created->setData('mask_id', $request->get('mask_id'));
            $mask_created->setData('width', $request->get('width'));
            $mask_created->setData('height', $request->get('height'));*/
            
            $mask_created->setX($request->get('x'));
            $mask_created->setY($request->get('y'));
            $mask_created->setMaskId($request->get('mask_id'));
            $mask_created->setWidth($request->get('width'));
            $mask_created->setHeight($request->get('height'));
            //var_dump($mask_created);die();
            $mask_created->save();
            $response = array();
            $response['id'] = $mask_created->getId();
            $this->_setBodyJson($response);
        }
    }
    
    
    
    public function getMaskAction()
    {
        $mask_created = Mage::getModel('aitcg/mask_created')->load(Mage::app()->getRequest()->get('id'));
        
        $response = array();
        $response['mask'] = array();
        $response['mask']['id'] = $mask_created->getId();
        $response['mask']['x'] = $mask_created->getX();
        $response['mask']['y'] = $mask_created->getY();
        $response['mask']['mask_id'] = $mask_created->getMaskId();
        $response['mask']['width'] = $mask_created->getWidth();
        $response['mask']['height'] = $mask_created->getHeight();
        $response['mask']['url'] = $mask_created->getImagesUrl(). 'mask_inverted.png';
        $response['mask']['url_base'] = Mage::getModel('aitcg/mask')->getImagesUrl(). 'alpha/';
        $response['mask']['url_white'] = $mask_created->getImagesUrl(). 'white_mask_inverted.png';
        $response['mask']['url_black'] = $mask_created->getImagesUrl(). 'black_mask_inverted.png';
        $this->_setBodyJson($response);
    }
    
    
    public function delMaskAction()
    {
        $mask_created = Mage::getModel('aitcg/mask_created')->load(Mage::app()->getRequest()->get('id'));
        $mask_created->delete();
        $response = array();
        $response['send'] = 'ok';
        $this->_setBodyJson($response);
    }
    
    public function setUpUploader()
    {
        $uploader = new Varien_File_Uploader('new_image');
        $uploader->setAllowedExtensions(explode(', ',Mage::getStoreConfig('catalog/aitcg/aitcg_image_extensions'))); 
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        return $uploader;
    }
    public function addImageAction()
    {
       $response = array();
        if(isset($_FILES['new_image']['name']) && (file_exists($_FILES['new_image']['tmp_name']))) {
            $uploader = $this->setUpUploader();
            $path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'quote' . DS;
            try 
            {
                $uploader->save($path, preg_replace('/[^A-Za-z\d\.]/','_',$_FILES['new_image']['name']));
                $filename = $uploader->getUploadedFileName();

                $filename = $this->_convertToImg($filename);
                
                if(getimagesize($path.$filename)!==false)
                {
                    $this->_imageTmpSave($filename);
                    $response['src'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'custom_product_preview/quote/'.$filename;
                    $response['error'] = 0;
                }
                else
                {
                    $response['error'] = Mage::helper('aitcg')->__('Image file is empty or corrupt');
                }
            }
            catch (Exception $e)
            {
                $response['error'] = $e->getMessage();
            }
          
        }
        else
        {
            $response['error'] = Mage::helper('aitcg')->__('Something went wrong. Please try again.');
        }
        
        $this->_setBodyJson($response); 
    }
    
    protected function _setBodyJson($response)
    {
        if(version_compare(Mage::getVersion(), '1.4.0.0', '>='))
        {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
        else
        {
            $this->getResponse()->setBody(Zend_Json::encode($response)); 
        }
    }
    protected function _imageTmpSave($filename)
    {
        $tmpImage = Mage::getModel('aitcg/image_temp');
        $tmpImage->setData('create_time', now())->setFileName($filename);
        $tmpImage->save();
    }
    protected function _convertToImg($filename)
    {
        if(Mage::getStoreConfig('catalog/aitcg/aitcg_use_imagemagick') && !in_array(pathinfo($filename, PATHINFO_EXTENSION),array('jpg','jpeg','bmp','gif','png')))
        {
            $path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'quote' . DS;
            exec('convert '.$path.$filename.' -flatten '.$path.$filename.'.jpg');
            $filename.='.jpg';
        }
        return $filename;
    }

    // creates image for social networks sharing functionality
    public function createImageAction()
    {
     
        $productId = $this->getRequest()->get('prodId');
        //$imgTemplateId = $this->getRequest()->get('imgTemplateId');
        $templateImgPath = $this->getRequest()->get('templateImgPath');
        $optionValue = $this->getRequest()->get('optionValue');
        $imgFullUrl = $this->getRequest()->get('imgFullUrl');
        $areaSizeX = intval($this->getRequest()->get('areaSizeX'));
        $areaSizeY = intval($this->getRequest()->get('areaSizeY'));
        $areaOffsetX = intval($this->getRequest()->get('areaOffsetX'));
        $areaOffsetY = intval($this->getRequest()->get('areaOffsetY'));
        $model = Mage::getModel('aitcg/sharedimage');
        $result = $model->createImage($productId, $optionValue, $templateImgPath, $areaSizeX, $areaSizeY, $areaOffsetX, $areaOffsetY);
        if($result == Aitoc_Aitcg_Model_Sharedimage::RESULT_CODE_ERROR_IMG_SIZE)
            echo 'imgSizeError';
        elseif($result == Aitoc_Aitcg_Model_Sharedimage::RESULT_CODE_SUCCESS)
            echo 'success';
      }

    public function sharedImgWasCreatedAction()
    {
        $sharedImgId = $this->getRequest()->get('sharedImgId');
        if(Mage::helper('aitcg')->sharedImgWasCreated($sharedImgId))
            echo 'success';
        else
            echo 'false';
    }
}