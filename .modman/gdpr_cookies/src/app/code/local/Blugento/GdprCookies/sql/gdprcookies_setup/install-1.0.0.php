<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_GdprCookies
 * @author      Marius Boia <marius.boia@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('gdprcookies/list'))
    ->addColumn(
        'id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ),
        'ID'
    )
    ->addColumn(
        'cookie_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'default' => NULL
        ),
        'Cookie Name'
    )
    ->addColumn(
        'cookie_category',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        array(
            'default' => 1
        ),
        'Cookie Category'
    )
    ->addColumn(
        'cookie_description',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'default' => NULL
        ),
        'Cookie Description'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ),
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
        ),
        'Updated At'
    );
$installer->getConnection()->createTable($table);


// create Cookie List CMS Block
$content = '';
//if you want one block for each store view, get the store collection
$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', array('gt'=>0))->getAllIds();
//if you want one general block for all the store viwes, uncomment the line below
//$stores = array(0);
foreach ($stores as $store){
    $block = Mage::getModel('cms/block');
    $block->setTitle('Cookies List');
    $block->setIdentifier('cookies-list');
    $block->setStores(array($store));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();

