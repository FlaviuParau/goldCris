<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->setConfigData('gdpr_cookies/general/manage_page_link', 'politica-de-utilizare-cookie-uri', 'default', 0);

$installer->endSetup();