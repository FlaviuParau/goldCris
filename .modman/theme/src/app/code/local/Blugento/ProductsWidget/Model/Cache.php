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
class Blugento_ProductsWidget_Model_Cache extends Mage_Core_Model_Abstract
{
    /**
     * Save cache data.
     *
     * @param string $cacheKey
     * @param array $products
     * @param null $categoryId
     */
    public function saveCacheInfo($cacheKey, $products, $categoryId=null)
    {
        if (!count($products) || !$cacheKey) {
            return;
        }

        $this->_checkCacheKeyExist($cacheKey);

        $categoryId = $categoryId ? $categoryId : 0;
        $storeId = Mage::app()->getStore()->getId() ? Mage::app()->getStore()->getId() : 1;
        try {
            $tableName = $this->_getResourceConnection()->getTableName('blugento_productswidget_cache');
            foreach ($products as $productId) {
                $sql = "INSERT INTO $tableName  (id, product_id, category_id, cache_key, store_id) VALUES ('', $productId, $categoryId, '$cacheKey', $storeId)";

                $this->_getWriteConnection()->query($sql);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

    }

    /**
     * Check if key already exist and delete it.
     *
     * @param string $cacheKey
     */
    private function _checkCacheKeyExist($cacheKey)
    {
        $tableName = $this->_getResourceConnection()->getTableName('blugento_productswidget_cache');

        try {
            $sql = "SELECT id FROM $tableName WHERE cache_key = '$cacheKey'";
            $result = $this->_getReadConnection()->fetchAll($sql);

            $cacheToDelete = array();
            foreach ($result as $cacheRecord) {
                if (isset($cacheRecord['id'])) {
                    $cacheToDelete[] = $cacheRecord['id'];
                }
            }
            if(!empty($cacheToDelete)) {
                $cacheToDelete = implode(',', $cacheToDelete);
                $sql = "DELETE FROM $tableName WHERE id IN($cacheToDelete)";
                $this->_getWriteConnection()->query($sql);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function invalidateCache($cacheKey)
    {
        Mage::app()->getCacheInstance()->invalidateType($cacheKey);
    }

    /**
     * Clear cache by key.
     *
     * @param string $cacheKey
     */
    public function clearCache($cacheKey)
    {
        foreach ($cacheKey as $key) {
            Mage::getModel('core/cache')->remove($key);
        }
    }

    public function isInCache($id, $productIds=null)
    {
        $tableName = $this->_getResourceConnection()->getTableName('blugento_productswidget_cache');
        $storeId = Mage::app()->getStore()->getId() ? Mage::app()->getStore()->getId() : 1;
        $keys = array();

        if (!$productIds || !count($productIds)) {
            $sql = "SELECT cache_key FROM $tableName WHERE product_id = " . $id . " AND store_id = " . $storeId;
            $result = $this->_getReadConnection()->fetchAll($sql);

            foreach ($result as $key) {
                $keys[] = isset($key['cache_key']) ? $key['cache_key'] : '';
            }

            return $keys;
        }

        if (count($productIds)) {
            $productIds = implode(',', $productIds);
            $sql = "SELECT cache_key FROM $tableName WHERE category_id = " . $id . " AND product_id IN ($productIds)" . " AND store_id = " . $storeId;
            $result = $this->_getReadConnection()->fetchAll($sql);

            foreach ($result as $key) {
                $keys[] = isset($key['cache_key']) ? $key['cache_key'] : '';
            }
        }

        return $keys;
    }

    /**
     * Retrieve the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        return $this->_getResourceConnection()->getConnection('core_read');
    }

    /**
     * Retrieve the write connection
     *
     * @return mixed
     */
    private function _getWriteConnection()
    {
        return $this->_getResourceConnection()->getConnection('core_write');
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }

    /**
     * Delete all widget cache info.
     */
    public function clearAllCache()
    {
        $tableName = $this->_getResourceConnection()->getTableName('blugento_productswidget_cache');

        $sql = "TRUNCATE TABLE $tableName";

        $this->_getWriteConnection()->query($sql);
    }
}
