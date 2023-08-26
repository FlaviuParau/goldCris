<?php
/**
 *
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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_visibility',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_tax_class_id',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 225
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_status',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 225
    )
);

$installer->getConnection()->modifyColumn(
    $installer->getTable('blugento_importer/importer'),
    'default_weight',
    array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 225
    )
);

$installer->endSetup();