<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_invoice_email_sent', 'smallint');

$installer->endSetup();
