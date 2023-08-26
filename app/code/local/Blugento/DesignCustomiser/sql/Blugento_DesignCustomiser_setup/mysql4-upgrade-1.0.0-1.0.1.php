<?php
/**
 * Blugento Design Customiser
 * installer script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_DesignCustomiser
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;

$installer->startSetup();

Mage::log('blugento_designcustomiser', null, 'sql.log', true);

// Create final css backup table
try {
    $installer->run("
        CREATE TABLE IF NOT EXISTS blugento_final_css (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `css` TEXT NULL,
          `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    ");
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
