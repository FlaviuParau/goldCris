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

class Blugento_Feeds_Model_Feed_Provider_Pricero extends Blugento_Feeds_Model_Feed_Abstract
{
    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'pricero';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_pricero';
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

        $dataFeedSeparator = $this->_dataFeedSeparator; // default '|'

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'image',
            'description', 'manufacturer', 'special_from_date', 'special_to_date');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $inStockMsg       = $this->getOption('in_stock', 'In stoc');
        $outOfStockMsg    = $this->getOption('out_of_stock', 'Indisponibil');
        $dataFeedCurrency = $this->getOption('currency', 'RON cu TVA');
        $showDescription = $this->getOption('show_description');
        $showImage = $this->getOption('show_image');

        $sql = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS prod_model, name.value AS prod_name, de.value AS prod_price, 
                       sp.value AS prod_spprice, st.is_in_stock AS stock, url.value AS prod_url, image.value AS prod_image, 
                       des.value AS prod_desc, fr.value AS fr, sto.value AS sto
                FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_int vis
                ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId["visibility"] . '
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

        $categories = $this->_getProductsCategories($readConnection, Mage::getStoreConfig('blugento_feeds/pricero/root_category_id'));
        $manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer']);

        $productData = array();

        foreach ($products as $key => $product) {
            if ($showDescription) {
                $prodDesc = $prodDesc = $this->replaceNotInTags("\n", "<BR />", $product['prod_desc']);
                $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
                if ($dataFeedSeparator == '|') {
                    $prodDesc = str_replace('|', ' ', $prodDesc);
                }

                $products[$key]['prod_desc'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($prodDesc);
            } else {
                unset($products[$key]['prod_desc']);
            }

            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['prod_name']));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace('|', ' ', $prodName);
            }

            if ($showImage) {
                $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product['prod_image'];
                if (strpos($prodImage, 'no_selection')) {
                    $prodImage = '';
                }
                $products[$key]['prod_image'] = $prodImage;

            } else {
                unset($products[$key]['prod_image']);
            }

            $prodPrice = $this->_establishPrice($product['prod_price'], $product['prod_spprice'], $product['fr'], $product['sto']);

            $products[$key]['prod_name'] = $prodName;
            $products[$key]['prod_url'] = Mage::getBaseUrl() . $product['prod_url'];
            $products[$key]['manufacturercode'] = $product['prod_model'];
            $products[$key]['prod_price'] = sprintf("%.2f", $prodPrice);
            $products[$key]['garantie'] = '';
            $products[$key]['datafeed_currency'] = $dataFeedCurrency;
            $products[$key]['stock'] = $this->_mapStock($product['stock'], $inStockMsg, $outOfStockMsg);

            $categoriesNames = [];
            foreach ($categories as $category) {
                if ($product['entity_id'] == $category['entity_id']) {
                    $categoriesNames[] = $category['category_name'];
                }
            }
            $products[$key]['category_name'] = implode(' > ', $categoriesNames);

            foreach ($manufacturers as $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $products[$key]['manufacturer'] = $manufac['manufacturer'];
                }
            }

            $productData[] = $this->getProductFeedLine($products[$key], array(
                'dataFeedSeparator' => $dataFeedSeparator
            ));
        }
        return $productData;
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

        $line = $product['prod_model'] . $dataFeedSeparator .
                $product['category_name'] . $dataFeedSeparator .
                $product['manufacturer'] . $dataFeedSeparator .
                $product['prod_name'] . $dataFeedSeparator .
                $product['manufacturercode'] . $dataFeedSeparator .
                $product['prod_price'] . $dataFeedSeparator .
                $product['datafeed_currency'] . $dataFeedSeparator .
                $product['stock'] . $dataFeedSeparator .
                $product['garantie'] . $dataFeedSeparator .
                $product['prod_url'] . $dataFeedSeparator .
                $product['prod_image'] . $dataFeedSeparator .
                $product['prod_desc'] . $dataFeedSeparator;

        return $line;
    }

    protected function _mapStock($stock, $inStock, $outStock) {
        return (int)$stock ? $inStock : $outStock;
    }

    protected function _getAttributesIds($connection, $attributes, $typeId) {
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

    protected function _getProductsCategories($connection, $rootCategory = NULL) {
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
            $categoriesSql .= " WHERE cce.path LIKE " . "'1/" . $rootCategory ."/%'";
        }
        $categories = $connection->fetchAll($categoriesSql);

        return $categories;
    }

    protected function _getProductsManufacturer($connection, $attrId) {
        $sql = 'SELECT cint.entity_id AS entity_id, man.value AS manufacturer
                FROM catalog_product_entity_int cint
                INNER JOIN eav_attribute_option_value man
                ON cint.value = man.option_id AND attribute_id = ' . $attrId . ' AND cint.store_id = 0';

        $manufacturers = $connection->fetchAll($sql);
        return $manufacturers;
    }
}
