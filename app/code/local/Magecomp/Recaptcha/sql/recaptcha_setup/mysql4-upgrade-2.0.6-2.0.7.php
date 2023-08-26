<?php

$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

/*** Update customer address attributes*/
$setup->updateAttribute('customer', 'lastname', 'is_required', 1);
$setup->updateAttribute('customer', 'firstname', 'is_required', 1);

$installer->endSetup();