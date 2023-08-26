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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'blugento_fbconversion_event'
 */
if ($installer->getConnection()->isTableExists('blugento_fbconversion_event') != true) {
    $table = $installer->getConnection()
        ->newTable('blugento_fbconversion_event')
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
            'ID'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event name'
        )
        ->addColumn(
            'time',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event time'
        )
        ->addColumn(
            'user_email_hash',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'User email hash'
        )
        ->addColumn(
            'user_phone_hash',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'User phone hash'
        )
        ->addColumn(
            'ip_address',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'IP address'
        )
        ->addColumn(
            'client_user_agent',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Client user agent'
        )
        ->addColumn(
            'fbc',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Facebook Click ID'
        )
        ->addColumn(
            'fbp',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Facebook Browser ID'
        )
        ->addColumn(
            'content',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event Content'
        )
        ->addColumn(
            'custom_data',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event Custom Data'
        )
        ->addColumn(
            'action_source',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event Action Source'
        )
        ->addColumn(
            'source_url',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            null,
            'Event Source URL'
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
    ;
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();