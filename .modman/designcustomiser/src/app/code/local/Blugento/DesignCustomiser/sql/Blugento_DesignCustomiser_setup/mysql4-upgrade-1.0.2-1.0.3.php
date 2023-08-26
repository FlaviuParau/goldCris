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

if ($installer->getConnection()->isTableExists('blugento_final_css')) {
    $installer->run("ALTER TABLE blugento_final_css MODIFY COLUMN css longtext");
}

$installer->endSetup();
