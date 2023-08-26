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

class Blugento_Feeds_Model_Feed_Provider_Paralero extends Blugento_Feeds_Model_Feed_Provider_Pricero
{
    /**
     * Data feed separator
     * @var string
     */
    protected $_dataFeedSeparator = ',';

    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'paralero';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_paralero';
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

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_key', 'image',
            'description', 'manufacturer', 'special_from_date', 'special_to_date', 'isbn');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $dataFeedCurrency = $this->getOption('currency', 'RON cu TVA');
        $showDescription = $this->getOption('show_description', 1);
        $showImage = $this->getOption('show_image', 1);

        $sql = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS prod_model, name.value AS prod_name, de.value AS prod_price, 
                  sp.value AS prod_spprice, st.is_in_stock AS stock, url.value AS prod_url, image.value AS prod_image, 
                  des.value AS prod_desc, fr.value AS fr, sto.value AS sto ';

        if ($attributesId['isbn']) {
            $sql .= ' , isbn.value as isbn ';
        }

        $sql .= 'FROM catalog_product_entity e
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
                ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId["url_key"] . ' AND url.store_id = 0
                LEFT JOIN catalog_product_entity_varchar image
                ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId["image"] . ' AND image.store_id = 0
                LEFT JOIN catalog_product_entity_text des
                ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId["description"] . ' AND des.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        if ($attributesId['isbn']) {
            $sql .= ' LEFT JOIN catalog_product_entity_varchar isbn
                ON e.entity_id = isbn.entity_id AND isbn.attribute_id = ' . $attributesId['isbn'] . ' AND isbn.store_id = 0';
        }



        $products = $readConnection->fetchAll($sql);

        $categories = $this->_getProductsCategories($readConnection);
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

            $forceCurrencyCode = Mage::getStoreConfig('blugento_feeds/paralero/force_currency');
            $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            $prodPrice = $product['prod_price'];
            if ($forceCurrencyCode && $forceCurrencyCode != $currencyCode) {
                $prodPrice = Mage::helper('directory')->currencyConvert($prodPrice, $currencyCode, $forceCurrencyCode);
            }

            if (isset($product['prod_spprice']) && $product['prod_spprice']) {
                $prodSpecialPrice = $this->_establishPrice($product['prod_price'], $product['prod_spprice'], $product['fr'], $product['sto']);

                if ($forceCurrencyCode && $forceCurrencyCode != $currencyCode) {
                    $prodSpecialPrice = Mage::helper('directory')->currencyConvert($prodSpecialPrice, $currencyCode, $forceCurrencyCode);
                }

                $prodPrice = $prodSpecialPrice < $prodPrice
                    ? sprintf("%.2f", $prodPrice) . '/' . sprintf("%.2f", $prodSpecialPrice)
                    : sprintf("%.2f", $prodPrice);
            } else {
                $prodPrice = sprintf("%.2f", $prodPrice);
            }

            $removeHtml = (int)Mage::getStoreConfig('blugento_feeds/paralero/url_extension');
            $productUrl = $removeHtml ? $product['prod_url'] : $product['prod_url'] . '.html';

            $products[$key]['prod_name'] = $prodName;
            $products[$key]['prod_url'] = Mage::getBaseUrl() . $productUrl;
            $products[$key]['manufacturercode'] = $product['prod_model'];
            $products[$key]['prod_price'] = $prodPrice;
            $products[$key]['garantie'] = '';
            $products[$key]['datafeed_currency'] = $dataFeedCurrency;
            $products[$key]['stock'] = $this->_mapStock($product['stock'], 1, 0);

            $categoryName = '';
            foreach ($categories as $category) {
                if ($product['entity_id'] == $category['entity_id']) {
                    if (!$categoryName) {
                        $categoryName .= $category['category_name'];
                    } else {
                        $subcategory = $category['category_name'];
                    }
                }
            }
            $products[$key]['category_name'] = $categoryName;

            if (isset($subcategory)) {
                $products[$key]['subcategory'] = $subcategory;
                unset($subcategory);
            }

            foreach ($manufacturers as $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $products[$key]['manufacturer'] = $manufac['manufacturer'];
                }
            }

            $feedLine = $this->getProductFeedLine($products[$key], array(
                'dataFeedSeparator' => $dataFeedSeparator
            ));

            if ($attributesId['isbn']) {
                $feedLine .= ',"' . str_replace('"', '', $product['isbn']) . '"';
            }

            $productData[] = $feedLine;

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

        $line = $this->formatCSV($product['prod_name'], $dataFeedSeparator) .
            $this->formatCSV($product['prod_desc'], $dataFeedSeparator) .
            $this->formatCSV('', $dataFeedSeparator) .
            $this->formatCSV($product['prod_price'], $dataFeedSeparator) .
            $this->formatCSV($product['category_name'], $dataFeedSeparator) .
            $this->formatCSV($product['subcategory'], $dataFeedSeparator) .
            $this->formatCSV($product['prod_url'], $dataFeedSeparator) .
            $this->formatCSV($product['prod_image'], $dataFeedSeparator) .
            $this->formatCSV($product['prod_model'], $dataFeedSeparator) .
            $this->formatCSV(0, $dataFeedSeparator) .
            $this->formatCSV($product['manufacturer'], $dataFeedSeparator) .
            $this->formatCSV($product['stock'], $dataFeedSeparator) .
            $this->formatCSV('', '');

        return $line;
    }

    protected function formatCSV($string, $dataFeedSeparator)
    {
        return '"' . str_replace('"', '', $string) . '"' . $dataFeedSeparator;
    }
}
