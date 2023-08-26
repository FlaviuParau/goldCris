<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Setup script
 * Create subtitle attribute
 * Update subtitle attribute properties
 */

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create subtitle attribute
 */
$installer->addAttribute(
    'catalog_product',
    'subtitle',
    array(
        'label'                      => 'Subtitle',
        'input'                      => 'text',
        'required'                   => 0,
        'user_defined'               => 1,
        'default'                    => '',
        'group'                      => 'General',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                    => 1,
        'filterable'                 => 0,
        'searchable'                 => 0,
        'comparable'                 => 1,
        'visible_on_front'           => 1,
        'visible_in_advanced_search' => 1,
        'used_in_product_listing'    => 1,
        'is_html_allowed_on_front'   => 1,
    )
);

/**
 * Update subtitle attribute properties
 */
$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_comparable',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_visible_on_front',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_visible_in_advanced_search',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'used_in_product_listing',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_html_allowed_on_front',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_visible_on_checkout',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'subtitle',
    'is_visible_on_checkout',
    true
);

$installer->endSetup();
