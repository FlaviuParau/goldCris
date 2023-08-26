<?php
/**
 * Blugento Sliders
 * Install script, add carousel_autospeed column to group table
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
    ALTER TABLE {$this->getTable('blugento_sliders_group')} ADD `carousel_autospeed` int(5) unsigned default 3000;
");

$this->endSetup();
