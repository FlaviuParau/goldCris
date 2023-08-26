<?php

/** Fix Romanian zip codes for regions that zip code start with 0 */

$installer = $this;
$installer->startSetup();

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

$sql = 'SELECT * FROM blugento_cautocomplete_city WHERE LENGTH(zipcode) < 6 AND country_code = "RO" AND zipcode <> ""';
$data = $readConnection->fetchAll($sql);

if (count($data)) {
    foreach ($data as $item) {
        $zipcode = '0' . $item['zipcode'];
        $updateSql = 'UPDATE blugento_cautocomplete_city SET zipcode = "' . $zipcode . '" WHERE id = ' . $item['id'];

        $installer->run($updateSql);
    }
}

$installer->endSetup();
