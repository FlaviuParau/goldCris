<?php

$installer = $this;
$installer->startSetup();

// Add "Blugento Header Block 1" CMS block to all existing stores
$identifier = 'blugento-header-block-1';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<div class="header-block header-block-1"></div>';
    $block->setTitle('Blugento Header Block 1');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

// Add "Blugento Header Block 2" CMS block to all existing stores
$identifier = 'blugento-header-block-2';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<div class="header-block header-block-2"></div>';
    $block->setTitle('Blugento Header Block 2');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();