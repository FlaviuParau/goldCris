<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


$installer = $this;
$installer->startSetup();

$field = 'tax_class_id';
$applyTo = $installer->getAttribute('catalog_product', $field, 'apply_to');
if ($applyTo) {
    $applyTo = explode(',', $applyTo);
    if (!in_array(Amasty_StoreCredit_Model_Catalog_Product_Type_StoreCredit::TYPE_STORECREDIT_PRODUCT, $applyTo)) {
        $applyTo[] = Amasty_StoreCredit_Model_Catalog_Product_Type_StoreCredit::TYPE_STORECREDIT_PRODUCT;
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->endSetup();


$installerSales = new Mage_Sales_Model_Resource_Setup('core_setup');
$installerSales->startSetup();


$installerSales->addAttribute('creditmemo', 'base_am_amount_total_refunded', array('type' => 'decimal'));
$installerSales->addAttribute('creditmemo', 'am_amount_total_refunded', array('type' => 'decimal'));

$installerSales->addAttribute('order', 'base_am_amount_total_refunded', array('type' => 'decimal'));
$installerSales->addAttribute('order', 'am_amount_total_refunded', array('type' => 'decimal'));


$installerSales->endSetup();
