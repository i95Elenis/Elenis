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
class Aitoc_Aitcg_Model_Image_Svg extends Mage_Core_Model_Abstract 
{
    protected $_backgroundData = null;
    protected $_maskApplied = false;
    protected $_paletteSize = null;

    public function getPaletteSize( $data )
    {
        if(is_null($this->_paletteSize)) {
            $this->_paletteSize = array();
            $arrTableWight = array();
            preg_match_all('/<svg[^>]*width="([^"]*)"/U', $data, $arrTableWight);
            $arrTableHeight = array();
            preg_match_all('/<svg[^>]*height="([^"]*)"/U', $data, $arrTableHeight);
            if(!empty($arrTableWight[1]) && !empty($arrTableHeight[1])) {
                $this->_paletteSize = array(
                    'width' => $arrTableWight[1][0],
                    'height' => $arrTableHeight[1][0],
                );
            }
        }
        return $this->_paletteSize;
    }

    public function setPaletteSize($data, $width, $height)
    {
        $this->_paletteSize = array(
            'width' => $width,
            'height' => $height,
        );
        $data = preg_replace('/<svg([^>]*)width="([^"]*)"/U', '<svg$1width="'.$width.'"', $data);
        $data = preg_replace('/<svg([^>]*)height="([^"]*)"/U', '<svg$1height="'.$height.'"', $data);
        return $data;
    }

    public function normalize($data)
    {
        $data = preg_replace('/xmlns:xlink="http:\/\/www\.w3\.org\/1999\/xlink"/si','',$data);

        // incorrect href attribute for Safari and Chrome browser
        // the HACK
        $data = str_replace('href=','xlink:href=',$data);
        // prevent from double xlink:xlink:href if SVG is saved form Firefox
        $data = str_replace('xlink:xlink','xlink',$data);

        
        $count = substr_count($data,'xmlns="http://www.w3.org/2000/svg"');
        if($count>1)
        {
            $data = preg_replace('/xmlns="http:\/\/www\.w3\.org\/2000\/svg"/si','',$data,$count-1);
        }
        $data = preg_replace('/xmlns="http:\/\/www\.w3\.org\/2000\/svg"/si','xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"',$data);
        
        $data = $this->_changeUrlToImage($data);
        
        return $data;
    }
    public function normalizeMultiSvg($data, $w, $h)
    {
        $pregTable = '/<svg x="(.*)px"[^>]*y="(.*)px"[^>]*><image x="(.*)"[^>]*y="(.*)"[^>]*>.*<\/image>[^>]*<\/svg>/U';
        $pregTable = '/<svg x="(.*)px"[^>]*y="(.*)px"[^>]*><image x="(.*)"[^>]*y="(.*)"[^>]*\/>[^>]*<\/svg>/U';
        $arrTable = array();
        preg_match_all($pregTable, $data, $arrTable);
        
        foreach($arrTable[0] as $key=>$img)
        {
            $img_new = preg_replace('/<svg[^>]*>/U','',$img);
            $img_new = preg_replace('/<\/svg>/U','',$img_new);
            $img_new = preg_replace('/x="(.*)"/U','x="'.($arrTable[1][$key]+$arrTable[3][$key]).'"',$img_new);
            $img_new = preg_replace('/y="(.*)"/U','y="'.($arrTable[2][$key]+$arrTable[4][$key]).'"',$img_new);
            $data = str_replace($img, $img_new, $data);
        }
        $data = preg_replace('/<svg[^>]*>/U','<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"   preserveAspectRatio="none" width="'.$w.'" height="'.$h.'">',$data);
            
        
        return $data;
    }
    
    public function normalizeMask($data)
    {
        
        $pregTable = '/<image [^>]*\/mask.*>.*<\/image>/U';
        $arrTable = array();
        preg_match_all($pregTable, $data, $arrTable);
        if(empty($arrTable[0]))
        {
            $pregTable = '/<image [^>]*\/mask.*\/>/U';
            preg_match_all($pregTable, $data, $arrTable);
            if(empty($arrTable[0]))
            {
                return $data;
            }
        }

        //flag that mask was applied to a SVG
        $this->_maskApplied = true;

        foreach($arrTable[0] as $img)
        {
            $data = str_replace ($img, '',$data);
        }

        $palette = $this->getPaletteSize($data);
        if(isset($palette['width'], $palette['height'])) {
            $str = '<mask id="fademask" x="0" y="0" width="'.$palette['width'].'" height="'.$palette['height'].'">'.implode('',$arrTable[0]).'</mask>';
        } else {
            $str = '<mask id="fademask" >'.implode('',$arrTable[0]).'</mask>';
        }
        $data = preg_replace('/<defs[^>]*>.*<\/defs>/U','<defs>'.$str.'</defs>',$data);
        $data = preg_replace('/<defs[^>]*\/>/U','<defs>'.$str.'</defs>',$data);
        $data = preg_replace('/<\/defs>/U','</defs><g  mask="url(#fademask)">',$data);
        $data = preg_replace('/<\/svg>/U','</g></svg>',$data);
        
        return $data;
    }    

    public function prepareBackground($data, $backgroundImage, $offset_x = 0, $offset_y = 0, $scale = 1)
    {
        $img = Mage::getSingleton('aitcg/image_transform')->getImg($backgroundImage);
        $src_w = round(imagesx($img) * $scale);
        $src_h = round(imagesy($img) * $scale);
        $offset_x = round($offset_x * $scale);
        $offset_y = round($offset_y * $scale);
        imagedestroy($img);
        // updating position of each element in svg
        $data = preg_replace('/x="([^"]*)"/eU', ' "x=\"". ("$1" + '.$offset_x.') ."\"" ', $data);
        $data = preg_replace('/y="([^"]*)"/eU', ' "y=\"". ("$1" + '.$offset_y.') ."\"" ', $data);
        $data = preg_replace('/rotate\(([\d\.]+) ([\d\.]+) ([\d\.]+)\)/eU', ' "rotate($1 ". ("$2" + '.$offset_x.') ." ". ("$3" + '.$offset_y.') .")" ', $data);

        $palette = $this->getPaletteSize($data);
        $data = $this->setPaletteSize($data, $src_w, $src_h);

        $this->_backgroundData = array(
            //xml string witch will apply background to svg
            'image_xml' => '<image xlink:href="'.$backgroundImage.'" preserveAspectRatio="none" height="'.$src_h.'" width="'.$src_w.'" y="0" x="0"></image>',
            //mask xml - will be applied if mask is not added to image, will hide all non-printable area places
            'mask_xml'  => '<defs><mask id="fademask" x="0" y="0" width="'.$src_w.'" height="'.$src_h.'">'.
                    '<rect x="'.$offset_x.'" y="'.$offset_y.'" width="'.$palette['width'].'" height="'.$palette['height'].'" fill="white" />'.
                '</mask></defs>',
            'width'     => $src_w,
            'height'    => $src_h,
            'offset_x'  => $offset_x,
            'offset_y'  => $offset_y,
        );
        return $data;
        #return $this;
    }

    public function applyBackground( $data )
    {
        if(is_null($this->_backgroundData)) {
            return $data;
        }
        if ( $this->_maskApplied ) {
            //if mask were applied - we add only background image
            $data = preg_replace('|<\/defs>|', '</defs>'.$this->_backgroundData['image_xml'], $data);
        } else {
            //we need to create our own mask
            $replace = $this->_backgroundData['mask_xml'] . $this->_backgroundData['image_xml'];
            $data = preg_replace('/<defs([^>]*)>(.*)<\/defs>(.*)<\/svg>/U',$replace . '<g  mask="url(#fademask)">$3</g></svg>',$data);
            $data = preg_replace('/<defs([^>]*)\/>(.*)<\/svg>/U',$replace . '<g  mask="url(#fademask)">$2</g></svg>',$data);
        }
        return $data;
    }
    
    public function resetMaskForPng($data)
    {
        $data = preg_replace('/1x1_black.png/U','1x1_white.png',$data);
        $data = preg_replace('/black_mask_inverted.png/U','white_mask_inverted.png',$data);
        return $data;
    }
    
    public function resetMaskForPDF($data)
    {
        $data = preg_replace('/1x1_black.png/U','1x1_white.png',$data);
        $data = preg_replace('/black_mask_inverted.png/U','white_pdf_mask_inverted.png',$data);
        return $data;
    }
    public function addWhiteFontForPDF($data)
    {
        $palette = $this->getPaletteSize($data);
        
        $data = preg_replace('/<defs[^>]*\/>/U','<defs/><image x="-20" y="-20" width="'.($palette['width']+40).'" height="'.($palette['height']+40).'" preserveAspectRatio="none" href="1x1_white_fill.png" />',$data);
        
        $data = preg_replace('/<\/defs>/U','</defs><image x="-20" y="-20" width="'.($palette['width']+40).'" height="'.($palette['height']+40).'" preserveAspectRatio="none" href="1x1_white_fill.png" />',$data);
        $data = str_replace ('1x1_white.png','1x1_white_fill.png',$data);
        
        return $data;
    }
    private function _changeUrlToImage($sData)
    { 
        $oXml = new DOMDocument();
        
        $oXml->loadXML($sData);
        $oSvg = $oXml->getElementsByTagName('svg')->item(0);
        $oImages = $oSvg->getElementsByTagName('image');
        if(!$oImages->length > 0)
        {
            return $sData;
        }
        foreach($oImages as $oImage)
        {
            $sImg = Mage::getModel('aitcg/image_converter')->imageToBase64($this->_getPathFromUrl($oImage->getAttribute('xlink:href')));
            $oImage->setAttribute('xlink:href',$sImg);
        }
        //die();
        return $oXml->saveXML();
    }
    
    
    private function _getPathFromUrl($url)
    {
        if(basename($url) == 'white_mask_inverted.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'masks_created' .DS.$part. DS.basename($url);
        }
            
        if(basename($url) == 'white_pdf_mask_inverted.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'masks_created' .DS.$part. DS.basename($url);
        }
        
        if(basename($url) == 'black_mask_inverted.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'masks_created' .DS.$part. DS.basename($url);
        }
            
        
        if(basename($url) == '1x1_white.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'mask' .DS.'alpha'. DS.basename($url);
        }
        if(basename($url) == '1x1_white_fill.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'mask' .DS.'alpha'. DS.basename($url);
        }
        if(basename($url) == '1x1_black.png')
        {
            $part = dirname($url);
            $part =  substr($part, strrpos($part, '/')+1);
            return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'mask' .DS.'alpha'. DS.basename($url);
        }
        $data = parse_url($url);
        if(isset($data['path'])) {
            $check = array(
                'media'.DS.'catalog', //checking if current image is from product images and located in media/catalog (default product image) folder
                'media'.DS.'custom_product_preview', //checking if current image is from product images and located in media/custom_product_preview folder (saved images for CPP module)
            );
            foreach($check as $path) {
                $pos = strpos($data['path'], $path);
                if($pos !== false) {
                    $folder = trim(substr($data['path'], $pos + 5 ), DS);
                    return Mage::getBaseDir('media') . DS . $folder;
                }
            }
        }
        return Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'quote' . DS.basename($url);
        
    }
}