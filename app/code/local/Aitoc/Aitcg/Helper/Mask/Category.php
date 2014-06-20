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
class Aitoc_Aitcg_Helper_Mask_Category extends Aitoc_Aitcg_Helper_Abstract
{
    public function getMaskCatsOptionHtml($ids = null)
    {
        $model = Mage::getModel('aitcg/mask_category');
        $collection = $model->getCollection();
        $return = '';
        if($ids!==null)
        {
            $collection->addFieldToFilter('id',array('in'=>explode(',',$ids)));
        }

        foreach($collection->load() as $category)
        {
            $return .= '\'<option value="'.$category->getId().'">'.htmlentities($category->getName(), ENT_QUOTES).'</option>\'+'."\r\n";
        }
        
        return $return;
    }
    
    public function getCategoryMaskRadio($category_id, $rand)
    {
            $maskCollection = Mage::getModel('aitcg/mask')->getCollection()
                    ->addFieldToFilter('category_id',$category_id)
                    ->addFieldToFilter('filename',array('neq'=>''));
            $return = '';
            foreach($maskCollection->load() as $mask)
            {
                $return .= '<label style="float: left; padding: 3px; width:135px; height: 135px;"><input type="radio" value="'.$mask->getId().'" name="mask'.$rand.'" style="display:none;">'.
                        '<img src="'.$mask->getImagesUrl().'preview/'.$mask->getFilename().'"   onclick="$(this).siblings()[0].click();$(this).ancestors()[0].ancestors()[0].descendants().invoke(\'setStyle\',{borderColor: \'#000000\'});this.style.borderColor=\'#FF0000\';new Effect.Pulsate(this,{pulses: 2,duration:0.5});"'.
                        'onmouseover="this.style.zIndex = \'5000\';new Effect.Morph(this,  {style:\'width:150px; height: 150px;\', duration:0.1});"  onmouseout="this.style.zIndex = \'0\';new Effect.Morph(this,  {style:\'width:135px; height: 135px;\', duration:0.1});" style="border: 1px solid; position:absolute;"></label>';
            }
    
            return $return;
    }
    
    public function copyPredefinedImage($id)
    {
        $path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'mask' . DS . 'alpha' . DS;
        $image = Mage::getModel('aitcg/mask')->load($id);
        
        $fileName = $image->getFilename();
        $fileNameExploded = explode('.',$fileName);
        $ext = '.'.array_pop($fileNameExploded);
        
        $filename = Mage::helper('aitcg')->uniqueFilename($ext);
        while(file_exists($path.$filename))
        {
            $filename = Mage::helper('aitcg')->uniqueFilename($ext);
        }
        
        
        @copy($image->getImagesPath().$image->getFilename(),$path.$filename);
        return $filename;
    }
    
   /* public function getMaskAlpha($id)
    {
        //$path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'mask' . DS . 'alpha' . DS;
        $image = Mage::getModel('aitcg/mask')->load($id);
        
        $filename = $image->getImagesUrl().DS . 'alpha' . DS.$image->getFilename();
        
        return $filename;
    }*/
}