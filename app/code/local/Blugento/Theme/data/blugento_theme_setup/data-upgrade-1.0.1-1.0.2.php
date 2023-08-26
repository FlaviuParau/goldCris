<?php

$installer = $this;
$installer->startSetup();

// Add "Blugento Footer Block 1" CMS block to all existing stores
$identifier = 'blugento-footer-block-1';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p></p>';
    $block->setTitle('Blugento Footer Block 1');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

// Add "Blugento Footer Block 2" CMS block to all existing stores
$identifier = 'blugento-footer-block-2';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p></p>';
    $block->setTitle('Blugento Footer Block 2');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();
