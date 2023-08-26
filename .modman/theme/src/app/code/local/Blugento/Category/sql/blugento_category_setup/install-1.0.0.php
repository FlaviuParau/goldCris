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
 * @package     Blugento_Category
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->removeAttribute('catalog_category', 'megamenu_image');
$installer->addAttribute('catalog_category', 'blugento_megamenu_image', array(
    'group'                    => 'Blugento',
    'label'                    => 'Megamenu Image',
    'input'                    => 'image',
    'type'                     => 'varchar',
    'backend'                  => 'blugento_category/category_attribute_backend_image_megamenu',
    'global'                   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'                  => true,
    'required'                 => false,
    'user_defined'             => true,
    'order'                    => 10
));
$installer->addAttribute('catalog_category', 'blugento_og_image', array(
    'group'                    => 'Blugento',
    'label'                    => 'OG Image',
    'input'                    => 'image',
    'type'                     => 'varchar',
    'backend'                  => 'blugento_category/category_attribute_backend_image_og',
    'global'                   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'                  => true,
    'required'                 => false,
    'user_defined'             => true,
    'order'                    => 20
));
$installer->endSetup();
