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

class Blugento_Feeds_Model_Feed_Provider_Mabor extends Blugento_Feeds_Model_Feed_Abstract
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
        $this->_feedName = 'mabor';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_mabor';
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
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();

        $dataFeedSeparator = ','; // default '|'

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'image',
            'special_from_date', 'special_to_date');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $sql = 'SELECT e.entity_id AS entity_id, name.value AS name, de.value AS price, sp.value AS special_price, 
                  url.value AS url, image.value AS image, fr.value AS fr, sto.value AS sto
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
                LEFT JOIN catalog_product_entity_varchar url
                ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId["url_path"] . ' AND url.store_id = 0
                LEFT JOIN catalog_product_entity_varchar image
                ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId["image"] . ' AND image.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fr
                ON e.entity_id = fr.entity_id AND fr.attribute_id = ' . $attributesId["special_from_date"] . ' AND fr.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId["special_to_date"] . ' AND sto.store_id = 0';

        $products = $readConnection->fetchAll($sql);

        $productsData = array();
        foreach ($products as $key => $product) {
            $productsData[$key][0] = $product['entity_id'];

            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace('|', ' ', $prodName);
            }

            $productsData[$key][1] = $prodName;
            $productsData[$key][2] = Mage::getBaseUrl() . $product['url'];

            $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['fr'], $product['sto']);
            $productsData[$key][3] = sprintf("%.2f", $prodPrice) . ' ' . $currency_code;

            $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product['image'];
            if (strpos($prodImage, 'no_selection')) {
                $prodImage = '';
            }

            $productsData[$key][4] = $prodImage;
        }

        $tableHeaders = array(
            0 => 'ID',
            1 => 'Item title',
            2 => 'Final URL',
            3 => 'price',
            4 => 'Image URL'
        );
        array_unshift($productsData , $tableHeaders);

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
        $line = array(
            $product['prod_id'],
            $product['prod_name'],
            $product['prod_url'],
            $product['prod_price'] . ' ' . $args['currency'],
            $product['prod_image']
        );

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
}
