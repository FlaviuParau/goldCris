<?php

$installer = $this;
$installer->startSetup();

// Create Success CMS page
try {
	$cmsSuccessPage = Array(
		'title'         => 'Blugento Product Inquiry Success',
		'identifier'    => 'blugento-product-inquiry-success',
		'content'       => getSuccessPageContent(),
		'is_active'     => 1,
		'stores'        => array(0),
		'root_template' => 'one_column'
	);
	Mage::getModel('cms/page')->setData($cmsSuccessPage)->save();
} catch (Exception $e) {
	Mage::logException($e);
}

// Add Blocks Permission
try {
	$adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
	if (version_compare($adminVersion, '1.6.1.2', '>=')) {
		$installer->getConnection()->insertMultiple(
			$installer->getTable('admin/permission_block'),
			array(
				array(
					'block_name' => 'blugento_cart/marketing',
					'is_allowed' => 1
				),
			)
		);
	}
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();

function getSuccessPageContent()
{
	return '<div class="row">
		<div class="col-6 col-sm-12">
			<h2>Thank you for your interest!</h2>
			<span>The information was successfully sent. A consultant will contact you as soon as possible to provide you with more information.</span>
			{{block type="blugento_cart/marketing" name="cartMarketing" template="blugento/cart/conversion.phtml"}}
		</div>
	</div>';
}
