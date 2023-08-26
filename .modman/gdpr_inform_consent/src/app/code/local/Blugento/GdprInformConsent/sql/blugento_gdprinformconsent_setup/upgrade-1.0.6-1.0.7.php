<?php

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'gdpr_consent', array(
	'type'      => 'static',
	'label'     => 'GDPR Inform Consent',
	'input'     => 'boolean',
	'backend'   => 'customer/attribute_backend_data_boolean',
	'required'  => true,
	'position'  => 9999,
));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'gdpr_consent');
$attribute->setData('used_in_forms', array(
	'adminhtml_customer',
	'customer_account_create',
	'customer_account_edit',
));
$attribute->save();

$installer->getConnection()->addColumn(
	$installer->getTable('customer/entity'),
	'gdpr_consent',
	array(
		'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
		'unsigned' => true,
		'nullable' => false,
		'default'  => '0',
		'comment'  => 'GDPR Inform Consent',
	)
);

$tableName = $installer->getTable('customer/entity');
$columnName = 'gdpr_consent';

if ($installer->getConnection()->tableColumnExists($tableName, $columnName)) {
	$installer->run(
		"UPDATE `{$tableName}` SET `{$columnName}` = 1"
	);
}

$installer->endSetup();
