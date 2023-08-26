<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->modifyColumn('sales_recurring_profile', 'reference_id', 'varchar(255)');
$installer->endSetup();