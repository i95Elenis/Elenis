<?php

/**
 *
 *
 * @category       
 * @package        Elenis_CustomCategoryAttribute
 * @Description    
 * @author         
 * @copyright      
 * @license        
 */
$this->startSetup();
$this->addAttribute('catalog_category', 'custom_image_attribute1', array(
    'group' => 'General Information',
    'input' => 'image',
    'type' => 'varchar',
    'label' => 'Custom Image 1',
    'backend' => 'catalog/category_attribute_backend_image',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'custom_image_attribute2', array(
    'group' => 'General Information',
    'input' => 'image',
    'type' => 'varchar',
    'label' => 'Custom Image 2',
    'backend' => 'catalog/category_attribute_backend_image',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'custom_image_attribute3', array(
    'group' => 'General Information',
    'input' => 'image',
    'type' => 'varchar',
    'label' => 'Custom Image 3',
    'backend' => 'catalog/category_attribute_backend_image',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'category_sku1', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'input' => 'select',
    'label'=>'Sku 1',
    'note'=>'provide valid sku for custom image attribute1 ',
    'source' => 'Elenis_CustomCategoryAttribute/Category_Attribute_Backend',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'category_sku2', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'input' => 'select',
    'label'=>'Sku 2',
    'note'=>'provide valid sku for custom image attribute2 ',
    'source' => 'Elenis_CustomCategoryAttribute/Category_Attribute_Backend',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'category_sku3', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'input' => 'select',
    'label'=>'Sku 3',
    'note'=>'provide valid sku for custom image attribute3 ',
    'source' => 'Elenis_CustomCategoryAttribute/Category_Attribute_Backend',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->addAttribute('catalog_category', 'link_category', array(
    'group' => 'General Information',
    'type' => 'varchar',
    'input' => 'text',
    'label'=>'Category Image Hyperlink',
    'note'=>'This will link to category image hyperlink ',
   // 'source' => 'Elenis_CustomCategoryAttribute/Category_Attribute_Backend',
    'backend'       => '',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->endSetup();
