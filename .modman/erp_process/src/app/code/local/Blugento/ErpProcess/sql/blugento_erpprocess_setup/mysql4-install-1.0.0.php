<?php

$installer = $this;
/* @var $this Mage_Core_Model_Resource_Setup */
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/**
 * Create custom 'invoice_download_url' order attribute
 */
$installer->addAttribute('order', 'invoice_download_url', array('type'=>'varchar', 'grid' => false));

$installer->endSetup();
