<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
if (!$installer->getAttributeId('catalog_category', 'googleshopping_exclude')) {
    $installer->addAttribute(
        'catalog_category', 'googleshopping_exclude', array(
            'group'        => 'Feeds',
            'input'        => 'select',
            'type'         => 'int',
            'source'       => 'eav/entity_attribute_source_boolean',
            'label'        => 'Exclude from Google Shopping Product Type',
            'required'     => false,
            'user_defined' => true,
            'visible'      => true,
            'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'position'     => 99,
        )
    );
}

$installer->endSetup();

