<?php

$installer = $this;
$installer->startSetup();

$table = 'directory_country_region_city';

/** Change city name from "Bucuresti" to "Fara Sector" */
$sql = 'UPDATE directory_country_region_city 
        SET cityname = "Fara Sector" 
        WHERE region_id = 287 AND cityname LIKE "Bucuresti"';

$installer->run($sql);

/** Add Bucuresti sectors to "directory_country_region_city" */
$newCity = array('Sector 1', 'Sector 2', 'Sector 3', 'Sector 4', 'Sector 5', 'Sector 6');

foreach ($newCity as $city) {
    $sql = 'SELECT * FROM ' . $table . ' WHERE region_id = 287 AND cityname LIKE "' . $city . '"';

    $data = $installer->getConnection()->fetchOne($sql);

    if (!$data) {
        $sql = 'INSERT INTO directory_country_region_city (country_id, region_id, cityname)
        VALUES ("RO", 287, "' . $city . '")';

        $installer->run($sql);
    }
}

$installer->endSetup();