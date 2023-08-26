<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'short_description', array(
	'group'            => 'Blugento',
	'label'            => 'Short Description',
	'input'            => 'textarea',
	'type'             => 'text',
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'          => true,
	'required'         => false,
	'user_defined'     => false,
	'visible_on_front' => true,
	'wysiwyg_enabled'  => true,
	'order'            => 60
));

$installer->endSetup();