<?php

$installer = $this;
$installer->startSetup();

// Add "Additional Info" attribute to all existing stores
$installer->addAttribute(
    'catalog_product',
    'additional_info',
    array(
        'label' => 'Additional Info',
        'group' => 'General',
        'required' => 0,
        'visible'  => 1,
    )
);

// Add "Blugento Additional Info" CMS block to all existing stores
$identifier = 'blugento-additional-info';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '';
    $block->setTitle('Blugento Additional Info');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}


$installer->endSetup();