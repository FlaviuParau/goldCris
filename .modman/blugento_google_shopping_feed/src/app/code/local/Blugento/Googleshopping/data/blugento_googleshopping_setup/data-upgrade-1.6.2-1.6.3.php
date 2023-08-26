<?php

$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_category_flat');
$process->reindexAll();
