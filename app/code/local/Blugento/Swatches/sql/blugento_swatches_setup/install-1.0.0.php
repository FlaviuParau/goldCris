<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_swatches'
 */
if ($installer->getConnection()->isTableExists('blugento_swatches') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_swatches')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
            'ID'
        )
        ->addColumn(
            'attribute',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'Attribute'
        )
        ->addColumn(
            'option_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'default' => NULL
            ),
            'Option ID'
        )
        ->addColumn(
            'mode',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'default' => 1
            ),
            'Mode'
        )
        ->addColumn(
            'image_name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'Image Name'
        )
        ->addColumn(
            'color',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255, array(),
            'Color'
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