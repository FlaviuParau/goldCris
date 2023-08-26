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
 * @package     Blugento_Importer
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Blugento_Importer_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create 'blugento_importer_images' table
 */
if ($installer->getConnection()->isTableExists($installer->getTable('blugento_importer/images')) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('blugento_importer/images'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Id')
        ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'default'  => '0',
        ),'Profile Id')
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Entity Id')
        ->addColumn('image_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Image Path')
        ->addColumn('image_label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Image Label')
        ->addColumn('image_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Image Type')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'default'  => '0',
        ),'Store Id')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Created At')
        ->setComment('Importer Data Table');
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
