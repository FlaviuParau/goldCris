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
    $content .= '{{block type="cms/block" block_id="cookies-list"}} ';
    $page->setContent($content)->save();
}

$installer->endSetup();