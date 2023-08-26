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
// update cookies policy cms page
$pageId = $collection = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier', 'politica-de-utilizare-cookie-uri')->getFirstItem()->getId();

if ($pageId){
    $page = Mage::getModel('cms/page')->load($pageId);
    $content = $page->getContent();

    if (strpos($content, '{{block type="cms/block" block_id="cookies-list"}}') === false){
        $content .= '{{block type="cms/block" block_id="cookies-list"}} ';
        $page->setContent($content)->save();
    }
}

//remove unnecessary cookies
$installer->run("DELETE FROM gdprcookies_list WHERE cookie_name like '2Performant';");
$installer->run("DELETE FROM gdprcookies_list WHERE cookie_name like 'Blugento Pop-Up';");
$installer->run("DELETE FROM gdprcookies_list WHERE cookie_name like 'Google tag Manager';");
$installer->run("DELETE FROM gdprcookies_list WHERE cookie_name like 'Blugento CDN';");

//add necessary cookies
$installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('intercom-id-fmjefzxq', 1, 'Acest serviciu este folosit pentru functionalitatea chat-ului in panoul de administrare.');");

$installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('gdprCookie', 1, 'Acest serviciu este folosit pentru functionalitatea pop-up-ului de cookie-uri');");

$installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('marketing', 1, 'Acest serviciu este folosit pentru functionalitatea pop-up-ului de cookie-uri');");

$installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('statistics', 1, 'Acest serviciu este folosit pentru functionalitatea pop-up-ului de cookie-uri');");

$installer->endSetup();