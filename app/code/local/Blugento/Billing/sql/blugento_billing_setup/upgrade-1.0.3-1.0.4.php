<?php
/**
 * Blugento Billing Attributes
 * installer script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Billing
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;

$installer->startSetup();

$forms = array(
	'customer_register_address',
	'customer_address_edit',
	'adminhtml_customer_address'
);

try {
	/**
	 * Create customer_reg_no attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_customer_reg_no', array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'Company Registration Number',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1
	));
	
	/**
	 * Create customer_iban attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_customer_iban', array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'IBAN',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1
	));
	
	/**
	 * Create customer_bank attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_customer_bank', array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'Bank Name',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1
	));
	
	/**
	 * Create customer_iban attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_customer_headquarter', array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'Headquarter',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1
	));
	
	/**
	 * Add columns for new attributes in sales_flat_order_address and sales_flat_quote_address
	 */
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_purchase_type` INT(11) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_customer_cnp` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_customer_reg_no` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_customer_iban` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_customer_bank` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/quote_address')}` ADD `blugento_customer_headquarter` VARCHAR(250) NULL DEFAULT NULL");
	
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_purchase_type` INT(11) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_customer_cnp` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_customer_reg_no` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_customer_iban` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_customer_bank` VARCHAR(150) NULL DEFAULT NULL");
	$installer->run("ALTER TABLE `{$this->getTable('sales/order_address')}` ADD `blugento_customer_headquarter` VARCHAR(250) NULL DEFAULT NULL");
	
	/**
	 * Update telephone attribute
	 */
	$installer->updateAttribute('customer_address', 'firstname', 'is_required', 0);
	$installer->updateAttribute('customer', 'firstname', 'is_required', 0);
	$installer->updateAttribute('customer_address', 'telephone', 'is_required', 0);
	
	/**
	 * Set attributes to be used in forms
	 */
	$attributeReg = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'blugento_customer_reg_no');
	$attributeReg->setData('used_in_forms', $forms)
		->save();
	
	$attributeIban = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'blugento_customer_iban');
	$attributeIban->setData('used_in_forms', $forms)
		->save();
	
	$attributeBank = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'blugento_customer_bank');
	$attributeBank->setData('used_in_forms', $forms)
		->save();
	
	$attributeHeadquarter = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'blugento_customer_headquarter');
	$attributeHeadquarter->setData('used_in_forms', $forms)
		->save();
	
	/**
	 * Set address templates
	 */
	$setup = Mage::getModel('eav/entity_setup', 'core_setup');
	$template = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{depend blugento_customer_cnp}}{{var blugento_customer_cnp}},{{/depend}}{{depend blugento_customer_reg_no}} {{var blugento_customer_reg_no}},{{/depend}} {{var street}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}';
	$setup->setConfigData('customer/address_templates/oneline', $template, 'default', 0);
	
	$template = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{if company}}{{var company}}
{{depend blugento_customer_reg_no}}Nr. Reg. Com.: {{var blugento_customer_reg_no}}{{/depend}}
{{depend blugento_customer_iban}}IBAN: {{var blugento_customer_iban}}{{/depend}}
{{depend blugento_customer_headquarter}}Sediu Social: {{var blugento_customer_headquarter}}{{/depend}}
{{depend blugento_customer_bank}}Banca: {{var blugento_customer_bank}}{{/depend}}
{{depend vat_id}}Cod TVA: {{var vat_id}}{{/depend}}
{{/if}}
{{if blugento_customer_cnp}}CNP: {{var blugento_customer_cnp}}
{{/if}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{var country}}
T: {{var telephone}}
{{depend fax}}F: {{var fax}}{{/depend}}';
	$setup->setConfigData('customer/address_templates/text', $template, 'default', 0);
	
	$template = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}<br/>
{{if company}}{{var company}}<br />
{{depend blugento_customer_reg_no}}Nr. Reg. Com.: {{var blugento_customer_reg_no}}<br />{{/depend}}
{{depend blugento_customer_iban}}IBAN: {{var blugento_customer_iban}}<br />{{/depend}}
{{depend blugento_customer_headquarter}}Sediu Social: {{var blugento_customer_headquarter}}<br />{{/depend}}
{{depend blugento_customer_bank}}Banca: {{var blugento_customer_bank}}<br />{{/depend}}
{{depend vat_id}}Cod TVA: {{var vat_id}}<br />{{/depend}}
{{/if}}
{{if blugento_customer_cnp}}CNP: {{var blugento_customer_cnp}}<br/>
{{/if}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br/>
{{var country}}<br/>
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}<br/>F: {{var fax}}{{/depend}}';
	$setup->setConfigData('customer/address_templates/html', $template, 'default', 0);
	
	$template = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{if company}}{{var company}}|
{{depend blugento_customer_reg_no}}Nr. Reg. Com.: {{var blugento_customer_reg_no}}|{{/depend}}
{{depend blugento_customer_iban}}IBAN: {{var blugento_customer_iban}}|{{/depend}}
{{depend blugento_customer_headquarter}}Sediu Social: {{var blugento_customer_headquarter}}|{{/depend}}
{{depend blugento_customer_bank}}Banca: {{var blugento_customer_bank}}|{{/depend}}
{{depend vat_id}}Cod TVA: {{var vat_id}}|{{/depend}}
{{/if}}
{{if blugento_customer_cnp}}CNP: {{var blugento_customer_cnp}}|
{{/if}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}|
{{if postcode}}{{var postcode}} {{/if}}{{if city}}{{var city}}|{{/if}}';
	$setup->setConfigData('customer/address_templates/pdf', $template, 'default', 0);
	
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();
