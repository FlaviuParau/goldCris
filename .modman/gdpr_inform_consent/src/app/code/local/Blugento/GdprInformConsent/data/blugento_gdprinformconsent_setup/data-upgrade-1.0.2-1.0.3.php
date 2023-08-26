<?php

$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

// Add record in "cms_page_store" for "Termeni si Conditii DE" CMS Page
$query = 'SELECT `page_id` FROM cms_page WHERE `identifier` like "allgemeine-geschäftsbedingungen";';
$data = $readConnection->fetchAll($query);

if ($data) {
    $query = 'SELECT * FROM cms_page_store WHERE `page_id`=' . $data[0]['page_id'];
    $pageStore = $readConnection->fetchAll($query);

    if (!$pageStore) {
        $query = 'INSERT INTO cms_page_store (`page_id`, `store_id`) VALUES (' . $data[0]['page_id'] . ', 0)';
        $installer->run($query);
    }
}

// Add record in "cms_page_store" for "Politica de confidentialitate DE" CMS Page
$query = 'SELECT `page_id` FROM cms_page WHERE `identifier` like "datenschutzrichtlinie";';
$data = $readConnection->fetchAll($query);

if ($data) {
    $query = 'SELECT * FROM cms_page_store WHERE `page_id`=' . $data[0]['page_id'];
    $pageStore = $readConnection->fetchAll($query);

    if (!$pageStore) {
        $query = 'INSERT INTO cms_page_store (`page_id`, `store_id`) VALUES (' . $data[0]['page_id'] . ', 0)';
        $installer->run($query);
    }
}

// Add record in "cms_block_store" for "Blugento Newsletter Checkbox Consent DE" CMS Block
$query = 'SELECT `block_id` FROM cms_block WHERE `identifier` like "blugento-newsletter-checkbox-consent-de";';
$data = $readConnection->fetchAll($query);

if ($data) {
    $query = 'SELECT * FROM cms_block_store WHERE `block_id`=' . $data[0]['block_id'];
    $pageStore = $readConnection->fetchAll($query);

    if (!$pageStore) {
        $query = 'INSERT INTO cms_block_store (`block_id`, `store_id`) VALUES (' . $data[0]['block_id'] . ', 0)';
        $installer->run($query);
    }
}

// Add record in "cms_block_store" for "Blugento Checkout GDPR acknowledfement DE" CMS Block
$query = 'SELECT `block_id` FROM cms_block WHERE `identifier` like "blugento-checkout-gdpr-acknowledgement-de";';
$data = $readConnection->fetchAll($query);

if ($data) {
    $query = 'SELECT * FROM cms_block_store WHERE `block_id`=' . $data[0]['block_id'];
    $pageStore = $readConnection->fetchAll($query);

    if (!$pageStore) {
        $query = 'INSERT INTO cms_block_store (`block_id`, `store_id`) VALUES (' . $data[0]['block_id'] . ', 0)';
        $installer->run($query);
    }
}

$installer->endSetup();