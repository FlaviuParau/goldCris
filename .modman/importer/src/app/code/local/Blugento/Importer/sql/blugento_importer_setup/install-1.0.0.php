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
 * Create 'blugento_importer_profile' table
 */
if ($installer->getConnection()->isTableExists($installer->getTable('blugento_importer/importer')) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('blugento_importer/importer'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Name')
        ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Entity Type')
        ->addColumn('behavior', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
            'nullable' => true,
            'default' => '',
        ), 'Behavior')
        ->addColumn('processimages', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Process Images')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Store ID')
        ->addColumn('data_source', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Data Source')
        ->addColumn('file_name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
            'nullable' => true,
        ), 'File Name')
        ->addColumn('file_path', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
            'nullable' => true,
        ), 'File Path')
        ->addColumn('remote_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Remote Url')
        ->addColumn('file_format', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'nullable' => true,
        ), 'File Format')
        ->addColumn('delimiter', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
            'nullable' => true,
        ), 'Delimiter')
        ->addColumn('enclosure', Varien_Db_Ddl_Table::TYPE_TEXT, 5, array(
            'nullable' => true,
        ), 'Enclosure')
        ->addColumn('default_values', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Default Values')
        ->addColumn('default_website', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Default Website')
        ->addColumn('default_attribute_set_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Default Attribute Set Id')
        ->addColumn('default_product_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Default Product Type')
        ->addColumn('default_weight', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Default Weight')
        ->addColumn('default_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Default Status')
        ->addColumn('default_visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Default Visibility')
        ->addColumn('default_tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Default Tax Class Id')
        ->addColumn('category_separator', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'nullable' => true,
        ), 'Category Separator')
        ->addColumn('gallery_separator', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
            'nullable' => true,
        ), 'Gallery Separator')
        ->addColumn('bolean_true', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Bolean True Values')
        ->addColumn('bolean_false', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Bolean False Values')
        ->addColumn('map_attributes_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => true,
        ), 'Map Attributes Data')
        ->addColumn('cron_run_frequency', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
        ), 'Cron Run Frequency')
        ->addColumn('last_run_time', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
        ), 'Cron Last Run Time')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
        ), 'Updated At')
        ->setComment('Importer Data Table');
    $installer->getConnection()->createTable($table);
}

/**
 * Create 'blugento_importer_history' table
 */
if ($installer->getConnection()->isTableExists($installer->getTable('blugento_importer/history')) != true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('blugento_importer/history'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Id')
        ->addColumn('profile_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Profile Id')
        ->addColumn('profile_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Profile Name')
        ->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
        ), 'Entity Type')
        ->addColumn('result', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Result')
        ->addColumn('run_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Run At')
        ->setComment('Importer Data Table');
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
