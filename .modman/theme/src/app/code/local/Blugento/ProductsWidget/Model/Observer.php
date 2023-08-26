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
 * @package     Blugento_ProductsWidget
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_ProductsWidget_Model_Observer
{
    /**
     * Dispatch by:: catalog_product_save_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function productSaveBefore(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('blugento_productswidget/general/enabled')){
            return $this;
        }

        $product = $observer->getProduct();

        $isInWidgetCache = $this->_productIsInProductsWidgetCache($product->getId());
        if(!count($isInWidgetCache)) {
            return $this;
        }

        $change = false;
        if ($product->getIsChangedWebsites()
            || $product->dataHasChangedFor('status')
            || $product->dataHasChangedFor('name')
            || $product->dataHasChangedFor('price')
            || $product->dataHasChangedFor('special_price')
            || $product->dataHasChangedFor('visibility')
            || $product->dataHasChangedFor('tax_class_id')
            || $product->dataHasChangedFor('thumbnail')
            || $product->dataHasChangedFor('url_key')
            || $this->_hasStockChanges($product)
        ) {
            $change = 'all';
        }

        if (!$change && ($product->dataHasChangedFor('news_from_date') || $product->dataHasChangedFor('news_to_date'))) {
            $change = 'new';
        }

        /** @var Blugento_ProductsWidget_Model_Cache $cacheModel */
        $cacheModel = Mage::getModel('blugento_productswidget/cache');

        if ($change == 'all') {
            $cacheKeys = $isInWidgetCache;
            $cacheModel->clearCache($cacheKeys);
        }

        if ($change == 'new') {
            $toDelete = array();
            foreach ($isInWidgetCache as $cacheKey) {
                if (strpos($cacheKey, 'NEW')) {
                    $toDelete[] = $cacheKey;
                }
            }
            $cacheModel->clearCache($toDelete);
        }
    }

    /**
     * Check if product is in widget cache.
     *
     * @param int $d
     * @param null|array $productIds
     * @return array
     */
    private function _productIsInProductsWidgetCache($d, $productIds=null)
    {
        /** @var Blugento_ProductsWidget_Model_Cache $cacheModel */
        $cacheModel = Mage::getModel('blugento_productswidget/cache');

        return $cacheModel->isInCache($d, $productIds);
    }

    /**
     * Check if product has inventory changes.
     *
     * @param $product
     * @return bool
     */
    private function _hasStockChanges($product)
    {
        $currentQty     = $product->getStockItem()->getData('qty');
        $currentBackOrd = $product->getStockItem()->getData('backorders');
        $currentInStock = $product->getStockItem()->getData('is_in_stock');

        $origStockData = $product->getOrigData('stock_item')->getOrigData();
        $origQty     = $origStockData['qty'];
        $origBackOrd = $origStockData['backorders'];
        $origInStock = $origStockData['is_in_stock'];

        if ($currentQty != $origQty || $currentBackOrd != $origBackOrd || $currentInStock != $origInStock) {
            return true;
        }

        return false;
    }

    /**
     * Dispatch by:: catalog_category_change_products
     *
     * @param Varien_Event_Observer $observer
     */
    public function categoryChangeProducts(Varien_Event_Observer $observer)
    {
        if(!Mage::getStoreConfig('blugento_productswidget/general/enabled')){
            return;
        }

        $category   = $observer->getCategory();
        $productIds = $observer->getProductIds();

        $isInWidgetCache = $this->_productIsInProductsWidgetCache($category->getId(), $productIds);
        if(!count($isInWidgetCache)) {
            return;
        }

        $toDelete = array();
        foreach ($isInWidgetCache as $cacheKey) {
            if (strpos($cacheKey, 'CATEGORY')) {
                $toDelete[] = $cacheKey;
            }
        }

        /** @var Blugento_ProductsWidget_Model_Cache $cacheModel */
        $cacheModel = Mage::getModel('blugento_productswidget/cache');
        $cacheModel->clearCache($toDelete);
    }

    /**
     * Dispatch by:: adminhtml_cache_refresh_type
     *
     * @param Varien_Event_Observer $observer
     */
    public function cleanCacheType(Varien_Event_Observer $observer)
    {
        if ($observer->getData('type') == 'block_html') {
            try {
                /** @var Blugento_ProductsWidget_Model_Cache $cacheModel */
                $cacheModel = Mage::getModel('blugento_productswidget/cache');
                $cacheModel->clearAllCache();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}
