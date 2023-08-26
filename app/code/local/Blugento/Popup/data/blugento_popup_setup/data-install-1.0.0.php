<?php
$this->startSetup();

$blockName = 'blugento_popup/cms_popup';
try {
    /** @var Mage_Admin_Model_Block $block */
    $block = Mage::getModel('admin/block');
    if (is_object($block)) {
        $block->load($blockName, 'block_name');
        if (!$block->getId()) {
            $block->setData(array('block_name' => $blockName, 'is_allowed' => 1));
            $block->save();
        }
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$this->endSetup();

