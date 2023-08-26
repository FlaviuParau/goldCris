<?php

$installer = $this;
$installer->startSetup();

/** Change city name from "Bucuresti" to "Fara Sector" */
$sql = 'UPDATE directory_country_region_city 
        SET cityname = "Bucuresti" 
        WHERE region_id = 287 AND cityname LIKE "Fara Sector"';

$installer->run($sql);

/** Add Bucuresti sectors to "directory_country_region_city" */
$sql = 'DELETE FROM directory_country_region_city 
        WHERE region_id = 287
        AND cityname IN ("Sector 1", "Sector 2", "Sector 3", "Sector 4", "Sector 5", "Sector 6")';

$installer->run($sql);

$installer->endSetup();