<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create custom 'awb_number' order attribute
 */
$installer->addAttribute('order', 'awb_number', array('type'=>'varchar', 'grid' => false));

$installer->endSetup();
