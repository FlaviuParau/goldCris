<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_generated_forms'
 */
if ($installer->getConnection()->isTableExists('blugento_generated_forms') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_generated_forms')
        ->addColumn(
            'id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ),
            'Form ID'
        )
        ->addColumn(
            'form_name',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Form Name'
        )
        ->addColumn(
            'fields_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Fields Code'
        )
        ->addColumn(
            'form_code',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Form Code'
        )
        ->addColumn(
            'form_status',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'default' => 1
            ),
            'Status'
        )
        ->addColumn(
            'recipient',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Recipient'
        )
        ->addColumn(
            'recipient_email',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Recipient Email'
        )
        ->addColumn(
            'shortcode',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Shortcode'
        )
        ->addColumn(
            'success_page',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Success Page'
        )
        ->addColumn(
            'email_template_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            'Email Template'
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
        )
    ;
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();