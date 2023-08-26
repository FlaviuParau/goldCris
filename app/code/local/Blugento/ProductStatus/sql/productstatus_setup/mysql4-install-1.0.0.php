<?php

$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute("catalog_product", "product_status", array(
    'group'           => 'General',
    'label'           => 'Product Status',
    'input'           => 'text',
    'type'            => 'varchar',
    'required'        => 0,
    'visible'         => 1,
    'is_visible_on_front'=> 1,
    'filterable'      => 0,
    'searchable'      => 0,
    'comparable'      => 0,
    'user_defined'    => 1,
    'is_configurable' => 0,
    'used_in_product_listing' => 1,
    'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'note'            => ''
));

$installer->updateAttribute(
    'catalog_product',
    'product_status',
    'is_visible_on_front',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'product_status',
    'used_in_product_listing',
    true
);

$installer->endSetup();
