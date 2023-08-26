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

class Blugento_Feeds_Model_Feed_Provider_Shopmania extends Blugento_Feeds_Model_Feed_Abstract
{
    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'shopmania';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_shopmania';
        return $this->_feedSaveModel;
    }

    /**
     * Get products ready for caching or printing
     *
     * @param array $args
     * @return array Feed lines
     * @throws Exception
     */
    public function getProducts($args)
    {
        $this->_setExecutionLimits();
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        // Force Server settings
        $force = $this->getOption('force', 0, true);
        if ($force == 1) {
            // Rewrite values
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 0);
        }

        // Get store ID, website ID, customer group
        $storeId        = $this->_getStoreId($args);
        $dataFeedSeparator = $this->_dataFeedSeparator; // default '|'

        $convertRate = '';
        $dataFeedCurrency = $this->_getCurrencyFromOptions($storeId, $convertRate);

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'description',
            'image', 'manufacturer', 'delivery_time', 'short_description', 'special_from_date', 'special_to_date');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $showDescription = $this->getOption('description');
        $showImage = $this->getOption('image');

        $productTypes = array();

        // Get grouped products
        $show = $this->getOption('show_grouped_products', 1);
        if ($show != 'off') {
            $productTypes[] = '"grouped"';
        }

        // Get bundle products
        $show = $this->getOption('show_bundle_products', 1);
        if ($show != 'off') {
            $productTypes[] = '"bundle"';
        }

        // Get configurable products
        $show = $this->getOption('show_conf_products', 1);
        if ($show != 'off') {
            $productTypes[] = '"configurable"';
        }

        // Get simple product visibility
        $show = $this->getOption('show_simple_products', 1);
        if ($show != 'off') {
            $productTypes[] = '"simple"';
        }

        $showInStock = $this->getOption('on_stock', 1);
        if ($showInStock) {
            $inStock = array(1);
        } else {
            $inStock = array(0, 1);
        }

        $storeView = Mage::getStoreConfig('blugento_feeds/shopmania/storeview');

        $select = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, st.is_in_stock AS stock, 
                   de.value AS price, sp.value AS special_price, url.value AS url, fr.value AS fr, sto.value AS sto';

        $sql = 'FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_int vis
                ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId["visibility"] . ' AND e.type_id in (' . implode(",", $productTypes) . ')
                INNER JOIN catalog_product_entity_int sts
                ON e.entity_id = sts.entity_id AND sts.value = 1 AND sts.attribute_id = ' . $attributesId["status"] . '
                LEFT JOIN catalog_product_entity_varchar name
                ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId["name"] . ' AND name.store_id = 0
                LEFT JOIN catalog_product_entity_decimal de
                ON e.entity_id = de.entity_id AND de.attribute_id = ' . $attributesId["price"] . ' AND de.store_id = ' . $storeView . '
                LEFT JOIN catalog_product_entity_decimal sp
                ON e.entity_id = sp.entity_id AND sp.attribute_id = ' . $attributesId["special_price"] . ' AND sp.store_id = ' . $storeView . '
                LEFT JOIN cataloginventory_stock_item st
                ON e.entity_id = st.product_id AND st.is_in_stock IN (' . implode(",", $inStock) . ')
                LEFT JOIN catalog_product_entity_varchar url
                ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId["url_path"] . ' AND url.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        if ($showImage) {
            $select .= ', image.value AS image';
            $sql .= ' LEFT JOIN catalog_product_entity_varchar image
                ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId["image"] . ' AND image.store_id = 0';
        }

        if ($showDescription) {
            $select .= ', des.value AS description, short.value AS short_description';
            $sql .= ' LEFT JOIN catalog_product_entity_text des
                     ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId["description"] . ' AND des.store_id = 0
                     LEFT JOIN catalog_product_entity_text short
                     ON e.entity_id = short.entity_id AND short.attribute_id = ' . $attributesId["short_description"] . ' AND des.store_id = 0';
        }

        $query = $select . ' ' . $sql;
        $products = $readConnection->fetchAll($query);

        $showAttributes = $this->getOption('attribute', 1);
        if ($showAttributes) {
            $attributes = $this->_getAttributes($readConnection);
        }

        // Display shipping
        if ($this->isOption('shipping', 1, true)) {
            $shippingValue = '';
        } else {
            $shippingValue = '';
        }

        // Display gtin
        $prodGtin = $this->isOption('gtin', 1, true) ? '' : '';

        //Add GA
        $addGA = $this->isOption('add_tagging', 1, true);

        //Special price
        $showSpecialPrice = $this->isOption('specialprice', 1, true);

        //Add availability
        $av = $this->isOption('availability', 1, true);

        $showCategories = $this->getOption('show_cat', 0, true);
        if ($showCategories) {
            $categories = $this->_getProductsCategories($readConnection, Mage::getStoreConfig('blugento_feeds/shopmania/root_category_id'));
        }

        $manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer']);

        $productData = array();
        $productIds = array();
        foreach($products as $key => $product) {
            $productsData[$key] = $this->_getKeys($showAttributes);

            $productsData[$key]['prod_model'] = $product['sku'];
            $productsData[$key]['prod_id'] = $product['entity_id'];
            $productsData[$key]['gtin'] = $prodGtin;
            $productsData[$key]['shipping_value'] = $shippingValue;
            $productsData[$key]['gender'] = '';

            $description = $this->_formatDescription($product['description'], $product['short_description']);
            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
            $description = str_replace(array("\n", "\r", "\t"), '', strip_tags($description));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace("|", " ", $prodName);
                $description = str_replace("|", " ", $description);
            }
            $productsData[$key]['prod_name'] = $prodName;
            $productsData[$key]['prod_desc'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($description);

            //set price and special price with values from Admin store view if NUll on desired storeview
            if (!$product['price'] || $product['price'] == NULL) {
                $priceQuery = 'SELECT value FROM catalog_product_entity_decimal WHERE entity_id = ' . $product['entity_id'] . ' AND attribute_id = ' . $attributesId["price"] . ' AND store_id = 0';
                $res = $readConnection->fetchRow($priceQuery);
                $product['price'] = $res['value'];
            }
            if (!$product['special_price'] || $product['special_price'] == NULL) {
                $priceQuery = 'SELECT value FROM catalog_product_entity_decimal WHERE entity_id = ' . $product['entity_id'] . ' AND attribute_id = ' . $attributesId["special_price"] . ' AND store_id = 0';
                $res = $readConnection->fetchRow($priceQuery);
                $product['special_price'] = $res['value'];
            }

            $prodPrice = $product['price'];
            if ($showSpecialPrice) {
                $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['fr'], $product['sto']);
            }
            $productsData[$key]['prod_price'] = sprintf("%.2f", $prodPrice);

            if ($showImage) {
                $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/' . $product['image'];
                if (strpos($prodImage, 'no_selection')) {
                    $prodImage = '';
                }
                $productsData[$key]['prod_image'] = $prodImage;
            }

            if ($showCategories) {
                $categoriesNames = array();
                $categoriesNames[] = 'Home';
                foreach ($categories as $k => $category) {
                    if ($product['entity_id'] == $category['entity_id']) {
                        $categoriesNames[] = $category['category_name'];
                        unset($categories[$k]);
                    }
                }
                $productsData[$key]['category_name'] = implode(' > ', $categoriesNames);
            }

            if ($av) {
                $availability = ($product['stock'] == 1) ? 'In stock' : 'Out of stock';
                $productsData[$key]['availability'] = $availability;
            }

            $prodUrl = Mage::getBaseUrl() . $product['url'];
            if ($addGA) {
                $taggingParams = $this->getOption('tagging_params', '');
                $andParam = (preg_match("/\?/", $prodUrl)) ? "&" : "?";
                $prodUrl = $prodUrl . $andParam . $taggingParams;
            }
            $productsData[$key]['prod_url'] = $prodUrl;

            $displayProduct = ($product['stock'] == 1) ? 1 : 0;
            $productsData[$key]['show_product'] = $displayProduct;

            $manufacturerName = '';
            foreach ($manufacturers as $k => $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $manufacturerName = $manufac['manufacturer'];
                    unset($manufacturers[$k]);
                }
            }

            if (isset($attributes) && is_array($attributes)) {
                foreach ($attributes as $k => $attribute) {
                    if ($product['entity_id'] == $attribute['entity_id']) {
                        if ($attribute['label'] == 'Manufacturer') {
                            $productsData[$key]['manufacturer'] = $attribute['ivalue'];
                        }

                        if ($attribute['label'] != 'Manufacturer') {
                            $productsData[$key]['attribute'] .= $attribute['label'] . ': ' . $attribute['ivalue'] . $attribute['dvalue'] . $attribute['vvalue'] . $attribute['tvalue'] . '; ';
                        }
                        unset($attributes[$k]);
                    }
                }
            }
            $productsData[$key]['manufacturer'] = $manufacturerName;
            if ($productsData[$key]['manufacturer'] == '') {
                $productsData[$key]['manufacturer'] = 'Nu';
            }

            if (!in_array($product['entity_id'], $productIds)) {
                $productData[] = $this->getProductFeedLine($productsData[$key], array('dataFeedSeparator' => $dataFeedSeparator, 'dataFeedCurrency'  => $dataFeedCurrency));
                $productIds[] = $product['entity_id'];
            }

        }
        return $productData;
    }

    private function _getProductsManufacturer($connection, $attrId) {
        $sql = 'SELECT cint.entity_id AS entity_id, man.value AS manufacturer
                FROM catalog_product_entity_int cint
                INNER JOIN eav_attribute_option_value man
                ON cint.value = man.option_id AND attribute_id = ' . $attrId . ' AND cint.store_id = 0';

        $manufacturers = $connection->fetchAll($sql);
        return $manufacturers;
    }

    /**
     * Determine store ID
     *
     * @param array $args
     * @return int
     */
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
     * Get feed currency from options
     *
     * @param $storeId
     * @param $convertRate
     * @return mixed|string
     */
    protected function _getCurrencyFromOptions($storeId, &$convertRate)
    {
        $displayCurrency = $this->getOption('display_currency', '');

        if ($displayCurrency != '') {
            $datafeedCurrency = $displayCurrency;
        } else {
            // Get shop currency
            $baseCurrencyCode = Mage::app()->getStore($storeId)->getBaseCurrencyCode();

            $currencyCode = $this->getOption('currency', '');
            if ($currencyCode != '') {
                $currencyValueRate = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, $currencyCode);
                $convertRate = $currencyValueRate[$currencyCode];
                $datafeedCurrency = $currencyCode;
            }
            else {
                $datafeedCurrency = $baseCurrencyCode;
            }
        }

        return $datafeedCurrency;
    }

    /**
     * Get product line feed
     * Category, Manufacturer, Model, ProdCode, ProdName, ProdDescription, ProdURL, ImageURL, Price, Currency, Shipping value, Availability, GTIN (UPC/EAN/ISBN)
     *
     * @param $product
     * @param $args
     * @return string
     */
    public function getProductFeedLine($product, $args)
    {
        $dataFeedSeparator  = $args['dataFeedSeparator'];
        $dataFeedCurrency   = $args['dataFeedCurrency'];

        $line = $product['category_name'] . $dataFeedSeparator .
                $product['manufacturer'] . $dataFeedSeparator .
                $product['prod_model'] . $dataFeedSeparator .
                $product['prod_id'] . $dataFeedSeparator .
                $product['prod_name'] . $dataFeedSeparator .
                $product['prod_desc'] . $dataFeedSeparator .
                $product['prod_url'] . $dataFeedSeparator .
                $product['prod_image'] . $dataFeedSeparator .
                $product['prod_price'] . $dataFeedSeparator .
                $dataFeedCurrency . $dataFeedSeparator .
                $product['shipping_value'] . $dataFeedSeparator .
                $product['availability'] . $dataFeedSeparator .
                $product['gtin'] . $dataFeedSeparator .
                $product['attribute'];


        return $line;
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

    private function _getAttributes($connection) {
        $sql = 'SELECT e.entity_id AS entity_id, eav.frontend_label AS label, opt.value AS ivalue, decim.value AS dvalue, var.value AS vvalue, txt.value AS tvalue
                FROM eav_attribute eav 
                LEFT JOIN catalog_product_entity_int integ
                ON eav.attribute_id = integ.attribute_id AND eav.backend_type = "int" AND eav.is_user_defined = 1
                LEFT JOIN eav_attribute_option_value opt
                ON integ.value = opt.option_id AND integ.attribute_id = eav.attribute_id AND integ.store_id = 0 
                LEFT JOIN catalog_product_entity_decimal decim
                ON eav.attribute_id = decim.attribute_id AND eav.backend_type = "decimal" AND eav.is_user_defined = 1
                LEFT JOIN catalog_product_entity_varchar var
                ON eav.attribute_id = var.attribute_id AND eav.backend_type = "varchar" AND eav.is_user_defined = 1
                LEFT JOIN catalog_product_entity_text txt
                ON eav.attribute_id = txt.attribute_id AND eav.backend_type = "text" AND eav.is_user_defined = 1
                INNER JOIN catalog_product_entity e
                ON integ.entity_id = e.entity_id OR txt.entity_id = e.entity_id OR var.entity_id = e.entity_id OR decim.entity_id = e.entity_id
                INNER JOIN catalog_eav_attribute ceav
                ON eav.attribute_id = ceav.attribute_id AND ceav.is_visible_on_front = 1';

        return $connection->fetchAll($sql);
    }

    /**
     * Get product description
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    private function _formatDescription($description, $shortDescription)
    {
        $show_description   = $this->getOption('description', 1);
        $description_length = $this->getOption('description_length', 0, true);

        // Limit description size
        if ($description_length > 0) {
            $use_desc  = ($show_description == 'short') ? $shortDescription : $description;
            return substr(strip_tags(trim($use_desc)), 0, $description_length);
        }

        switch ($show_description) {
            case 'limited':
                $prod_desc = $description;
                $prod_desc = substr(trim($prod_desc), 0, 600);
                $prod_desc = strip_tags($prod_desc);
                $prod_desc = substr(trim($prod_desc), 0, 300);
                break;
            case 'short':
                $prod_desc = $shortDescription;
                break;
            case 1:
                $prod_desc = $description;
                break;
            default:
                $prod_desc = '';

        }

        return $prod_desc;
    }

    private function _getProductsCategories($connection, $rootCategory = NULL) {
        $attributesId = $this->_getAttributesIds($connection, array('name'), 3);

        $categoriesSql = 'SELECT e.entity_id AS entity_id, catname.value AS category_name
                        FROM catalog_product_entity e
                        INNER JOIN catalog_category_product cat
                        ON e.entity_id = cat.product_id
                        INNER JOIN catalog_category_entity cce
                        ON cce.entity_id = cat.category_id
                        INNER JOIN catalog_category_entity_varchar catname
                        ON cat.category_id = catname.entity_id AND catname.attribute_id = ' . $attributesId["name"] . ' AND catname.store_id = 0';

        if ($rootCategory || $rootCategory != '') {
            $categoriesSql .= " WHERE cce.path LIKE " . "'1/" . $rootCategory . "/%'";
        }

        $categories = $connection->fetchAll($categoriesSql);

        return $categories;
    }

    private function _getKeys($showAttributes) {
        $keys = array(
            'category_name' => '',
            'manufacturer' => '',
            'prod_model' => '',
            'prod_id' => '',
            'prod_name' => '',
            'prod_desc' => '',
            'prod_url' => '',
            'prod_image' => '',
            'prod_price' => '',
            'show_product' => '',
            'availability' => '',
            'gtin' => '',
            'shipping_value' => '',
            'gender' => ''
        );

        if ($showAttributes) {
            $keys['attribute'] = '';
        }

        return $keys;
    }
}
