<?php
/**
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
 * @package     Blugento_Compare
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Compare_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function limitProductCompare(Varien_Event_Observer $observer)
    {
        /* @var Blugento_Compare_Helper_Data $helper */
        $helper = Mage::helper('blugento_compare');

        $limitProducts = $helper->getMaxCompareProduct();
        $enabled = $helper->enableCompare();
        $limitMessage = $helper->getLimitCompareMessage();

        $items = Mage::getResourceModel('catalog/product_compare_item_collection')
            ->useProductItem(true)
            ->setStoreId(Mage::app()->getStore()->getId());

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        if ($enabled && (count($items) <= $limitProducts)) {
            return;
        }

        $product = $observer->getProduct();

        $session = Mage::getSingleton('catalog/session');

        /* @var $compareList Mage_Catalog_Model_Product_Compare_List */
        $compareList = Mage::getSingleton('catalog/product_compare_list');
        $compareList->removeProduct($product);

        Mage::dispatchEvent('catalog_product_compare_remove_product', array('product' => $product));
        Mage::helper('catalog/product_compare')->calculate();

        $session->getMessages()->clear();
        $session->addNotice($limitMessage);
    }
}
