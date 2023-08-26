<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */


/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();
$this->run(
    "CREATE TABLE IF NOT EXISTS `{$this->getTable('amgeoip/block_v6')}`(
    `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `start_ip_num` int(10) unsigned NOT NULL,
    `end_ip_num` int(10) unsigned NOT NULL,
    `geoip_loc_id` int(10) unsigned NOT NULL,
    `postal_code` char(10) DEFAULT NULL,
    `latitude` float DEFAULT NULL,
    `longitude` float DEFAULT NULL,
    PRIMARY KEY (`block_id`),
    KEY `start_ip_num` (`start_ip_num`),
    KEY `end_ip_num` (`end_ip_num`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;
");
$this->endSetup();
