<?php
/**
 * Blugento Admin Menu
 * Setup script; Adds a new column 'Visible in Checkout' to attributes
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;
$installer->startSetup();

if (version_compare(Mage::getVersion(), '1.6', '<')) {

    $installer->run("
        ALTER TABLE `{$installer->getTable('catalog/eav_attribute')}`
        ADD `is_visible_on_checkout` SMALLINT(5) NOT NULL DEFAULT '0';
    ");

} else {

    $installer->getConnection()->addColumn(
        $installer->getTable('catalog/eav_attribute'),
        'is_visible_on_checkout',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
            'comment'  => 'Visible in Checkout'
        )
    );

}

$installer->endSetup();
