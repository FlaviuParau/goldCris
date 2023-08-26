<?php

$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

$query = 'SELECT * FROM core_config_data WHERE path like "advanced/modules_disable_output/Mage_AdminNotification";';
$data = $readConnection->fetchRow($query);

if (!$data) {
    $query = 'INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES (\'default\', 0, \'advanced/modules_disable_output/Mage_AdminNotification\', \'1\');';
} else {
    $query = 'UPDATE core_config_data SET value = 1 WHERE path LIKE "advanced/modules_disable_output/Mage_AdminNotification";';
}

$installer->run($query);

$installer->endSetup();





