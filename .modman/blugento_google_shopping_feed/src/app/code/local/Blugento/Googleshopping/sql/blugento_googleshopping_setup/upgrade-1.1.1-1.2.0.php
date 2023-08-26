<?php

/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();
$installer->deleteConfigData('crontab/jobs/googleshopping_generate/run/model');
$installer->deleteConfigData('crontab/jobs/googleshopping_generate/schedule/cron_expr');
$installer->endSetup();
