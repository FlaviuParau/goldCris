<?php
/**
 * Set permissions for cms/block and newsletter/subscribe
 * for Magento > 1.9.2.2
 */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

try {
    /*
     * Make sure the upgrade is not performed on installations without the tables
     */
    $adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
    if (version_compare($adminVersion, '1.6.1.2', '>=')) {
        $installer->getConnection()->insertMultiple(
            $installer->getTable('admin/permission_block'),
            array(
                array('block_name' => 'cms/block', 'is_allowed' => 1),
                array('block_name' => 'newsletter/subscribe', 'is_allowed' => 1),
            )
        );
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
