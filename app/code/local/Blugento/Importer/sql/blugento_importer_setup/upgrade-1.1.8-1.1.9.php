<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'ftp_server', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => NULL,
        'comment' => 'FTP Server'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'ftp_username', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => NULL,
        'comment' => 'FTP Username'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'ftp_password', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => NULL,
        'comment' => 'FTP Password'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'ftp_filepath', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => NULL,
        'comment' => 'FTP Filepath'
    ));

$installer->endSetup();