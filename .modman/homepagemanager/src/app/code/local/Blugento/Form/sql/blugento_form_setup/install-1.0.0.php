<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$installer->addEntityType('contact', array(
    'entity_model'          => 'sales/order',
    'table'                 => 'sales/order',
    'increment_model'       => 'eav/entity_increment_alphanum',
    'increment_per_store'   => 0,
    'increment_prefix'      => 'C',
));

$installer->addEntityType('orderform', array(
    'entity_model'          => 'sales/order',
    'table'                 => 'sales/order',
    'increment_model'       => 'eav/entity_increment_alphanum',
    'increment_per_store'   => 0,
    'increment_prefix'      => 'O',
));

$installer->addEntityType('websiteform', array(
    'entity_model'          => 'sales/order',
    'table'                 => 'sales/order',
    'increment_model'       => 'eav/entity_increment_alphanum',
    'increment_per_store'   => 0,
    'increment_prefix'      => 'W',
));

$installer->addEntityType('custominc', array(
    'entity_model'          => 'sales/order',
    'table'                 => 'sales/order',
    'increment_model'       => 'eav/entity_increment_alphanum',
    'increment_per_store'   => 0,
    'increment_prefix'      => 'CU',
));

$installer->endSetup();
