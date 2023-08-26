<?php
/**
 * Blugento Billing Attributes
 * installer script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Billing
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;
$installer->startSetup();

Mage::getConfig()
	->saveConfig('customer/address/street_lines', '1', 'default', 0)
	->saveConfig('blugento_billing/global_config/region_before', '1', 'default', 0)
	->reinit();

$installer->endSetup();
