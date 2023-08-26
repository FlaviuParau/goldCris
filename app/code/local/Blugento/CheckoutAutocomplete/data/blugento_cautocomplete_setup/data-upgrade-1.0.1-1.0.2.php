<?php

$installer = $this;
$installer->startSetup();

$zipcodesFile = $filepath = Mage::getBaseDir('lib') . DS . 'checkout_autocomplete' . DS . 'update_zipcodes.php';

if (file_exists($zipcodesFile)) {
    $zipcodes = require_once $zipcodesFile;

    foreach ($zipcodes as $zipcode) {
        $sql = "UPDATE blugento_cautocomplete_city SET zipcode = '" . $zipcode['zip'] . "' WHERE region_id = " . $zipcode['region_id'] . " AND city LIKE '" . $zipcode['name'] . "'";
        $installer->run($sql);
    }
}

$updateFile = $filepath = Mage::getBaseDir('lib') . DS . 'checkout_autocomplete' . DS . 'add_data.php';

if (file_exists($updateFile)) {
    $data = require_once $updateFile;

    $values = array();
    foreach ($data as $item) {
        $values[] = '("' . $item['city'] . '", "' . $item['region'] . '", ' . $item['region_id'] . ', "' . $item['zipcode'] . '", "' . $item['country_code'] . '", ' . $item['priority'] . ')';
    }

    $sql = 'INSERT INTO blugento_cautocomplete_city (city, region, region_id, zipcode, country_code, priority) VALUES ' . implode(',', $values);
    $installer->run($sql);
}

$installer->endSetup();