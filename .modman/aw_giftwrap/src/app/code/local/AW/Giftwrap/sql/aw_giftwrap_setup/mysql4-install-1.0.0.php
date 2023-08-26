<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_giftwrap/type')}` (
    `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NULL,
    `is_enabled` TINYINT(4) UNSIGNED NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(12,4) NOT NULL,
    `image` VARCHAR(255) NULL,
    `sort_order` INT NULL,
    PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_giftwrap/order_wrap')}` (
    `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT UNSIGNED NOT NULL,
    `base_giftwrap_amount` FLOAT NOT NULL,
    `giftwrap_amount` FLOAT NOT NULL,
    `giftwrap_type_info` TEXT NOT NULL,
    `gift_message` TEXT NULL,
    `is_wrapping_products_separately` TINYINT(4) NOT NULL,
    PRIMARY KEY (`link_id`),
    INDEX `fk_aw_giftwrap_order_wrap_idx` (`order_id` ASC),
    CONSTRAINT `fk_aw_giftwrap_order_wrap_order_id`
        FOREIGN KEY (`order_id`)
        REFERENCES `{$installer->getTable('sales/order')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_giftwrap/invoice_wrap')}` (
    `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` INT UNSIGNED NOT NULL,
    `base_giftwrap_amount` FLOAT NOT NULL,
    `giftwrap_amount` FLOAT NOT NULL,
    PRIMARY KEY (`link_id`),
    INDEX `fk_aw_giftwrap_invoice_wrap_idx` (`invoice_id` ASC),
    CONSTRAINT `fk_aw_giftwrap_invoice_wrap_order_id`
        FOREIGN KEY (`invoice_id`)
        REFERENCES `{$installer->getTable('sales/invoice')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('aw_giftwrap/creditmemo_wrap')}` (
    `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `creditmemo_id` INT UNSIGNED NOT NULL,
    `base_giftwrap_amount` FLOAT NOT NULL,
    `giftwrap_amount` FLOAT NOT NULL,
    PRIMARY KEY (`link_id`),
    INDEX `fk_aw_giftwrap_creditmemo_wrap_idx` (`creditmemo_id` ASC),
    CONSTRAINT `fk_aw_giftwrap_creditmemo_wrap_order_id`
        FOREIGN KEY (`creditmemo_id`)
        REFERENCES `{$installer->getTable('sales/creditmemo')}` (`entity_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();