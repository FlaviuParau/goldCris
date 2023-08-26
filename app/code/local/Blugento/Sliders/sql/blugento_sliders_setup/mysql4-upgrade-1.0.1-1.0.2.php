<?php
/**
 * Blugento Sliders
 * Install script, add is_wide column to group table
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$this->startSetup();

$this->run("
    ALTER TABLE {$this->getTable('blugento_sliders_group')} ADD `is_wide` tinyint(1) unsigned NOT NULL default 0;
");

$this->endSetup();
