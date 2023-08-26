<?php

$installer = $this;
$installer->startSetup();

$blockCheckoutId = 'blugento-checkout-gdpr-acknowledgement-';
$checkoutQuery = 'UPDATE cms_block SET identifier = "blugento-checkout-gdpr-acknowledgement" WHERE identifier LIKE "%'. $blockCheckoutId .'%"';
$installer->run($checkoutQuery);

$blockNewsletterId = 'blugento-newsletter-checkbox-consent-';
$newsletterQuery = 'UPDATE cms_block SET identifier = "blugento-newsletter-checkbox-consent" WHERE identifier LIKE "%'. $blockNewsletterId .'%"';
$installer->run($newsletterQuery);

$blockAccountId = 'blugento-newsletter-checkbox-myaccountchecked-';
$accountQuery = 'UPDATE cms_block SET identifier = "blugento-newsletter-checkbox-myaccountchecked" WHERE identifier LIKE "%'. $blockAccountId .'%"';
$installer->run($accountQuery);

$installer->endSetup();