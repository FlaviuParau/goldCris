<?php

$installer = $this;
$installer->startSetup();

$table = 'blugento_productmultitabs_tabs';

/** Add sort order to every default tab. */
$sortOrder = Mage::getSingleton('blugento_productmultitabs/system_config_source_data')->toArrayOrder();

foreach ($sortOrder as $identifier => $order) {
    $sql = 'UPDATE ' . $table . ' 
            SET sort_order = ' . $order . ' 
            WHERE identifier = "' . $identifier . '"';

    $installer->run($sql);
}

/** Move 'content' value to 'content_block' for existing custom tabs. */
$sql = 'SELECT identifier, content
        FROM ' . $table . '
        WHERE type = "custom"';

$data = $installer->getConnection()->fetchAll($sql);

foreach ($data as $item) {
    $sql = 'UPDATE ' . $table . '
            SET content_block = "' . $item['content'] . '"
            WHERE identifier LIKE "' . $item['identifier'] . '"';

    $installer->run($sql);
}

/** Set 'content' to 1 (CMS Block) for existing custom tabs. */
$sql = 'UPDATE ' . $table . '
        SET content = 1
        WHERE type LIKE "custom"';

$installer->run($sql);

$installer->endSetup();
