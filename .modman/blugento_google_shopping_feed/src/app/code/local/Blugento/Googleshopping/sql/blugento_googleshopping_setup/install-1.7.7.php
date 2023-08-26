<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

$attributeSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();
$attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);
$installer->addAttributeGroup('catalog_product', $attributeSet->getAttributeSetName(), 'Google Shopping', 1000);

if (!$installer->getAttributeId('catalog_product', 'googleshopping_exclude')) {
    $installer->addAttribute(
        'catalog_product', 'googleshopping_exclude', array(
            'group'                      => 'Google Shopping',
            'input'                      => 'select',
            'type'                       => 'int',
            'source'                     => 'eav/entity_attribute_source_boolean',
            'label'                      => 'Exclude for Google Shopping',
            'visible'                    => true,
            'required'                   => false,
            'user_defined'               => true,
            'searchable'                 => false,
            'filterable'                 => false,
            'comparable'                 => false,
            'visible_on_front'           => true,
            'used_in_product_listing'    => true,
            'visible_in_advanced_search' => false,
            'is_html_allowed_on_front'   => false,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        )
    );
}

if (!$installer->getAttributeId('catalog_product', 'googleshopping_condition')) {
    $installer->addAttribute(
        'catalog_product', 'googleshopping_condition', array(
            'group'                      => 'Google Shopping',
            'input'                      => 'select',
            'type'                       => 'int',
            'backend'                    => 'eav/entity_attribute_backend_array',
            'option'                     => array(
                'value' => array(
                    'new'         => array('New'),
                    'refurbished' => array('Refurbished'),
                    'used'        => array('Used')
                )
            ),
            'default'                    => 'new',
            'label'                      => 'Product Condition',
            'visible'                    => true,
            'required'                   => false,
            'user_defined'               => true,
            'searchable'                 => false,
            'filterable'                 => false,
            'comparable'                 => false,
            'visible_on_front'           => true,
            'used_in_product_listing'    => true,
            'visible_in_advanced_search' => false,
            'is_html_allowed_on_front'   => false,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        )
    );
}

if (!$installer->getAttributeId('catalog_product', 'googleshopping_category')) {
    $installer->addAttribute(
        'catalog_product', 'googleshopping_category', array(
            'group'                      => 'Google Shopping',
            'input'                      => 'text',
            'type'                       => 'varchar',
            'backend'                    => '',
            'label'                      => 'Google Shopping Product Category',
            'note'                       => 'Overwrite the Google Shopping Category from your category configuration and default configuration on product level with this open text field. You can implement the full path or the ID from the Google Shopping requirements.',
            'visible'                    => true,
            'required'                   => false,
            'user_defined'               => true,
            'searchable'                 => false,
            'filterable'                 => false,
            'comparable'                 => false,
            'visible_on_front'           => true,
            'used_in_product_listing'    => true,
            'visible_in_advanced_search' => false,
            'is_html_allowed_on_front'   => false,
            'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        )
    );
}

if (!$installer->getAttributeId('catalog_category', 'googleshopping_category')) {
    $installer->addAttribute(
        'catalog_category', 'googleshopping_category', array(
            'group'        => 'Feeds',
            'input'        => 'text',
            'type'         => 'varchar',
            'label'        => 'Google Product Category',
            'required'     => false,
            'user_defined' => true,
            'visible'      => true,
            'global'       => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        )
    );
}

$installer->endSetup();