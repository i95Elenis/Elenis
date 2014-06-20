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
class Aitoc_Aitcg_Helper_Font extends Aitoc_Aitcg_Helper_Abstract
{
    public function getFontOptionHtml()
    {
        $collection = Mage::getModel('aitcg/font')
                ->getCollection()
                ->addFieldToFilter('filename', array('neq'=>''))
                ->addFieldToFilter('status', '1');

        $optionsHtml = '<option value="">' . Mage::helper('aitcg')->__('Select font...') . '</option>';
        foreach ($collection->load() as $font)
        {
            $optionsHtml .= '<option value="'.$font->getFontId().'">' . $font->getName() . '</option>';
        }
        
        return $optionsHtml;
    }
    
    public function getFontPreview($font)
    {
        $im = imagecreatetruecolor(550, 30);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 549, 29, $white);

        $text = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        imagettftext($im, 20, 0, 10, 20, $black, $font, $text);
        ob_start();
        imagepng($im);
        $image = ob_get_contents();
        ob_clean();
        imagedestroy($im);
        
        return 'data:image/png;base64,'.base64_encode($image);
    }
    
    public function getTextImage($font, $text, $color,$outline, $shadow)
    { 
        $resolution = $this->getResolution();
        $coords = imagettfbbox($this->getResolution(), 0, $font, $text);
        #return print_r($coords,1);

        $wightoutline = empty($outline['wight'])?0:$outline['wight'];
        $shadowX = empty($shadow['x'])?0:$shadow['x'];
        $shadowY = empty($shadow['y'])?0:$shadow['y'];

        $im = $this->_getEmptyImage($coords[2]-$coords[0]+11+$wightoutline*2+abs($shadowX), $coords[1]-$coords[5]+$resolution*0.75+$wightoutline*2+abs($shadowY));
        $color = $this->_getColorOnImage($im, $color);

        $shadowX = ($shadow['x']>0)?0:-$shadow['x'];
        $shadowY = ($shadow['y']>0)?0:-$shadow['y'];
        $X= $coords[0]+5+$wightoutline+$shadowX;
        $Y= $coords[1]-$coords[5]+$wightoutline+$shadowY;
        /*
        imagefilledrectangle($im, 0, 0, $coords[2]-$coords[0]+10, $coords[1]-$coords[5]+10, $empty);*/

        //$im = $this->_dropshadow($im, 0, 0, 50);
        if(!empty($shadow) )
        {

            $shadowcolor = $this->_getColorOnImage($im, $shadow['color'],$shadow['alpha']);

           // $shadowcolor =imagecolorallocatealpha($im, 0,0,0,$shadow['alpha']);

            imagettftext($im, $resolution, 0, $X+$shadow['x'], $Y+$shadow['y'], $shadowcolor, $font, $text);

        }
        if(!empty($outline) )
        {
            $outlinecolor = $this->_getColorOnImage($im, $outline['color']);
            //$resolution_outline = $outline['wight'];

            $this->_imagettftextoutline($im, $resolution, 0, $X, $Y, $outlinecolor, $font, $text, $wightoutline);

        }
        imagettftext($im, $resolution, 0, $X, $Y, $color, $font, $text);

        $path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'quote' . DS;
        $filename = $this->_getUniqueFilename($path);
        ob_start();
        imagepng($im);
        $image = ob_get_contents();
        ob_clean();
        imagedestroy($im);
        file_put_contents($path.$filename, $image);
        return $filename;
    }

    protected function _imagettftextoutline(&$im,$size,$angle,$x,$y, &$outlinecol,$fontfile,$text,$width) {
        // For every X pixel to the left and the right
        $widthIterration = ceil($width/10);//for very big width
        for ($xc=$x-abs($width);$xc<=$x+abs($width);$xc+=$widthIterration) {
            // For every Y pixel to the top and the bottom
            for ($yc=$y-abs($width);$yc<=$y+abs($width);$yc+=$widthIterration) {
                $text1 = imagettftext($im,$size,$angle,$xc,$yc,$outlinecol,$fontfile,$text);
            }
        }
    }
   /* protected function _dropshadow($im, $shadow, $outline){
        // Create an image the size of the original plus the size of the drop shadow

    }*/

    protected function _getUniqueFilename($path)
    {
        do
        {
            $filename = Mage::helper('aitcg')->uniqueFilename('.png');
        }while(file_exists($path.$filename));
        
        return $filename;
    }


    protected function _getColorOnImage($image, $color, $alpha=0)
    {
        $color = str_split($color,2);
        array_walk($color, create_function('&$n','$n = hexdec($n);'));
        
        $color[0]=isset($color[0])?$color[0]:0;
        $color[1]=isset($color[1])?$color[1]:0;
        $color[2]=isset($color[2])?$color[2]:0;
            
        return imagecolorallocatealpha($image, $color[0], $color[1], $color[2],$alpha);
        
    }
    public function getResolution()
    {
        return (int) Mage::getStoreConfig('catalog/aitcg/aitcg_font_resolution_predefine');
    }
    
    private function _getEmptyImage($sWidth, $sHeight)
    {
        $rImage = imagecreatetruecolor((int)$sWidth, (int)$sHeight);
        imagesavealpha($rImage, true);
        $iBackgroundColor = imagecolorallocatealpha($rImage, 0, 0, 0, 127);
        imagefill($rImage, 0, 0, $iBackgroundColor);
        return $rImage;
    }        
    
    
}