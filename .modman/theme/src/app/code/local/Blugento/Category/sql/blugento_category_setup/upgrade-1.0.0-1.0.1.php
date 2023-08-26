<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'blugento_cat_left_cms_block', array(
	'group'            => 'Blugento',
	'label'            => 'Left CMS Block',
	'input'            => 'select',
	'type'             => 'int',
	'source'           => 'catalog/category_attribute_source_page',
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'          => true,
	'required'         => false,
	'user_defined'     => true,
	'visible_on_front' => true,
	'note'             => 'This applies only to first level categories',
	'order'            => 40
));

$installer->addAttribute('catalog_category', 'blugento_cat_right_cms_block', array(
	'group'            => 'Blugento',
	'label'            => 'Right CMS Block',
	'input'            => 'select',
	'type'             => 'int',
	'source'           => 'catalog/category_attribute_source_page',
	'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'          => true,
	'required'         => false,
	'user_defined'     => true,
	'visible_on_front' => true,
	'note'             => 'This applies only to first level categories',
	'order'            => 50
));

$leftIdentifier = 'blugento-category-left-cms-block';
$block          = Mage::getModel('cms/block');

$leftBlockId  = null;
$rightBlockId = null;

if (!$block->load($leftIdentifier)->getIdentifier()) {
	$leftContent = Mage::helper('blugento_category')->__('<p>Custom CMS block to be shown in Mega Menu on the left side</p>');
	$block->setTitle('Blugento Category Left CMS Block');
	$block->setIdentifier($leftIdentifier);
	$block->setStores(array(0));
	$block->setIsActive(1);
	$block->setContent($leftContent);
	$block->save();
	$leftBlockId = $block->getBlockId();
}

$rightIdentifier = 'blugento-category-right-cms-block';
$block           = Mage::getModel('cms/block');

if (!$block->load($rightIdentifier)->getIdentifier()) {
	$rightContent = Mage::helper('blugento_category')->__('<p>Custom CMS block to be shown in Mega Menu on the right side</p>');
	$block->setTitle('Blugento Category Right CMS Block');
	$block->setIdentifier($rightIdentifier);
	$block->setStores(array(0));
	$block->setIsActive(1);
	$block->setContent($rightContent);
	$block->save();
	$rightBlockId = $block->getBlockId();
}

$entityTypeId      = $installer->getEntityTypeId('catalog_category');
$leftAttributeId   = $installer->getAttributeId($entityTypeId, 'blugento_cat_left_cms_block');
$rightAttributeId  = $installer->getAttributeId($entityTypeId, 'blugento_cat_right_cms_block');
$categoryIntTable  = $installer->getTable('catalog_category_entity_int');
$categoryTable     = $installer->getTable('catalog_category_entity');

$query = "SELECT entity_id FROM {$categoryTable}";
$data  = $installer->getConnection()->fetchAll($query);

foreach ($data as $row) {
	$query = "INSERT INTO {$categoryIntTable} (`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES ({$entityTypeId}, {$leftAttributeId}, 0, {$row['entity_id']}, {$leftBlockId});";
	$installer->run($query);
}

$query = "SELECT entity_id FROM {$categoryTable}";
$data  = $installer->getConnection()->fetchAll($query);

foreach ($data as $row) {
	$query = "INSERT INTO {$categoryIntTable} (`entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES ({$entityTypeId}, {$rightAttributeId}, 0, {$row['entity_id']}, {$rightBlockId});";
	$installer->run($query);
}

$installer->endSetup();
