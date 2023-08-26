<?php
/**
 * Blugento Feeds
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Feed_Provider_Okaziiro extends Blugento_Feeds_Model_Feed_Abstract
{
    /**
     * Header content type
     * @var string
     */
    protected $_contentType = 'text/xml';

    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'okaziiro';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_okaziiro';
        return $this->_feedSaveModel;
    }

    /**
     * Get feed from database
     *
     * @return string
     * @throws Exception
     */
    public function getFromDb()
    {
        try {
            $this->setFeedSaveModel();
            return Mage::getModel($this->_feedSaveModel)->buildXMLString($this->getProducts(array()));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get products ready for caching or printing
     * @param array $args
     * @return array
     */
    public function getProducts($args)
    {
        $this->_setExecutionLimits();
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $storeId  = $this->_getStoreId($args);
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'description', 'manufacturer',
            'image', 'media_gallery', 'special_from_date', 'special_to_date', 'delivery_time', 'delivery_time_okazii');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $sql = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, de.value AS price, 
                    sp.value AS special_price, st.qty AS qty, des.value AS description, fr.value AS fr, sto.value AS sto';

        if (isset($attributesId['delivery_time'])) {
            $sql .= ', dt.value AS delivery_time';
        }

        if (isset($attributesId['delivery_time_okazii'])) {
            $sql .= ', dto.value AS delivery_time_okazii';
        }

        $sql .= ' FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_int vis
                ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId["visibility"] . ' AND e.type_id in ("simple", "configurable", "grouped", "bundle")
                INNER JOIN catalog_product_entity_int sts
                ON e.entity_id = sts.entity_id AND sts.value = 1 AND sts.attribute_id = ' . $attributesId["status"] . '
                LEFT JOIN catalog_product_entity_varchar name
                ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId["name"] . ' AND name.store_id = 0
                LEFT JOIN catalog_product_entity_decimal de
                ON e.entity_id = de.entity_id AND de.attribute_id = ' . $attributesId["price"] . ' AND de.store_id = 0
                LEFT JOIN catalog_product_entity_decimal sp
                ON e.entity_id = sp.entity_id AND sp.attribute_id = ' . $attributesId["special_price"] . ' AND sp.store_id = 0
                LEFT JOIN cataloginventory_stock_item st
                ON e.entity_id = st.product_id
                LEFT JOIN catalog_product_entity_text des
                ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId["description"] . ' AND des.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        if (isset($attributesId['delivery_time'])) {
            $sql .= ' LEFT JOIN catalog_product_entity_varchar dt
                ON e.entity_id = dt.entity_id AND dt.attribute_id = ' . $attributesId["delivery_time"] . ' AND sto.store_id = 0';
        }

        if (isset($attributesId['delivery_time_okazii'])) {
            $sql .= ' LEFT JOIN catalog_product_entity_varchar dto
                ON e.entity_id = dto.entity_id AND dto.attribute_id = ' . $attributesId["delivery_time_okazii"] . ' AND sto.store_id = 0';
        }

        $products = $readConnection->fetchAll($sql);

        //$categories = $this->_getProductsCategories($readConnection);
        $manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer']);
        $images = $this->_getProductImages($readConnection, $attributesId['image'], $attributesId['media_gallery']);
        $stockArray = $this->_getStockForConfigurableProducts($readConnection, $attributesId["price"]);

        $productsData = array();
        foreach ($products as $product){
            $categoryName = '';
            $categories = $this->_getProductsCategories($readConnection, $product['entity_id']);
            foreach ($categories as $k => $category) {
                if ($product['entity_id'] == $category['entity_id']) {
                    $categoryName = $category['category_name'];
                    unset($categories[$k]);
                }
            }

            $manufacturerName = '';
            foreach ($manufacturers as $k => $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $manufacturerName = $manufac['manufacturer'];
                    unset($manufacturers[$k]);
                }
            }

            $amount = number_format((float)$product['qty'], 2, '.', '');
            if (!$amount) {
                $amount = 100;
            }

            $imagesArray = $images[$product['entity_id']];
            $stocks = $stockArray[$product['entity_id']];

            if (isset($product['delivery_time']) && is_numeric($product['delivery_time'])) {
                $deliveryTime = $product['delivery_time'];
            } else if (isset($product['delivery_time_okazii'])) {
                $deliveryTime = $product['delivery_time_okazii'];
            }

            $productsData[] = array(
                'unique_id'			=> $product['sku'],
                'title'			    => $product['name'],//iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($productName)),
                'category'		    => $categoryName,
                'description'		=> Mage::helper('cms')->getPageTemplateProcessor()->filter($product['description']),
                'price'			    => sprintf('%.2f', $product['price']),
                'discount_price'	=> sprintf('%.2f', $this->_establishPrice($product['price'], $product['special_price'], $product['fr'], $product['sto'])),
                'currency'		    => $currency,
                'amount'		    => $amount,
                'brand'	            => trim($manufacturerName),
                'images'		    => $imagesArray,
                'attributes'        => '',
                'stocks'            => $stocks,
                'delivery_time'     => isset($deliveryTime) ? $deliveryTime : ''
            );
        }

        return $productsData;
    }

    protected function _getStoreId($args)
    {
        // Get store
        if (isset($args['store']) && ($args['store'] != '')) {
            $stores = Mage::app()->getStores();
            foreach ($stores as $i) {
                if ($i->getCode() == $args['store']) {
                    $storeId = $i->getId();
                }
            }
        }

        // Get default store
        if (!isset($storeId)) {
            // Get store ID use this to filter products
            $storeId = Mage::app()->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * Display feed
     *
     * @return string|array
     * @throws Exception
     */
    public function output()
    {
        $this->setContentType();

        header('Content-type: ' . $this->_contentType);
        echo $this->get();
        exit();
    }

    /**
     * Get product line feed
     *
     * @param array $product
     * @param array $args
     * @return string
     */
    public function getProductFeedLine($product, $args)
    {
        return '';
    }

    private function _getAttributesIds($connection, $attributes, $typeId) {
        $attributes = '"' . implode('", "', $attributes) . '"';

        $sql = 'SELECT attribute_id AS id, attribute_code AS name FROM eav_attribute 
                WHERE attribute_code IN (' . $attributes . ') AND entity_type_id = ' . $typeId;

        $result = $connection->fetchAll($sql);

        $attrs = array();
        foreach ($result as $item) {
            $attrs[$item['name']] = $item['id'];
        }

        return $attrs;
    }

    private function _getProductsCategories($connection, $productId) {
        $attributesId = $this->_getAttributesIds($connection, array('name'), 3);

        $categoriesSql = 'SELECT e.entity_id AS entity_id, catname.value AS category_name
                        FROM catalog_product_entity e
                        INNER JOIN catalog_category_product cat
                        ON e.entity_id = cat.product_id
                        INNER JOIN catalog_category_entity_varchar catname
                        ON cat.category_id = catname.entity_id AND catname.attribute_id = ' . $attributesId["name"] . ' AND catname.store_id = 0
                        WHERE e.entity_id = ' . $productId;

        $categories = $connection->fetchAll($categoriesSql);

        return $categories;
    }

    private function _getProductsManufacturer($connection, $attrId) {
        $sql = 'SELECT cint.entity_id AS entity_id, man.value AS manufacturer
                FROM catalog_product_entity_int cint
                INNER JOIN eav_attribute_option_value man
                ON cint.value = man.option_id AND attribute_id = ' . $attrId . ' AND cint.store_id = 0';

        $manufacturers = $connection->fetchAll($sql);
        return $manufacturers;
    }

    private function _getProductImages($connection, $imageAttrId, $galleryAttrId) {
        $sql = 'SELECT e.entity_id AS entity_id, img.value AS image, media.value AS media_gallery
                FROM catalog_product_entity e
                LEFT JOIN catalog_product_entity_varchar img
                ON e.entity_id = img.entity_id AND img.attribute_id = ' . $imageAttrId . ' AND img.store_id = 0
                LEFT JOIN catalog_product_entity_media_gallery media
                ON e.entity_id = media.entity_id AND media.attribute_id = ' . $galleryAttrId;

        $images = $connection->fetchAll($sql);
        
        $imagesArray = array();
        foreach ($images as $image) {
            if ($image['image'] == 'no_selection') {
                $image['image'] = '';
            }

            if (($image['image'] == '' || !$image['image']) && ($image['media_gallery'] == '' || !$image['media_gallery'])) {
                continue;
            }

            $imagesArray[$image['entity_id']][] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/' . $image['image'];
            $imagesArray[$image['entity_id']][] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/' . $image['media_gallery'];

            $imagesArray[$image['entity_id']] = array_unique($imagesArray[$image['entity_id']]);
        }
        return $imagesArray;
    }

    private function _getStockForConfigurableProducts($connection, $priceAttrId) {
        $sql = 'SELECT e.entity_id AS parent_id, rel.child_id AS child_id, cpsal.value AS label, eaov.value AS value, csi.qty AS amount, cped.value AS price
                FROM catalog_product_entity e
                INNER JOIN catalog_product_relation rel
                ON e.entity_id = rel.parent_id AND e.type_id = "configurable"
                LEFT JOIN catalog_product_super_attribute cpsa
                ON rel.parent_id = cpsa.product_id
                LEFT JOIN catalog_product_super_attribute_label cpsal
                ON cpsa.product_super_attribute_id = cpsal.product_super_attribute_id
                LEFT JOIN catalog_product_entity_int cpei
                ON rel.child_id = cpei.entity_id AND cpsa.attribute_id = cpei.attribute_id AND cpei.store_id = 0
                LEFT JOIN eav_attribute_option_value eaov
                ON cpei.value = eaov.option_id
                LEFT JOIN cataloginventory_stock_item csi
                ON rel.child_id = csi.product_id
                LEFT JOIN catalog_product_entity_decimal cped
                ON rel.child_id = cped.entity_id AND cped.attribute_id = ' . $priceAttrId . ' AND cped.store_id = 0';

        $stocks = $connection->fetchAll($sql);

        $stockArray = array();
        foreach ($stocks as $stock) {
            if (!isset($stockArray[$stock['parent_id']])) {
                $stockArray[$stock['parent_id']] = array();
            }
            if (!isset($stockArray[$stock['parent_id']][$stock['child_id']])) {
                $stockArray[$stock['parent_id']][$stock['child_id']] = array();
            }
            $stockArray[$stock['parent_id']][$stock['child_id']]['amount'] = $stock['amount'];
            $stockArray[$stock['parent_id']][$stock['child_id']]['attributes'][] = array('code' => strtolower($stock['label']), 'value' => $stock['value']);
            $stockArray[$stock['parent_id']][$stock['child_id']]['price'] = $stock['price'];
        }
        return $stockArray;
    }
}
