<?php
/**
 * Blugento Design Customiser
 * installer script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_DesignCustomiser
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;
$installer->startSetup();

if (!$installer->getConnection()->isTableExists('blugento_final_css')) {
    $table = $installer->getConnection()
        ->newTable('blugento_final_css')
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Id')
        ->addColumn('css', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
        ), 'Final CSS')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Updated At')
        ->setComment('Design Customiser Final CSS Table');
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
