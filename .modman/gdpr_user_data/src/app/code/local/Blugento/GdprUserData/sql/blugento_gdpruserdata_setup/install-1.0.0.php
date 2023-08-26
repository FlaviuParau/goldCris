<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_gdpruserdata_request'
 */
if ($installer->getConnection()->isTableExists('blugento_gdpruserdata_request') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_gdpruserdata_request')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'ID'
        )
        ->addColumn(
            'type',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Request type'
        )
        ->addColumn(
            'customer_email',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Customer email'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Request status'
        )
        ->addColumn(
            'archive_name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Archive Name'
        )
        ->addColumn(
            'secret_key',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Secret Key'
        )
        ->addColumn(
            'admin_confirmation',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255, array(),
            'Admin confirmation'
        )
        ->addColumn(
            'reject_delete_message',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Reject delete message'
        )
        ->addColumn(
            'created_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
            ),
            'Created At'
        )
        ->addColumn(
            'updated_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
            ),
            'Updated At'
        );

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();