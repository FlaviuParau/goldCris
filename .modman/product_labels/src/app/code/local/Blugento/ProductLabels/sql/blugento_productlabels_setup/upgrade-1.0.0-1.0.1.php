<?php
$installer = $this;

$installer->getConnection()
    ->modifyColumn($installer->getTable('blugento_productlabels_label'),
    'categories', Varien_Db_Ddl_Table::TYPE_TEXT);

$installer->endSetup();
