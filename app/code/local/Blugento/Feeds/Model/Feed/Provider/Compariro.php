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

class Blugento_Feeds_Model_Feed_Provider_Compariro extends Blugento_Feeds_Model_Feed_Provider_Pricero
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
        $this->_feedName = 'compariro';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_compariro';
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
     *
     * @param array $args
     * @return array Feed lines
     * @throws Exception
     */
    public function getProducts($args)
    {
        $this->_setExecutionLimits();
        $readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $dataFeedSeparator = $this->_dataFeedSeparator; // default '|'

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'description',
            'image', 'manufacturer', 'delivery_time', 'delivery_cost', 'special_from_date', 'special_to_date');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $showDescription = $this->getOption('show_description', 1);
        $showImage = $this->getOption('show_image', 1);

        $categories = $this->_getProductsCategories($readConnection, Mage::getStoreConfig('blugento_feeds/compariro/root_category_id'));
        $manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer']);

        if (isset($attributesId['delivery_cost']) && $attributesId['delivery_cost']) {
            $deliveryCosts = $this->_getAttributeValuesVarchar($readConnection, $attributesId['delivery_cost'], 'delivery_cost');
        }
        if (isset($attributesId['delivery_time']) && $attributesId['delivery_time']) {
            $deliveryTime = $this->_getAttributeValuesVarchar($readConnection, $attributesId['delivery_time'], 'delivery_time');
        }

        $storeView = Mage::getStoreConfig('blugento_feeds/compariro/storeview');

        $sql = 'SELECT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, de.value AS price, sp.value AS special_price, 
                      url.value AS url, image.value AS image, des.value AS description, fr.value AS fr, sto.value AS sto
                FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_int vis
                ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId["visibility"] . '
                INNER JOIN catalog_product_entity_int sts
                ON e.entity_id = sts.entity_id AND sts.value = 1 AND sts.attribute_id = ' . $attributesId["status"] . '
                LEFT JOIN catalog_product_entity_varchar name
                ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId["name"] . ' AND name.store_id = 0
                LEFT JOIN catalog_product_entity_decimal de
                ON e.entity_id = de.entity_id AND de.attribute_id = ' . $attributesId["price"] . ' AND de.store_id = ' . $storeView . '
                LEFT JOIN catalog_product_entity_decimal sp
                ON e.entity_id = sp.entity_id AND sp.attribute_id = ' . $attributesId["special_price"] . ' AND sp.store_id = ' . $storeView . '
                LEFT JOIN catalog_product_entity_varchar url
                ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId["url_path"] . ' AND url.store_id = 0
                LEFT JOIN catalog_product_entity_varchar image
                ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId["image"] . ' AND image.store_id = 0
                LEFT JOIN catalog_product_entity_text des
                ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId["description"] . ' AND des.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        $products = $readConnection->fetchAll($sql);

        $productIds = $this->getProductsIdsByCategoriesFilter($readConnection);

        $productsData = array();
        foreach ($products as $key => $product) {
            // filter products by category
            if ($productIds && !in_array($product['entity_id'], $productIds)) {
                continue;
            }

            if ($showDescription) {
                $prodDesc = $prodDesc = $this->replaceNotInTags("\n", "<BR />", $product['description']);
                $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
                if ($dataFeedSeparator == '|') {
                    $prodDesc = str_replace('|', ' ', $prodDesc);
                }

                $productsData[$key]['description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($prodDesc);
            }

            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace('|', ' ', $prodName);
            }

            if ($showImage) {
                $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . str_replace('//', '/', 'catalog/product/' . $product['image']);
                if (strpos($prodImage, 'no_selection')) {
                    $prodImage = '';
                }
                $productsData[$key]['image_url'] = $prodImage;

            }

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

            $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['fr'], $product['sto']);

            $productsData[$key]['name'] = $prodName;
            $productsData[$key]['product_url'] = Mage::getBaseUrl() . $product['url'];
            $productsData[$key]['price'] = $prodPrice;
            $productsData[$key]['identifier'] = $product['entity_id'];
            $productsData[$key]['productid'] = $product['sku'];

            $categoriesNames = [];
            foreach ($categories as $category) {
                if ($product['entity_id'] == $category['entity_id']) {
                    $categoriesNames[] = $category['category_name'];
                }
            }

            // get category value from other attribute
            $attr = Mage::getResourceModel('catalog/eav_attribute')
                ->loadByCode('catalog_product','compari_categorytext');
            if ($attr->getId()) {
                $productObject = Mage::getModel('catalog/product')->load($product['entity_id']);
                $productsData[$key]['category'] = $productObject->getData('compari_categorytext');
            } else {
                $productsData[$key]['category'] = implode(' > ', $categoriesNames);
            }

            $productsData[$key]['manufacturer'] = '';
            foreach ($manufacturers as $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $productsData[$key]['manufacturer'] = $manufac['manufacturer'];
                }
            }

            $productsData[$key]['delivery_cost'] = '';
            if (isset($deliveryCosts) && count($deliveryCosts)) {
                foreach ($deliveryCosts as $cost) {
                    if ($product['entity_id'] == $cost['entity_id']) {
                        $productsData[$key]['delivery_cost'] = $cost['delivery_cost'];
                    }
                }
            }

            $productsData[$key]['delivery_time'] = '';
            if (isset($deliveryTime) && count($deliveryTime)) {
                foreach ($deliveryTime as $time) {
                    if ($product['entity_id'] == $time['entity_id']) {
                        $productsData[$key]['delivery_time'] = $time['delivery_time'];
                    }
                }
            }
        }
        return $productsData;
    }

    /**
     * Get product line feed
     * - cod unic (cod unic al produsului folosit de Dvs). Acest cod trebuie sa ramana neschimbat pe toata perioada colaborarii cu price.ro, si sa fie asociat aceluiasi produs.
     * - categorie
     * - producator
     * - model
     * - codul producatorului
     * - pret (RON cu TVA, in format 1234.56)
     * - moneda (RON cu TVA)
     * - stoc (camp text: "disponibil pe loc", "2-3 zile de la comanda" etc. - va contine acele informatii pe care le considerati importante in legatura cu disponibilitatea produsului)
     * - garantie (camp text: "24 luni", "3 ani" etc.)
     * - link (catre pagina produsului de pe site-ul Dvs)
     * - imagine (link catre imaginea produsului de pe site-ul Dvs) - imagine fara watermark, de
     * preferinta minim 300x300 pixeli, format .jpg, .gif sau .png
     * - descriere (+ specificatii tehnice), contine si codul producatorului
     *
     * @param array $product
     * @param $args
     * @return string
     */
    public function getProductFeedLine($product, $args)
    {
        $dataFeedSeparator  = $args['dataFeedSeparator'];

        $line = $product['manufacturer'] . $dataFeedSeparator .
                $product['prod_name'] . $dataFeedSeparator .
                $product['category_name'] . $dataFeedSeparator .
                $product['prod_url'] . $dataFeedSeparator .
                $product['prod_price'] . $dataFeedSeparator .
                $product['prod_image'] . $dataFeedSeparator .
                $product['prod_desc'] . $dataFeedSeparator .
                $product['manufacturercode'] . $dataFeedSeparator .
                $product['delivery_cost'] . $dataFeedSeparator .
                $product['delivery_time'];

        return $line;
    }

    /**
     * Return product ids by categories ids
     *
     * @param $connection
     * @return array|null
     */
    protected function getProductsIdsByCategoriesFilter($connection)
    {
        $categoriesIds = $this->getOption('category');

        if ($categoriesIds != 'all') {
            $categoriesIds = str_replace('all,', '', $categoriesIds);

            $sql = "SELECT product_id
                    FROM catalog_category_product
                    WHERE category_id IN ($categoriesIds)";

            try {
                $ids = [];
                foreach ($connection->fetchAll($sql) as $productId) {
                    if (!in_array($productId['product_id'], $ids)) {
                        $ids[] = $productId['product_id'];
                    }
                }

                return $ids;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return null;
    }

    private function _getAttributeValuesVarchar($connection, $attrId, $as) {
        $sql = 'SELECT e.entity_id AS entity_id, var.value AS ' . $as . '
                FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_varchar var
                ON e.entity_id = var.entity_id AND var.attribute_id = ' . $attrId . ' AND var.store_id = 0';

        try {
            $values = $connection->fetchAll($sql);
            return $values;
        } catch (Exception $e) {
                Mage::logException($e);
            }
    }
}
