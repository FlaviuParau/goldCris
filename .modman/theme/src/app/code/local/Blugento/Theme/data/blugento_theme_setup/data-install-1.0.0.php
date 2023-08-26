<?php

$installer = $this;
$installer->startSetup();

// Add "Blugento Navigation Before Links" CMS block to all existing stores
$identifier = 'blugento-navigation-before-links';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<ul class="links-before"><li><a href="{{store url=""}}">Home</a></li></ul>';
    $block->setTitle('Blugento Navigation Before Links');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

// Add "Blugento Navigation After Links" CMS block to all existing stores
$identifier = 'blugento-navigation-after-links';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<ul class="links-after"><li><a href="{{store url="contacts"}}">Contact</a></li></ul>';
    $block->setTitle('Blugento Navigation After Links');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

// Add "Blugento Footer Marketing" CMS block to all existing stores
$identifier = 'blugento-footer-marketing';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<div class="marketing-box marketing-box--shipping"><i>&nbsp;</i><div class="box-title"><h4>For orders above &euro; 50.-</h4><h5>Free Shipping</h5></div></div><div class="marketing-box marketing-box--support"><i>&nbsp;</i><div class="box-title"><h4>We offer complete</h4><h5>24/7 Support</h5></div></div>{{block type="newsletter/subscribe" template="newsletter/subscribe-footer.phtml"}}';
    $block->setTitle('Blugento Footer Marketing');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();
