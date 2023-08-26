<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingPerItem
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * ADDING FIRST ATTRIBUTE
 */
$installer->addAttribute(
    'catalog_product',
    'am_shipping_peritem',
    array(
        'type' => 'decimal',
        'backend' => '',
        'frontend' => '',
        'label' => 'Shipping Rate',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => '',
        'is_configurable' => false
    )
);
$attributeId = $installer->getAttributeId('catalog_product', 'am_shipping_peritem');

foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
    try {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
    } catch (Exception $e) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
    }
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}

$installer->endSetup();
