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
class Aitoc_Aitcg_Helper_Options extends Aitoc_Aitcg_Helper_Abstract
{
    const OPTION_TYPE_AITCUSTOMER_IMAGE = 'aitcustomer_image';
    
    public function checkAitOption($option) 
    {
        /*
                if(! Mage::helper('aitcg/options')->checkAitOption( $option ) )
        {
            return parent::getOptionHtml( $option );            
        }

        */
        //if( is_string($option) && $option != Aitoc_Aitcg_Model_Rewrite_Catalog_Product_Option::OPTION_TYPE_AITCUSTOMER_IMAGE
        //    || is_object($option) && $option->getType() != Aitoc_Aitcg_Model_Rewrite_Catalog_Product_Option::OPTION_TYPE_AITCUSTOMER_IMAGE ) {
        if( is_string($option) && $option != self::OPTION_TYPE_AITCUSTOMER_IMAGE
            || is_object($option) && $option->getType() != self::OPTION_TYPE_AITCUSTOMER_IMAGE ) {
            return false;
        }
        return true;
    }
    
    public function calculateThumbnailDimension($new_size_x, $new_size_y, $full_x, $full_y) 
    {
        if($new_size_x >= $full_x && $new_size_y >= $full_y) {
            $value = array(
                $full_x,
                $full_y,
                "mult" => 1
            );            
        } else if ($full_x > $full_y ) {
            $value = array(
                $new_size_x,
                round($new_size_x / $full_x * $full_y),
                "mult" => round($new_size_x / $full_x,4)
            );
        } else {
            $value = array(
                round($new_size_y / $full_y * $full_x),
                $new_size_y,
                "mult" => round($new_size_y / $full_y,4)
            );                        
        }        
        return $value;
    }
    
    public function updateQuoteTemplateId($id, $quote_option_id, $product_id, $item_id) {
        if($id==0 || $quote_option_id == 0 || $product_id == 0 || $item_id == 0 ) {
            return false;
        }
        $model = Mage::getModel('sales/quote_item_option');
        $model->load($quote_option_id);
        if($model->getProductId()!=$product_id || $model->getItemId()!=$item_id) {
            return false;
        }
        $value = unserialize( $model->getValue() );
        if(isset($value['template_id'])) {
            $old_id = $value['template_id'];
            $value['template_id'] = $id;
            $option_id = intval( str_replace('option_', '', $model->getCode()) );
            $model->setData('value', serialize($value) );

            $model2 = Mage::getModel('sales/quote_item_option');
            $collection = $model2->getCollection();
            $collection->getSelect()
                ->where('item_id=?', $item_id)
                ->where('product_id=?', $product_id)
                ->where('code=?', 'info_buyRequest');
            $collection->load();
            return $this->_setTemplateIdInOption($collection, $option_id, $old_id, $id );
        }
    }
    
    protected function _setTemplateIdInOption($collection, $option_id, $old_id, $id )
    {
        
        if($collection->count() != 1) {
            return false;
        }
        foreach($collection as $data) 
        {
            $value = unserialize( $data->getValue() );
            if(isset($value['options'], $value['options'][$option_id],$value['options'][$option_id]['template_id'])) {
                if($value['options'][$option_id]['template_id'] != $old_id) {
                    return false;
                }
                $value['options'][$option_id]['template_id'] = $id;
                $data->setData('value', serialize($value) );
                $data->save();
                $model->save();
                return true;
            }
        }
    }


    public function replaceAitTemplate( $template ) {
        if(strpos($template, '|||aitcgimage') === false) {
            return $template;
        }
        if(strpos($template, '|||email') === false) {
            $template = preg_replace('!\|\|\|aitcgimage\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|!Uis',
                '<div class="input-box">
                    <div class="aitinput">
                        <p class="no-margin">
                            <strong>'.Mage::helper("aitcg")->__('A custom image was added.  Image preview is available in the order overview page in your account.').'</strong>
                        </p>
                    </div>
                </div>',$template);
        }else{
            $template = preg_replace('!\|\|\|email\|\|\|aitcgimage\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|!Uis',
                '<div class="input-box">
                    <div class="aitinput">
                        <p class="no-margin">
                            <strong>'.Mage::helper("aitcg")->__('A custom image was added.  Image preview is available in the order overview page in your account.').'</strong>
                        </p>
                    </div>
                </div>',$template);
        }
        return $template;
    }

    public function replaceAitTemplateWithText($result) {
        foreach($result as &$option) {
            if($this->checkAitOption( $option['option_type'] ) ) {
                $template = $option['print_value'];
                if(strpos($template, '|||aitcgimage') === false) {
                    continue;
                }
                $template = preg_replace('!\|\|\|aitcgimage\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|([^|]*)\|\|\|!Uis','
                    '.Mage::helper("api")->__('Preview').': $1, 
                    '.Mage::helper("aitcg")->__('The product preview is available in the order overview at your account'),$template);            
                $option['print_value'] = $template;
            }
        }
        return $result;        
    }

}