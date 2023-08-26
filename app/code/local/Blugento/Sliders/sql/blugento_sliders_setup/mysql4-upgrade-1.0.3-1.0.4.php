<?php

$this->startSetup();

$this->run("
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `tablet_banner_width` int(11) NULL;
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `tablet_banner_height` int(11) NULL;
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `mobile_banner_width` int(11) NULL;
    ALTER TABLE {$this->getTable('blugento_sliders_banner')} ADD `mobile_banner_height` int(11) NULL;
");

$this->endSetup();
