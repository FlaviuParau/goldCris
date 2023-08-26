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
 * @package     Blugento_Theme
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Theme_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Attach theme data after product collection load
     * Dispatch by:: catalog_product_collection_load_after
     *
     * @param Varien_Event_Observer $observer
     * @return $this|void
     */
    public function productCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        return $this;
        /* @var $mediaHelper Blugento_Theme_Helper_Theme */
        $mediaHelper = Mage::helper('blugento_theme/theme');

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $observer->getCollection();

        if ($collection
            instanceof Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection
        ) {
            return;
        }

        $products = $collection->getItems();

        $mediaHelper->attachThemeData($products, $collection->getStoreId());
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function createCustomRegistry(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();

        $productIds = [];
        if ($collection) {
            foreach ($collection as $product) {
                $productIds[] = $product->getId();
            }

            $this->registerCategoriesByProduct($productIds);
            $this->registerRatingsByProduct($productIds);
            $this->registerSwatches();
        }
    }

    /**
     * Create Categories by Product Registry
     *
     * @param $productCollection
     */
    protected function registerCategoriesByProduct($productIds)
    {
        $categoriesByProduct = [];
        if ($productIds) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $sql = 'SELECT product_id,category_id FROM catalog_category_product WHERE product_id IN (' . implode(',', $productIds) . ')';

            $data = $connection->fetchAll($sql);


            foreach ($data as $item) {
                if (isset($item['product_id']) && $item['category_id']) {
                    $categoriesByProduct[$item['product_id']][] = ['category_id' => $item['category_id']];
                }
            }
        }

        if (count($categoriesByProduct) > 0) {
            if (Mage::registry('categories_by_product')) {
                Mage::unregister('categories_by_product');
            }

            if (!Mage::registry('categories_by_product')) {
                Mage::register('categories_by_product', $categoriesByProduct);
            }
        }
    }

    /**
     * Create Ratings by Product Registry
     *
     * @param array $productIds
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function registerRatingsByProduct($productIds)
    {
        $storeId = Mage::app()->getStore()->getStoreId();

        $items = [];
        if ($productIds) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
	        $_helper = Mage::helper('review');
	
	        if (!$_helper->isEnabled()) {
		        $sql = 'SELECT entity_pk_value, reviews_count, rating_summary
                        FROM review_entity_summary
                        WHERE entity_pk_value IN (' . implode(',', $productIds) . ') 
                        AND store_id = ' . $storeId;
	        } else {
		        $sql = 'SELECT rov.entity_pk_value, COUNT(rov.review_id) as reviews_count,
                            avg(rov.percent) as rating_summary
                        FROM rating_option_vote rov 
                        LEFT JOIN review r 
                        ON rov.review_id = r.review_id
                        WHERE rov.entity_pk_value IN (' . implode(',', $productIds) . ')
                        AND rov.review_id IN (
                            SELECT rv.review_id 
                            FROM review rv
                            JOIN review_detail rd
                        	ON rv.review_id = rd.review_id 
                            WHERE rv.status_id IN (1,4)
                            AND store_id = ' . $storeId . '
                        )
                        GROUP BY rov.entity_pk_value';
	        }

            $data = $connection->fetchAll($sql);
	        
            foreach ($data as $item) {
                if (isset($item['entity_pk_value']) && $item['entity_pk_value']) {
                    $items[$item['entity_pk_value']] = $item;
                }
            }
        }

        if (count($items) > 0) {
            if (Mage::registry('ratings_by_product')) {
                Mage::unregister('ratings_by_product');
            }

            if (!Mage::registry('ratings_by_product')) {
                Mage::register('ratings_by_product', $items);
            }
        }
    }

    /**
     * Register swatches collection
     */
    protected function registerSwatches()
    {
        try {
            $swatchesCollection = Mage::getModel('blugento_swatches/swatches')->getCollection();

            if ($swatchesCollection->getSize() > 0) {
                if (Mage::registry('blugento_swatches_collection')) {
                    Mage::unregister('blugento_swatches_collection');
                }

                if (!Mage::registry('blugento_swatches_collection')) {
                    Mage::register('blugento_swatches_collection', $swatchesCollection);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
