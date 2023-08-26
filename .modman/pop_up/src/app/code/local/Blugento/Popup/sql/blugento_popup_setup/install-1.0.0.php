<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_popup'
 */
if ($installer->getConnection()->isTableExists('blugento_popup') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_popup')
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
            'Popup ID'
        )
        ->addColumn(
            'title',
            Varien_Db_Ddl_Table::TYPE_VARCHAR,
            255,
            'Popup Title'
        )
        ->addColumn(
            'content',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Popup Content'
        )
        ->addColumn(
            'pages',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Popup Pages'
        )
        ->addColumn(
            'status',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'default' => 1
            ),
            'Status'
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