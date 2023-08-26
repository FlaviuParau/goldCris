<?php

$this->startSetup();

$this->run("
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `tablet_image` varchar(255) NOT NULL default '' AFTER `image`;
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `mobile_image` varchar(255) NOT NULL default '' AFTER `tablet_image`;
");

$this->endSetup();
