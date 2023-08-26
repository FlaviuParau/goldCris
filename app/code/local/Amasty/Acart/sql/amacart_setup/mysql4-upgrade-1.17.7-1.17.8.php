<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amacart/canceled')}`
    CHANGE COLUMN `reason` `reason` ENUM('elapsed', 'bought', 'link', 'any_product_out_of_stock', 'all_products_out_of_stock', 'blacklist', 'admin', 'updated', 'quote', 'any_product_not_active', 'all_products_not_active') DEFAULT NULL;
    
    ALTER TABLE `{$this->getTable('amacart/history')}`
    CHANGE COLUMN status `status` ENUM('pending','processing','sent','done','blacklist','canceled');
");

$this->endSetup();