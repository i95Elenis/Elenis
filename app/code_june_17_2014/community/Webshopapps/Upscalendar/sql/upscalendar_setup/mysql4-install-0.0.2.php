<?php

$installer = $this;

$installer->startSetup();

$installer->run("

select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='catalog_product';

    insert ignore into {$this->getTable('eav_attribute')}
        set entity_type_id 	= @entity_type_id,
        attribute_code 	= 'max_transit',
        backend_type	= 'int',
        frontend_input	= 'text',
        is_required	= 0,
        is_user_defined	= 1,
        frontend_label	= 'Maximum Time in Transit';

    select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='max_transit';

    insert ignore into {$this->getTable('catalog_eav_attribute')}
        set attribute_id 	= @attribute_id,
            is_visible 	= 1,
            used_in_product_listing	= 0,
            is_filterable_in_search	= 0;
");

$entityTypeId = $installer->getEntityTypeId('catalog_product');

$attributeSetArr = $installer->getConnection()->fetchAll("SELECT attribute_set_id FROM {$this->getTable('eav_attribute_set')} WHERE entity_type_id={$entityTypeId}");

$attributeId = $installer->getAttributeId($entityTypeId,'max_transit');

foreach( $attributeSetArr as $attr)
{
    $attributeSetId= $attr['attribute_set_id'];

    $installer->addAttributeGroup($entityTypeId,$attributeSetId,'Shipping','99');

    $attributeGroupId = $installer->getAttributeGroupId($entityTypeId,$attributeSetId,'Shipping');

    $installer->addAttributeToGroup($entityTypeId,$attributeSetId,$attributeGroupId,$attributeId,'99');
};

$installer->endSetup();