<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
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

$installer->endSetup();
