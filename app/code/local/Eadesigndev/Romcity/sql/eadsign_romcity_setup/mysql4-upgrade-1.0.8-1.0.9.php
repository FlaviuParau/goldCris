<?php

$installer = $this;
$installer->startSetup();

/** Change city name from "Bucuresti" to "Fara Sector" */
$sql = 'UPDATE directory_country_region_city 
        SET cityname = "Fara Sector" 
        WHERE region_id = 287 AND cityname LIKE "Bucuresti"';

$installer->run($sql);

/** Add Bucuresti sectors to "directory_country_region_city" */
$sql = 'INSERT INTO directory_country_region_city (country_id, region_id, cityname)
        VALUES 
        ("RO", 287, "Sector 1"),
        ("RO", 287, "Sector 2"),
        ("RO", 287, "Sector 3"),
        ("RO", 287, "Sector 4"),
        ("RO", 287, "Sector 5"),
        ("RO", 287, "Sector 6")';

$installer->run($sql);

$installer->endSetup();