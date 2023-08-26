<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn('blugento_popup', 'category_pages', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => null,
        'comment' => 'Category pages',
        'after' => 'pages',
    ));

$installer->endSetup();
