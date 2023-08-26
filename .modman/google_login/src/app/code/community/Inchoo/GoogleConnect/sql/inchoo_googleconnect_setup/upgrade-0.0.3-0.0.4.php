<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

if ($installer->tableExists('gdprcookies/list')) {
    $installer->run("UPDATE {$this->getTable('gdprcookies/list')} SET cookie_description = 'Acest serviciu este folosit în scopul plasării comenzilor, mult mai rapid, prin intermediul contului tău de Google.'
                      WHERE  cookie_name like 'Google Login';");
}
$installer->endSetup();



