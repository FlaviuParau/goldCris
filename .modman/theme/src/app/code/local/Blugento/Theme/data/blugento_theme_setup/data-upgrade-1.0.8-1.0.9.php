<?php
/**
 * Set permissions for blugento_localizer/imprint_field block
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
                array('block_name' => 'blugento_localizer/imprint_field', 'is_allowed' => 1)
            )
        );
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
