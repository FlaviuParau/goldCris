<?php

$installer = $this;
$installer->startSetup();

// Change Blugento inquiry page title
try {
    $block1 = Mage::getModel('cms/page')->load('cerere-produs', 'identifier');

    if ($block1->getId()) {
        $block1->setTitle('Cerere produs')->save();
    }
} catch (Exception $e) {
    Mage::logException($e);
}

// Change Blugento inquiry success page title
try {
    $block2 = Mage::getModel('cms/page')->load('cerere-produs-success', 'identifier');

    if ($block2->getId()) {
        $block2->setTitle('Cerere produs - succes')->save();
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
