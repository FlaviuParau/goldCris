<?php
/**
 * Blugento Sliders
 * Install script, creates group and banner tables
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

    DROP TABLE IF EXISTS {$this->getTable('blugento_sliders_group')};
    CREATE TABLE IF NOT EXISTS {$this->getTable('blugento_sliders_group')} (
        `group_id` int(11) unsigned NOT NULL auto_increment,
        `store_id` int(11) unsigned NOT NULL default 0,
        `title` varchar(255) NOT NULL default '',
        `code` varchar(32) NOT NULL default '',
        `is_enabled` tinyint(1) unsigned NOT NULL default 1,
        `carousel_animate` int(1) unsigned NOT NULL default 1,
        `carousel_duration` int(3) unsigned default 400,
        `carousel_auto` int(1) unsigned default NULL,
        `carousel_effect` varchar(32) NOT NULL default '',
        `controls_position` varchar(32) NOT NULL default '',
        PRIMARY KEY (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS {$this->getTable('blugento_sliders_banner')};
    CREATE TABLE IF NOT EXISTS {$this->getTable('blugento_sliders_banner')} (
        `banner_id` int(11) unsigned NOT NULL auto_increment,
        `group_id` int (11) unsigned default NULL,
        `title` varchar(255) NOT NULL default '',
        `url` varchar(255) NOT NULL default '',
        `url_target` varchar(32) NOT NULL default '',
        `image` varchar(255) NOT NULL default '',
        `alt_text` varchar(255) NOT NULL default '',
        `html` text NOT NULL default '',
        `sort_order` tinyint(3) unsigned NOT NULL default 1,
        `is_enabled` tinyint(1) unsigned NOT NULL default 1,
        KEY `FK_GROUP_ID_BANNER` (`group_id`),
        CONSTRAINT `FK_BLUGENTO_SLIDERS_GROUP_ID_BANNER` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('blugento_sliders_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        PRIMARY KEY (`banner_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ALTER TABLE {$this->getTable('blugento_sliders_group')} ADD UNIQUE (code, store_id);

    INSERT INTO {$this->getTable('blugento_sliders_group')} (store_id, title, code, is_enabled) VALUES (0, 'Homepage', 'home', 1);

");

$this->endSetup();
