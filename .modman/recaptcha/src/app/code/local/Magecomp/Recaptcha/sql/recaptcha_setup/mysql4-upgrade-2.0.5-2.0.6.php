<?php

$installer = $this;
$installer->startSetup();

$installer->run("UPDATE `customer_eav_attribute` SET `validate_rules` = 'a:2:{s:15:\"max_text_length\";i:30;s:15:\"min_text_length\";i:1;}' WHERE `attribute_id` IN (SELECT `attribute_id` FROM `eav_attribute` WHERE  (`attribute_code` LIKE '%firstname%' OR  `attribute_code` LIKE '%lastname%') AND `entity_type_id` = 1)");

$installer->endSetup();
