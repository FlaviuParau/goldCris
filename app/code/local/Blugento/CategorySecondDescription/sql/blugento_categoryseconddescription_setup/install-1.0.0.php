<?php

$installer = $this;

$installer->startSetup();

$descriptionAttributeCode = 'second_description';

$installer->addAttribute('catalog_category', $descriptionAttributeCode, array(
    'group'             => 'General Information',
    'type'              => 'text',
    'input'             => 'textarea',
    'label'             => 'Second Description',
    'global'            => true,
    'visible'           => true,
    'user_defined'      => true,
    'required'          => false,
    'visible_on_front'  => true,
    'sort_order'        => 100
));

$descriptionAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', $descriptionAttributeCode);
$descriptionAttribute->setData('is_wysiwyg_enabled', true);
$descriptionAttribute->save();

$imageAttributeCode = 'second_image';

$installer->addAttribute('catalog_category', $imageAttributeCode, array(
    'group'             => 'General Information',
    'type'              => 'varchar',
    'input'             => 'image',
    'backend'           => 'catalog/category_attribute_backend_image',
    'label'             => 'Second Image',
    'global'            => true,
    'visible'           => true,
    'user_defined'      => true,
    'required'          => false,
    'visible_on_front'  => true,
    'sort_order'        => 110
));

$installer->endSetup();