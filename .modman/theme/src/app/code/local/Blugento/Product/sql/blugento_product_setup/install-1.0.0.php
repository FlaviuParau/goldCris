<?php

$installer = $this;
$installer->startSetup();

$this->addAttribute('catalog_product', 'image_hover',
	array (
		'group'                   => 'Images',
		'type'                    => 'varchar',
		'frontend'                => 'catalog/product_attribute_frontend_image',
		'label'                   => 'Hover Image',
		'input'                   => 'media_image',
		'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'visible'                 => true,
		'required'                => false,
		'used_in_product_listing' => true,
	)
);

$installer->endSetup();