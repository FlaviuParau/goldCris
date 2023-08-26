<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'blugento_background_image', array(
	'group'            => 'Blugento',
	'label'            => 'Background Image',
	'input'            => 'image',
	'type'             => 'varchar',
	'backend'          => 'catalog/category_attribute_backend_image',
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'          => true,
	'required'         => false,
	'user_defined'     => true,
	'visible_on_front' => true,
	'order'            => 30
));

$installer->addAttribute('catalog_category', 'blugento_category_slider', array(
	'group'            => 'Blugento',
	'label'            => 'Category Slider',
	'input'            => 'select',
	'type'             => 'varchar',
	'source'           => 'blugento_sliders/attribute_source_group',
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'          => true,
	'required'         => false,
	'user_defined'     => true,
	'visible_on_front' => true,
	'order'            => 40
));

$installer->endSetup();