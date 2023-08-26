<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

if ($installer->tableExists('gdprcookies/list')) {
    $installer->run("UPDATE {$this->getTable('gdprcookies/list')} SET cookie_description = 'Acest serviciu este folosit în scopul optimizării reclamelor care îți vor fi afișate la navigarea pe Facebook'
                      WHERE  cookie_name like 'Facebook Pixel';");
}
$installer->endSetup();



