<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

if ($installer->tableExists('gdprcookies/list')) {
    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('Facebook Login', 3, 'Facebook Login');");
}
$installer->endSetup();