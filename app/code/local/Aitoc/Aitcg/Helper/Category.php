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
class Aitoc_Aitcg_Helper_Category extends Aitoc_Aitcg_Helper_Abstract
{
    public function getPredefinedCatsOptionHtml($ids = null)
    {
        $model = Mage::getModel('aitcg/category');
        $collection = $model->getCollection();
        $return = '';
        if($ids!==null)
        {
            $collection->addFieldToFilter('category_id',array('in'=>explode(',',$ids)));
        }

        foreach($collection->load() as $category)
        {
            $return .= '\'<option value="'.$category->getCategoryId().'">'.htmlentities($category->getName(), ENT_QUOTES).'</option>\'+'."\r\n";
        }
        
        return $return;
    }
    
    public function getCategoryImagesRadio($category_id, $rand)
    {
            $imageCollection = Mage::getModel('aitcg/category_image')->getCollection()
                    ->addFieldToFilter('category_id',$category_id)
                    ->addFieldToFilter('filename',array('neq'=>''));
            $return = '';
            foreach($imageCollection->load() as $image)
            {
                $return .= '<label style="float: left; padding: 3px; width:135px; height: 135px;"><input type="radio" value="'.$image->getCategoryImageId().'" name="predefined-image'.$rand.'" style="display:none;">'.
                        '<img src="'.$image->getImagesUrl().'preview/'.$image->getFilename().'"   onclick="$(this).siblings()[0].click();$(this).ancestors()[0].ancestors()[0].descendants().invoke(\'setStyle\',{borderColor: \'#000000\'});this.style.borderColor=\'#FF0000\';new Effect.Pulsate(this,{pulses: 2,duration:0.5});"'.
                        'onmouseover="this.style.zIndex = \'5000\';new Effect.Morph(this,  {style:\'width:150px; height: 150px;\', duration:0.1});"  onmouseout="this.style.zIndex = \'0\';new Effect.Morph(this,  {style:\'width:135px; height: 135px;\', duration:0.1});" style="border: 1px solid; position:absolute;"></label>';
            }
    
            return $return;
    }
    
    public function copyPredefinedImage($id)
    {
        $path = Mage::getBaseDir('media') . DS . 'custom_product_preview' . DS . 'quote' . DS;
        $image = Mage::getModel('aitcg/category_image')->load($id);
        
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
    
}