<?php

$installer = $this;
$installer->startSetup();

// Create custom attribute to show/hide price when module functionality is enabled
try {
	$installer->addAttribute('catalog_product', 'blugento_cart_price', array(
		'type'                       => 'int',
		'backend'                    => '',
		'frontend'                   => '',
		'label'                      => 'Hide price if custom Add to Cart Module Enabled',
		'input'                      => 'select',
		'class'                      => '',
		'source'                     => 'eav/entity_attribute_source_boolean',
		'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'                    => true,
		'required'                   => false,
		'filterable'                 => false,
		'is_configurable'            => false,
		'searchable'                 => false,
		'comparable'                 => false,
		'visible_on_front'           => false,
		'visible_in_advanced_search' => false,
		'used_in_product_listing'    => true,
		'is_html_allowed_on_front'   => false,
		'group'                      => 'General',
		'sort_order'                 => '1001'
	));
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();
