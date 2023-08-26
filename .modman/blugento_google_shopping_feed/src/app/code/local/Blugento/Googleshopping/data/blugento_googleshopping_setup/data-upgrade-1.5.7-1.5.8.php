<?php

$process = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_flat');
if ($process->getStatus() != Mage_Index_Model_Process::STATUS_RUNNING) {
    $process = Mage::getModel('index/indexer')->getProcessByCode('catalog_category_flat');
    $process->reindexAll();
}

