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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Model_Label extends Mage_Core_Model_Abstract
{
    private $_collection;

    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_productlabels/label');

        $this->_collection = $this->_getLabelCollection();
    }

    /**
     * Unserialize database fields
     *
     * @return Blugento_ProductLabels_Model_Label
     */
    protected function _afterLoad()
    {
        if ($this->getData('categories') !== null && is_string($this->getData('categories'))) {
            $this->setData('categories', explode(',', $this->getData('categories')));
        }
        return parent::_afterLoad();
    }

    /**
     * Serialize fields for database storage
     *
     * @return Blugento_ProductLabels_Model_Label
     */
    protected function _beforeSave()
    {
        if ($this->getData('categories') !== null && is_array($this->getData('categories'))) {
            $this->setData('categories', implode(',', $this->getData('categories')));
        }
        return parent::_beforeSave();
    }

    /**
     * Check if another label with type promo/new is active
     *
     * @param string $type
     * @param array $stores
     * @return bool
     */
    public function canActivate($type, $stores)
    {
        try {
            $collection = $this->_filterCollection($this->_getLabelCollection(false), array('type', 'status'), array($type, 1));

            $valid = true;
            foreach ($collection as $data) {
                $existingStores = explode(',', $data['stores']);
                if (count(array_intersect($stores, $existingStores)) > 0 || in_array(0, $stores) || in_array(0, $existingStores)) {
                    if ($data['id'] != $this->getId()) {
                        $valid = false;
                    }
                }
            }
        } catch (Exception $e) {
            $valid = false;
            Mage::logException($e);
        }

        return $valid;
    }

    /**
     * Check if the label name is unique
     *
     * @param string $name
     * @param array $stores
     * @return bool
     */
    public function isUnique($name, $stores)
    {
        try {
            $collection = $this->_filterCollection($this->_getLabelCollection(false), 'name', $name);

            $valid = true;
            foreach ($collection as $data) {
                $existingStores = explode(',', $data['stores']);
                if (count(array_intersect($stores, $existingStores)) > 0 || in_array(0, $stores) || in_array(0, $existingStores)) {
                    if ($data['id'] != $this->getId()) {
                        $valid = false;
                    }
                }
            }
        } catch (Exception $e) {
            $valid = false;
            Mage::logException($e);
        }
        return $valid;
    }

    /**
     * Return all valid labels for a certain product.
     *
     * @param int $product
     * @param int $storeId
     * @param bool $productPage
     * @param bool $catPage
     * @return array|bool
     */
    public function getActiveLabelsForProduct($product, $storeId, $productPage = false, $catPage = false)
    {
        $productCategories = array();
        if (Mage::registry('categories_by_product') && isset(Mage::registry('categories_by_product')[$product->getId()])) {
            $productCategories = Mage::registry('categories_by_product')[$product->getId()];
        } else {
            $productCategories = $this->_getCategoryIds($product->getId());
        }

        if ($productPage) {
            $labels = $this->_getLabels($productCategories, 'product', $storeId);

            if ($this->isDefaultLabelEnabled('promo', 'product', $storeId)
                && $this->_isProductPromo($product->getSpecialPrice(), $product->getSpecialFromDate(), $product->getSpecialToDate())) {
                $labels[] = $this->_getDefaultLabel('promo', 'product', $storeId);
            }

            if ($this->isDefaultLabelEnabled('new', 'product', $storeId)
                && $this->_isProductNew($product->getNewsFromDate(), $product->getNewsToDate())) {
                $labels[] = $this->_getDefaultLabel('new', 'product', $storeId);
            }

        } elseif ($catPage) {
            $labels = $this->_getLabels($productCategories, 'category', $storeId);

            if ($this->isDefaultLabelEnabled('promo', 'category', $storeId)
                && $this->_isProductPromo($product->getSpecialPrice(), $product->getSpecialFromDate(), $product->getSpecialToDate())) {
                $labels[] = $this->_getDefaultLabel('promo', 'category', $storeId);
            }

            if ($this->isDefaultLabelEnabled('new', 'category', $storeId)
                && $this->_isProductNew($product->getNewsFromDate(), $product->getNewsToDate())) {
                $labels[] = $this->_getDefaultLabel('new', 'category', $storeId);
            }
        } else {
            return false;
        }

        /** @var Blugento_ProductLabels_Helper_Data $helper */
        $helper = Mage::helper('blugento_productlabels');

        $labels = $helper->removeArrayDuplicates($labels, 'id');

        return $labels;
    }

    /**
     * Check if there is any default label by type promo/new is enabled.
     *
     * @param string $type
     * @param string $page
     * @param int $storeId
     * @return bool
     */
    public function isDefaultLabelEnabled($type, $page, $storeId)
    {
        try {
            $collection = $this->_filterCollection($this->_collection, array('type', 'status', 'enabled_on_' . $page), array($type, 1, 1), $storeId);

            if (count($collection) > 0) {
                return true;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * Return all the active labels for product page for a certain category
     *
     * @param array $categoryIds
     * @param string $page
     * @param int $storeId
     * @return array
     */
    private function _getLabels($categoryIds, $page, $storeId)
    {
        $labels = array();
        try {
            $collection = $this->_filterCollection($this->_collection, array('status', 'enabled_on_' . $page), array(1, 1), $storeId);

            foreach ($categoryIds as $id) {
                foreach ($collection as $label) {
                    if ($this->_includeLabel($label, $id['category_id'])) {
                        $labels[] = $label;
                    }
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $labels;
    }

    /**
     * Check if a label is applicable to a certain category
     *
     * @param array $label
     * @param int $catId
     * @return bool
     */
    private function _includeLabel($label, $catId)
    {
        $categoriesIds = explode(',', $label['categories']);

        if (in_array($catId, $categoriesIds)) {
            return true;
        }

        return false;
    }


    /**
     * Return all default labels enabled on product page.
     *
     * @param string $type
     * @param string $page
     * @param int $storeId
     * @return mixed
     */
    private function _getDefaultLabel($type, $page, $storeId)
    {
        try {
            $collection = $this->_filterCollection($this->_collection, array('type', 'status', 'enabled_on_' . $page), array($type, 1, 1), $storeId);

            return $collection[0];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Check if product has special price.
     *
     * @param string $specialPrice
     * @param string $from
     * @param string $to
     * @return bool
     */
    private function _isProductPromo($specialPrice, $from, $to)
    {
        if ($specialPrice) {
            $to = str_replace('00:00:00', '23:59:59', $to);

            if (($from < now() || $from == null) && ($to > now() || $to == null)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if product is new.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    private function _isProductNew($from, $to)
    {
        $to = str_replace('00:00:00', '23:59:59', $to);

        if ($from != null && $to != null) {
            if ($from < now() && $to > now()) {
                return true;
            }
        }

        if ($from == null && $to != null) {
            if ($to > now()) {
                return true;
            }
        }

        if ($from != null && $to == null) {
            if ($from < now()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all categories id by product id.
     *
     * @param int $productId
     * @return array
     */
    private function _getCategoryIds($productId)
    {
        $connection = $this->_getResource()->getReadConnection();

        $sql = 'SELECT category_id FROM catalog_category_product WHERE product_id = ' . $productId;

        return $connection->fetchAll($sql);
    }

    /**
     * Get label collection
     *
     * @param bool $cache
     * @return array
     */
    private function _getLabelCollection($cache = true)
    {
        $labels = array();

        if ($cache && $cachedData = Mage::app()->getCache()->load(Blugento_ProductLabels_Helper_Data::CACHE_ID)) {
            $labels = unserialize($cachedData);
        } else {
            $sql = 'SELECT id, name, status, type, created_type, path, categories, enabled_on_product,
                        position_on_product, enabled_on_category, position_on_category, stores
                    FROM blugento_productlabels_label';

            try {
                $labels = $this->_getConnection()->fetchAll($sql);

                if ($labels) {
                    Mage::app()->getCache()->save(serialize($labels), Blugento_ProductLabels_Helper_Data::CACHE_ID, array(Blugento_ProductLabels_Helper_Data::CACHE_TAG));
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $labels;
    }

    /**
     * @param array $collection
     * @param array|string $keys
     * @param array|string $values
     * @param null|int $storeId
     * @return array
     * @throws Exception
     */
    private function _filterCollection($collection, $keys, $values, $storeId = null)
    {
        if ((is_array($keys) && !is_array($values)) || (is_array($values) && !is_array($keys)) || (count($keys) != count($values))) {
            throw new Exception('Filter parameters are invalid!');
        } else {
            $labels = array();

            foreach ($collection as $data) {
                if ($storeId) {
                    $stores = explode(',', $data['stores']);

                    if (!in_array($storeId, $stores) && !in_array(0, $stores)) {
                        continue;
                    }
                }

                if (is_array($keys)) {
                    $valid = true;
                    foreach ($keys as $k => $key) {
                        if ($data[$key] == $values[$k]) {
                            continue;
                        } else {
                            $valid = false;
                        }
                    }

                    if ($valid) {
                        $labels[] = $data;
                    }
                } else {
                    if ($data[$keys] == $values) {
                        $labels[] = $data;
                    }
                }
            }
        }

        return $labels;
    }

    /**
     * Retrieve connection
     *
     * @param null|string $type
     * @return mixed
     */
    private function _getConnection($type = null)
    {
        if ($type == 'write') {
            return $this->_getResourceConnection()->getConnection('core_write');
        } else {
            return $this->_getResourceConnection()->getConnection('core_read');
        }
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
}