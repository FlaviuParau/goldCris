<?php

$installer = $this;

$installer->startSetup();

// Add block permissions
try {
    /*
     * Make sure the upgrade is not performed on installations without the tables
     */
    $adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
    if (version_compare($adminVersion, '1.6.1.2', '>=')) {
        $installer->getConnection()->insertMultiple(
            $installer->getTable('admin/permission_block'),
            array(
                array('block_name' => 'fblogin/fblogin', 'is_allowed' => 1)
            )
        );
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup(); 