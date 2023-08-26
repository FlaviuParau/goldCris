<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$installer->getConnection()
	->addColumn($installer->getTable('blugento_generated_forms'),
		'activate_recaptcha',
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'size'      => 11,
			'comment'   => 'Recaptcha Status',
			'after'     => 'email_template_id'
		)
	);

$installer->endSetup();
