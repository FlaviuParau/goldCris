<?php

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute(
    'catalog_product',
    'delivery_day_min',
    array(
        'type' => 'int',
        'group' => 'General',
        'label' => 'Minimum days till delivery',
        'input' => 'text',
        'visible' => true,
        'visible_on_front' => true,
        'user_defined' => false,
        'source' => '',
        'sort_order' => 1001,
        'position' => 1001,
        'system' => 0,
        'unique' => false,
        'attribute_set' => 'Default',
        'required' => false,
        'frontend_class' => 'validate-number'
    )
);

$setup->addAttribute(
    'catalog_product',
    'delivery_day_max',
    array(
        'type' => 'int',
        'group' => 'General',
        'label' => 'Maximum days till delivery',
        'input' => 'text',
        'visible' => true,
        'visible_on_front' => true,
        'user_defined' => false,
        'source' => '',
        'sort_order' => 1001,
        'position' => 1001,
        'system' => 0,
        'unique' => false,
        'attribute_set' => 'Default',
        'required' => false,
        'frontend_class' => 'validate-number'
    )
);

$installer->endSetup();
