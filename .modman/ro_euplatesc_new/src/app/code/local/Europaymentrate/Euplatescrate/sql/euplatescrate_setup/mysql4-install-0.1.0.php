<?php

$code = 'euplatescrate';

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE euplatescrate(
   id   INT              NOT NULL AUTO_INCREMENT,
   ep_id VARCHAR (50)     NOT NULL,
   invoice_id VARCHAR (50)     NOT NULL, 
   PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Map id of EuPlatesc.ro with Id of MAGE'
	

");

$installer->endSetup();

?>