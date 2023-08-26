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
	 * Create purchase_type attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_purchase_type', array(
		'type' => 'int',
		'input' => 'select',
		'label' => 'Purchase Type',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1,
		'source' => 'eav/entity_attribute_source_table',
		'options' => array(
			'values' => array(
				1 => 'Personal Purchase',
				2 => 'Company Purchase'
			)
		)
	));
	$attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'blugento_purchase_type');
	$attribute->setData('used_in_forms', $forms)
		->save();
	
	$options = $attribute->getSource()->getAllOptions(false);
	if (!$options) {
		
		$tableOptions = $installer->getTable('eav_attribute_option');
		$tableOptionValues = $installer->getTable('eav_attribute_option_value');
		$attributeId = (int)$installer->getAttribute('customer_address', 'blugento_purchase_type', 'attribute_id');
		
		
		// Attribute options
		$options = array(
			'Personal Purchase',
			'Company Purchase'
		);
		
		// Add options
		foreach ($options as $sortOrder => $label) {
			$data = array(
				'attribute_id' => $attributeId,
				'sort_order' => $sortOrder,
			);
			$installer->getConnection()->insert($tableOptions, $data);
			
			// Add option label
			$optionId = (int)$installer->getConnection()->lastInsertId($tableOptions, 'option_id');
			$data = array(
				'option_id' => $optionId,
				'store_id' => Mage_Core_Model_App::ADMIN_STORE_ID,
				'value' => $label
			);
			$installer->getConnection()->insert($tableOptionValues, $data);
			
		}
	}
	
	/**
	 * Create customer_cnp attribute
	 */
	$installer->addAttribute('customer_address', 'blugento_customer_cnp', array(
		'type' => 'varchar',
		'input' => 'text',
		'label' => 'CNP',
		'global' => 1,
		'visible' => 1,
		'required' => 0,
		'user_defined' => 1,
		'visible_on_front' => 1
	));
	Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'blugento_customer_cnp')
		->setData('used_in_forms', $forms)
		->save();
	
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();
