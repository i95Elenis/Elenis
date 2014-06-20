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
class Aitoc_Aitcg_Model_Mysql4_Rewrite_Catalog_Product_Option_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection
{

    public function addPriceToResult($store_id)
    {
        parent::addPriceToResult($store_id);
        
        $this->getSelect()
            ->joinLeft(array('default_option_aitimage'=>$this->getTable('catalog/product_option_aitimage')),
                '`default_option_aitimage`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`default_option_aitimage`.store_id=?',0),
                array(
                    //'default_price'=>'price','default_price_type'=>'price_type'
                    'default_image_template_id' => 'image_template_id',
                    'default_area_size_x'   => 'area_size_x',
                    'default_area_size_y'   => 'area_size_y',
                    'default_area_offset_x' => 'area_offset_x',
                    'default_area_offset_y' => 'area_offset_y',
                    'default_use_text' => 'use_text',
                    'default_use_user_image' => 'use_user_image',
                    'default_predefined_cats' => 'predefined_cats',  
                    'default_use_masks' => 'use_masks',
                    'default_masks_cat_id' => 'masks_cat_id',  
                    'default_mask_location' => 'mask_location',  
                    'default_use_predefined_image' => 'use_predefined_image',  
                    'default_allow_colorpick' => 'allow_colorpick',  
                    'default_text_length' => 'text_length',
                    'default_allow_text_distortion' => 'allow_text_distortion',
                    'default_allow_predefined_colors' => 'allow_predefined_colors',
                    'default_color_set_id' => 'color_set_id',
                   # 'default_img_data'         => 'img_data',
                ))
            ->joinLeft(array('store_option_aitimage'=>$this->getTable('catalog/product_option_aitimage')),
                '`store_option_aitimage`.option_id=`main_table`.option_id AND '.$this->getConnection()->quoteInto('`store_option_aitimage`.store_id=?', $store_id),
                array(
                //'store_price'=>'price','store_price_type'=>'price_type',
                    'store_image_template_id' => 'image_template_id',
                    'store_area_size_x'   => 'area_size_x',
                    'store_area_size_y'   => 'area_size_y',
                    'store_area_offset_x' => 'area_offset_x',
                    'store_area_offset_y' => 'area_offset_y',
                    'store_use_text' => 'use_text',
                    'store_use_user_image' => 'use_user_image',
                    'store_predefined_cats' => 'predefined_cats',  
                    'store_use_masks' => 'use_masks',
                    'store_masks_cat_id' => 'masks_cat_id',  
                    'store_mask_location' => 'mask_location',  
                    'store_use_predefined_image' => 'use_predefined_image',
                    'store_allow_colorpick' => 'allow_colorpick',  
                    'store_text_length' => 'text_length',
                    'store_allow_text_distortion' => 'allow_text_distortion',
                    'store_allow_predefined_colors' => 'allow_predefined_colors',
                    'store_color_set_id' => 'color_set_id',
                   # 'default_img_data'         => 'img_data',
                    
                    'image_template_id' => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.image_template_id,`default_option_aitimage`.image_template_id)'),
                    'area_size_x'       => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.area_size_x,`default_option_aitimage`.area_size_x)'),
                    'area_size_y'       => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.area_size_y,`default_option_aitimage`.area_size_y)'),
                    'area_offset_x'     => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.area_offset_x,`default_option_aitimage`.area_offset_x)'),
                    'area_offset_y'     => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.area_offset_y,`default_option_aitimage`.area_offset_y)'),
                    'use_text'          => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.use_text,`default_option_aitimage`.use_text)'),
                    'use_user_image'    => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.use_user_image,`default_option_aitimage`.use_user_image)'),
                    'use_predefined_image'  => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.use_predefined_image,`default_option_aitimage`.use_predefined_image)'),
                    'use_masks'   => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.use_masks,`default_option_aitimage`.use_masks)'),                    
                    'masks_cat_id'  => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.masks_cat_id,`default_option_aitimage`.masks_cat_id)'),
                    'mask_location'  => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.mask_location,`default_option_aitimage`.mask_location)'),
                    'predefined_cats'   => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.predefined_cats,`default_option_aitimage`.predefined_cats)'),                    
                    'allow_colorpick'   => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.allow_colorpick,`default_option_aitimage`.allow_colorpick)'),                    
                    'text_length'       => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.text_length,`default_option_aitimage`.text_length)'),
                    'allow_text_distortion' => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.allow_text_distortion,`default_option_aitimage`.allow_text_distortion)'),
                    'allow_predefined_colors'   => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.allow_predefined_colors,`default_option_aitimage`.allow_predefined_colors)'),
                    'color_set_id'      => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.color_set_id,`default_option_aitimage`.color_set_id)'),
                    
                    #'img_data'         => new Zend_Db_Expr('IFNULL(`store_option_aitimage`.use_text,`default_option_aitimage`.img_data)'),                    
                //'price' =>   new Zend_Db_Expr('IFNULL(`store_option_price`.price,`default_option_price`.price)'),
                //'price_type'=>new Zend_Db_Expr('IFNULL(`store_option_price`.price_type,`default_option_price`.price_type)')
                ));
        return $this;
    }
}