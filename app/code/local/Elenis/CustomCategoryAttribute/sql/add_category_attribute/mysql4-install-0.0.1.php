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
 'visible_on_front' => true,
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
 'visible_on_front' => true,
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
 'visible_on_front' => true,
 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
$this->endSetup();
