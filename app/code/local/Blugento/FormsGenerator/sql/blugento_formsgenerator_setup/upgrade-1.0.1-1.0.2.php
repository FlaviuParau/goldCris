<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('blugento_generated_forms'), 'activate_recaptcha');

$installer->endSetup();
