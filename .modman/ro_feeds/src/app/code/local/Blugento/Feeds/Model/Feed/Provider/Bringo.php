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

class Blugento_Feeds_Model_Feed_Provider_Bringo extends Blugento_Feeds_Model_Feed_Abstract
{
    /**
     * Header content type
     * @var string
     */
    protected $_contentType = 'text/csv';

    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'bringo';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_bringo';
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

        $dataFeedSeparator = ','; // default '|'

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'description',
            'image', 'short_description', 'weight', 'c2c_ingredients', 'c2c_poveste', 'c2c_tip_gramaj', 'c2c_cod_ean',
            'manufacturer', 'special_from_date', 'special_to_date');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $sql = 'SELECT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, st.qty as qty, de.value AS price, 
                       sp.value AS special_price, url.value AS url, image.value AS image, des.value AS description, 
                       short.value AS short_description, fr.value AS fr, sto.value AS sto
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
                LEFT JOIN catalog_product_entity_text short
                ON e.entity_id = short.entity_id AND short.attribute_id = ' . $attributesId["short_description"] . ' AND des.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        $products = $readConnection->fetchAll($sql);

        $categories = $this->_getProductsCategories($readConnection);
        $manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer']);
        $attributes = $this->_getAttributesValues($readConnection, $attributesId);

        $productsData = array();
        foreach ($products as $key => $product) {
            $productsData[$key] = $this->_orderKeys();
            $productsData[$key][4] = $product['sku']; //Cod intern

            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace('|', ' ', $prodName);
            }
            $this->_sanitizeString($prodName);

            $productsData[$key][2] = $prodName; //Titlu produs

            $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product['image'];
            if (strpos($prodImage, 'no_selection')) {
                $prodImage = '';
            }
            $productsData[$key][14] = $prodImage; //URL foto

            $prodDesc = $prodDesc = $this->replaceNotInTags("\n", "<BR />", $product['description']);
            $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
            if ($dataFeedSeparator == '|') {
                $prodDesc = str_replace('|', ' ', $prodDesc);
            }

            $productsData[$key][8] = Mage::helper('cms')->getPageTemplateProcessor()->filter($prodDesc); //Descriere
            $productsData[$key][11] = $product['qty']; //Stoc
            $productsData[$key][12] = sprintf('%.2f', $this->_establishPrice($product['price'], $product['special_price'], $product['fr'], $product['sto'])); //Pret cu TVA
            $productsData[$key][13] = sprintf('%.2f', $product['price']); //Pret vechi cu TVA
            $productsData[$key][3] = Mage::helper('cms')->getPageTemplateProcessor()->filter($product['short_description']); //Descriere scurta

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
            $productsData[$key][0] = $categoryName; //Categoria

            if (isset($subcategory)) {
                $productsData[$key][1] = $subcategory; //Subcategoria
                unset($subcategory);
            }

            foreach ($manufacturers as $manufac) {
                if ($product['entity_id'] == $manufac['entity_id']) {
                    $productsData[$key][6] = $manufac['manufacturer']; //Producator
                }
            }

            foreach ($attributes as $att) {
                if ($product['entity_id'] == $att['entity_id']) {

                    if ($att['c2c_tip_gramaj'] == 'g') {
                        $att['c2c_tip_gramaj'] = 'kg';
                        $att['weight'] = $att['weight'] / 1000;
                    }

                    $productsData[$key][10] = $att['weight']; //Gramaj
                    $productsData[$key][5] = $att['c2c_cod_ean']; //Cod produs(Cod de bare)
                    $productsData[$key][9] = $att['c2c_tip_gramaj']; //Unitate de masura
                    $productsData[$key][7] = strip_tags($att['c2c_ingredients'] . ' ' . $att['c2c_poveste']); //Ingrediente + Alergeni
                }
            }
        }

        $tableHeader = array(
            0 => 'Categoria',
            1 => 'Subcategoria',
            2 => 'Titlu produs',
            3 => 'Descriere scurta',
            4 => 'Cod intern',
            5 => 'Cod produs(Cod de bare)',
            6 => 'Producator',
            7 => 'Ingrediente + Alergeni',
            8 => 'Descriere',
            9 => 'Unitate de masura',
            10 => 'Gramaj',
            11 => 'Stoc',
            12 => 'Pret cu TVA',
            13 => 'Pret vechi cu TVA',
            14 => 'Url foto'
        );

        array_unshift($productsData , $tableHeader);
        return $productsData;
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
        $line = array(
            $product['parent_category_name'],
            $product['category_name'],
            $product['prod_name'],
            $product['short_description'],
            $product['product_code'],
            $product['cod_bare'],
            $product['manufacturer'],
            $product['ingrediente_alergeni'],
            $product['prod_desc'],
            $product['um'],
            $product['weight'],
            $product['stock'],
            $product['prod_price'],
            $product['old_price'],
            $product['prod_image']
        );

        return $line;
    }

    protected function _sanitizeString($string)
    {
        return str_replace(
            array('ă', 'â', 'î', 'ţ', 'ş', 'Î', 'Ș', 'Ț', 'Â', 'Ă'),
            array('a', 'a', 'i', 't', 's', 'I', 'S', 'T', 'A', 'A'),
            $string);
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

    private function _getProductsCategories($connection) {
        $attributesId = $this->_getAttributesIds($connection, array('name'), 3);

        $categoriesSql = 'SELECT e.entity_id AS entity_id, catname.value AS category_name
                        FROM catalog_product_entity e
                        INNER JOIN catalog_category_product cat
                        ON e.entity_id = cat.product_id
                        INNER JOIN catalog_category_entity_varchar catname
                        ON cat.category_id = catname.entity_id AND catname.attribute_id = ' . $attributesId["name"] . ' AND catname.store_id = 0';

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
    
    private function _getAttributesValues($connection, $attributesId) {
        $select[] = 'e.entity_id AS entity_id, w.value AS weight';
        $sql = 'FROM catalog_product_entity e
                LEFT JOIN catalog_product_entity_decimal w
                ON e.entity_id = w.entity_id AND w.attribute_id = ' . $attributesId["weight"] . ' AND w.store_id = 0';
        if (isset($attributesId['c2c_ingredients']) && $attributesId['c2c_ingredients']) {
            $select[] = 'ing.value AS c2c_ingredients';
            $sql .= ' LEFT JOIN catalog_product_entity_text ing
                    ON e.entity_id = ing.entity_id AND ing.attribute_id = ' . $attributesId["c2c_ingredients"] . ' AND ing.store_id = 0';
        }

        if (isset($attributesId['c2c_poveste']) && $attributesId['c2c_poveste']) {
            $select[] = 'pov.value AS c2c_poveste';
            $sql .= ' LEFT JOIN catalog_product_entity_text pov
                    ON e.entity_id = pov.entity_id AND pov.attribute_id = ' . $attributesId["c2c_poveste"] . ' AND pov.store_id = 0';
        }

        if (isset($attributesId['c2c_tip_gramaj']) && $attributesId['c2c_tip_gramaj']) {
            $select[] = 'gr.value AS c2c_tip_gramaj';
            $sql .= ' LEFT JOIN catalog_product_entity_int gr
                    ON e.entity_id = gr.entity_id AND gr.attribute_id = ' . $attributesId["c2c_tip_gramaj"] . ' AND gr.store_id = 0';
        }

        if (isset($attributesId['c2c_cod_ean']) && $attributesId['c2c_cod_ean']) {
            $select[] = 'ean.value AS c2c_code_ean';
            $sql .= ' LEFT JOIN catalog_product_entity_varchar ean
                ON e.entity_id = ean.entity_id AND ean.attribute_id = ' . $attributesId["c2c_cod_ean"] . ' AND ean.store_id = 0';
        }

        $query = 'SELECT ' . implode(', ', $select) . ' ' . $sql;
        return $connection->fetchAll($query);
    }

    private function _orderKeys() {
        return array(
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
            12 => '',
            13 => '',
            14 => '',
        );
    }
}
