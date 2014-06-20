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
class Aitoc_Aitcg_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();     
        $this->renderLayout();
    }
    
    public function cronAction() 
    {
        $model = Mage::getModel('aitcg/observer');
        $model->cronDeleteTempImages();
    }
    
    public function svgAction()
    {
        $request = $this->getRequest();
       
        $data = $request->getPost('data');
        $model = Mage::getModel('aitcg/image_svg');
        /** @var $model Aitoc_Aitcg_Model_Image_Svg */
        switch($request->getPost('type'))        
        {
            case 'VML':
                $matches = ARRAY();
                preg_match('/WIDTH:\s(\d+)px;.*?HEIGHT:\s(\d+)px;/si',$data,$matches);
                $data = Mage::getModel('aitcg/image_converter')->vmlToSvg($data);
                $data = $model->normalizeMultiSvg($data, $matches[1],$matches[2]);
                break;
            case 'SVG':
                $data = '<?xml version="1.0" ?>'.$data;
                break;
            default:
                Mage::throwException(Mage::helper('aitcg')->__('Unknown image type'));
        }
        $useBackground = (bool)Mage::getStoreConfig('catalog/aitcg/show_background_images');
        if($useBackground && $request->getPost('background') != '') {
            $data = $model->prepareBackground($data, $request->getPost('background'), $request->getPost('areaOffsetX'), $request->getPost('areaOffsetY'), $request->getPost('print_scale'));
        }
        $data = $model->normalizeMask($data);
        $data = $model->applyBackground($data);
        
        //print $data; die();
        
        $data = $model->normalize($data);

        
        $this->getResponse()->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type','image/svg+xml',true)
                ->setHeader('Content-Disposition','attachment; filename="image.svg"',true);
        $this->getResponse()->clearBody();

        //$this->getResponse()->setBody(str_replace('xlink:','',$data)); 
        $this->getResponse()->setBody($data); 
    }

    public function svgtopngAction()
    {
        $request = $this->getRequest();
       
        $data = $request->getPost('svg');
        $model = Mage::getModel('aitcg/image_svg');
        switch($request->getPost('type'))
        
        {
            case 'VML':
                $data = Mage::getModel('aitcg/image_converter')->vmlToSvg($data);
                $data = Mage::getModel('aitcg/image_svg')->normalizeMultiSvg($data);
                break;
            case 'SVG':
                break;
            default:
                Mage::throwException(Mage::helper('aitcg')->__('Unknown image type'));
        }
        $useBackground = (bool)Mage::getStoreConfig('catalog/aitcg/show_background_images');
        if($useBackground && $request->getPost('background') != '') {
            $data = $model->prepareBackground($data, $request->getPost('background'), $request->getPost('areaOffsetX'), $request->getPost('areaOffsetY'), $request->getPost('print_scale'));
        }    
        $data = $model->normalizeMask($data);
        $data = $model->applyBackground($data);
        $data = $model->resetMaskForPng($data);
        $this->getResponse()->setBody($data); 
    }

    public function pdfAction()
    {
        $request = $this->getRequest();
       
        $data = $request->getPost('data');
        $scale = $request->getPost('print_scale');
        $model = Mage::getModel('aitcg/image_svg');
        switch($request->getPost('type'))
        {
            case 'VML':
                $matches = ARRAY();
                preg_match('/WIDTH:\s(\d+)px;.*?HEIGHT:\s(\d+)px;/si',$data,$matches);
                $data = Mage::getModel('aitcg/image_converter')->vmlToSvg($data);
                $data = Mage::getModel('aitcg/image_svg')->normalizeMultiSvg($data, $matches[1],$matches[2]);
                break;
            case 'SVG':
                $data = '<?xml version="1.0" ?>'.$data;
                break;
            default:
                Mage::throwException(Mage::helper('aitcg')->__('Unknown image type'));
        } 
    
        //$data = Mage::getModel('aitcg/image_svg')->normalizeMask($data);
        $data = $model->applyBackground($data);
        $data = $model->resetMaskForPDF($data);
        $data = $model->addWhiteFontForPDF($data);
        $data = $model->normalize($data);

        $this->getResponse()->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                //->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type','application/pdf',true)
                ->setHeader('Content-Disposition','attachment; filename="downloaded.pdf"',true);
        $this->getResponse()->clearBody();

        //$this->getResponse()->setBody(str_replace('xlink:','',$data)); 
        $imagick = new Imagick(); 
        $imagick->readImageBlob($data);
        if ($scale !=1 && $scale <= 15)
        {
            $imagick->scaleImage($scale*$imagick->getImageWidth(), $scale*$imagick->getImageHeight());
        }
        $imagick->setImageFormat("pdf");

        /*$imagick->writeImage(MAGENTO_ROOT.'/media/us-map.pdf');scaleImage */
        $this->getResponse()->setBody($imagick); 
        $imagick->clear();
        $imagick->destroy();
    }
    
    protected function _initSharedImage()
    {
        $id = (string) $this->getRequest()->getParam('id');

        if (!$id) 
        {
            return false;
        }

        $sharedImgModel = Mage::getModel('aitcg/sharedimage');
        $sharedImgModel->load($id);
        if($sharedImgModel->productNotExist() || $sharedImgModel->imagesNotExist())
        {
            return false;
        }

        return true;
    }

    public function sharedimageAction()
    {
        if(!$this->_initSharedImage())
        {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $pageId = Mage::getStoreConfig('web/default/cms_no_route');
            if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
                $this->_forward('defaultNoRoute');}
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}