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
 * Create delivery_time attribute
 * Update delivery_time attribute properties
 * Add is_required column in checkout_agreement
 */

$installer = $this;
$this->startSetup();

/**
 * Create delivery_time attribute
 */
$installer->addAttribute(
    'catalog_product',
    'delivery_time',
    array(
        'label'                      => 'Delivery Time',
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
 * Update delivery_time attribute properties
 */
$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'is_comparable',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'is_visible_on_front',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'is_visible_in_advanced_search',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'used_in_product_listing',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'is_html_allowed_on_front',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'delivery_time',
    'is_visible_on_checkout',
    true
);

$installer->updateAttribute(
    'catalog_product',
    'short_description',
    'is_visible_on_checkout',
    true
);

/**
 * Create is_required column in checkout_agreement table
 */
if ($installer->tableExists('checkout/agreement')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('checkout/agreement'),
        'is_required',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '1',
            'comment'   => 'Agreement is Required'
        )
    );
}

$installer->endSetup();
