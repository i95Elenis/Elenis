<?php
class Onlinebrand_Minimumqty_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
  public function getDefaultEntities(){
    return array(
      'catalog_product'                => array(
        'entity_model'                   => 'catalog/product',
        'attribute_model'                => 'catalog/resource_eav_attribute',
        'table'                          => 'catalog/product',
        'additional_attribute_table'     => 'catalog/eav_attribute',
        'entity_attribute_collection'    => 'catalog/product_attribute_collection',
        'attributes'                     => array(
          'minqty'               => array(
            'type'                       => 'int',
            'label'                      => 'Minimum qty',
            'input'                      => 'text',
            'sort_order'                 => 5,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'searchable'                 => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing'    => true,
            'used_for_sort_by'           => true,
          ),
        )
      ),
    );
  }
}