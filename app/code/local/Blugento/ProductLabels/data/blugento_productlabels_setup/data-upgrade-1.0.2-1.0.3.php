<?php

$installer = $this;
$installer->startSetup();

$table = 'blugento_productlabels_label';

/** Set "created_type" to "default" for "new" and "promo" labels that are created when module is installed */
$sql = 'UPDATE ' . $table . '
        SET created_type = "default"
        WHERE type IN ("new", "promo")';

$installer->run($sql);

/** Set "created_type" to "custom" for already user created labels */
$sql = 'UPDATE ' . $table . '
        SET created_type = "custom"
        WHERE type IN ("custom")';

$installer->run($sql);

/** Modify label path */
$sql = 'SELECT id, path 
        FROM ' . $table . '
        WHERE created_type LIKE "custom"';

$data = $installer->getConnection()->fetchAll($sql);

foreach ($data as $item) {
    $path = end(explode('/', $item['path']));

    $sql = 'UPDATE ' . $table . '
            SET path = "' . $path . '"
            WHERE id = ' . $item['id'];

    $installer->run($sql);
}

$installer->endSetup();
