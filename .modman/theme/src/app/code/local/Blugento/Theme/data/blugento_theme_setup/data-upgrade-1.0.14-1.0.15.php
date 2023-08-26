<?php

$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

$query = 'SELECT `page_id` FROM cms_page WHERE `identifier` like "termeni-si-conditii";';
$data = $readConnection->fetchAll($query);

if ($data) {
    $query = 'SELECT * FROM cms_page_store WHERE `page_id`=' . $data[0]['page_id'];
    $pageStore = $readConnection->fetchAll($query);

    if (!$pageStore) {
        $query = 'INSERT INTO cms_page_store (`page_id`, `store_id`) VALUES (' . $data[0]['page_id'] . ', 0)';
        $installer->run($query);
    }
}

$installer->endSetup();