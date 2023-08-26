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

class Blugento_Feeds_Model_Feed_Provider_Ga extends Blugento_Feeds_Model_Feed_Abstract
{
	/**
	 * @return string
	 */
	public function setDataFeedSeparator()
	{
		if (Mage::helper('blugento_feeds')->getGaDownloadOption() == 'xml') {
			$this->_dataFeedSeparator = '|';
		} else {
			$this->_dataFeedSeparator = ',';
		}
		
		return $this->_dataFeedSeparator;
	}
	
	/**
	 * @return string
	 */
	public function setContentType()
	{
		if (Mage::helper('blugento_feeds')->getGaDownloadOption() == 'xml') {
			$this->_contentType = 'text/xml';
		} elseif (Mage::helper('blugento_feeds')->getGaDownloadOption() == 'csv') {
			$this->_contentType = 'text/csv';
		} else {
			$this->_contentType = 'text/plain';
		}
		
		return $this->_contentType;
	}
    
    /**
     * Set feed name
     * @return string
     */
    public function setFeedName()
    {
        $this->_feedName = 'ga';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
	    if (Mage::helper('blugento_feeds')->getGaDownloadOption() == 'xml') {
		    $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_ga';
	    } else {
		    $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_gaCsv';
	    }
	    
        return $this->_feedSaveModel;
    }
	
	/**
	 * Get feed from database
	 *
	 * @return string|array
	 * @throws Exception
	 */
	public function getFromDb()
	{
		try {
			$this->setContentType();
			$this->setDataFeedSeparator();
			$this->setFeedSaveModel();
			
			if ($this->_contentType == 'text/csv') {
				return $this->getProducts(array());
			} elseif ($this->_contentType == 'text/xml') {
				return Mage::getModel($this->_feedSaveModel)->buildXMLString($this->getProducts(array()));
			} else {
				return implode("\n", $this->getProducts(array()));
			}
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public function output()
	{
		$this->setContentType();
		
		header('Content-type: ' . $this->_contentType);
		echo $this->get();
		exit();
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

        $dataFeedSeparator = $this->_dataFeedSeparator;

        $productAttributes = array('visibility', 'status', 'name', 'price', 'special_price', 'url_path', 'description',
            'image', 'special_from_date', 'special_to_date', 'manufacturer');
        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $showDescription = $this->getOption('show_description', 1);

        $sql = 'SELECT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, de.value AS price, sp.value AS special_price, 
                    url.value AS url, image.value AS image, des.value AS description, fro.value as special_from,
                    sto.value as special_to, sts.value as status, optv.value as manufacturer
                FROM catalog_product_entity e
                INNER JOIN catalog_product_entity_int vis
                ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId['visibility'] . '
                INNER JOIN catalog_product_entity_int sts
                ON e.entity_id = sts.entity_id AND sts.attribute_id = ' . $attributesId['status'] . '
                LEFT JOIN catalog_product_entity_int manu
                ON e.entity_id = manu.entity_id AND manu.attribute_id = ' . $attributesId['manufacturer'] . '
                LEFT JOIN eav_attribute_option_value AS optv
                ON manu.value = optv.option_id
                LEFT JOIN catalog_product_entity_varchar name
                ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId['name'] . ' AND name.store_id = 0
                LEFT JOIN catalog_product_entity_decimal de
                ON e.entity_id = de.entity_id AND de.attribute_id = ' . $attributesId['price'] . ' AND de.store_id = 0
                LEFT JOIN catalog_product_entity_decimal sp
                ON e.entity_id = sp.entity_id AND sp.attribute_id = ' . $attributesId['special_price'] . ' AND sp.store_id = 0
                LEFT JOIN catalog_product_entity_varchar url
                ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId['url_path'] . ' AND url.store_id = 0
                LEFT JOIN catalog_product_entity_varchar image
                ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId['image'] . ' AND image.store_id = 0
                LEFT JOIN catalog_product_entity_text des
                ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId['description'] . ' AND des.store_id = 0
                LEFT JOIN catalog_product_entity_datetime fro
                ON e.entity_id = fro.entity_id AND fro.attribute_id = ' . $attributesId['special_from_date'] . ' AND fro.store_id = 0
                LEFT JOIN catalog_product_entity_datetime sto
                ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId['special_to_date'] . ' AND fro.store_id = 0';

        $products = $readConnection->fetchAll($sql);
        
        $categories = $this->_getProductsCategories($readConnection);

        $productsData = array();
        foreach ($products as $key => $product) {
	        //$productsData[$key]['g:id']  = $product['entity_id'];
            $productsData[$key]['g:mpn'] = $product['sku'];

            $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
            if ($dataFeedSeparator == '|') {
                $prodName = str_replace('|', ' ', $prodName);
            }
            $productsData[$key]['title'] = $prodName;

            $productsData[$key]['description'] = '';
            if ($showDescription) {
                $prodDesc = $prodDesc = $this->replaceNotInTags("\n", "<BR />", $product['description']);
                $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
                if ($dataFeedSeparator == '|') {
                    $prodDesc = str_replace('|', ' ', $prodDesc);
                }
                $productsData[$key]['description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($prodDesc);
            }

            $productsData[$key]['link'] = Mage::getBaseUrl() . $product['url'];

            $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product['image'];
            if (strpos($prodImage, 'no_selection')) {
                $prodImage = '';
            }
            $productsData[$key]['g:image_link'] = $prodImage;

            $productsData[$key]['g:availability'] = $product['status'] == 1 ? 'in stock' : 'out of stock';

            $productsData[$key]['g:price'] = number_format($product['price'], 2);

            $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['special_from'], $product['special_to']);
            $productsData[$key]['g:sale_price'] = sprintf("%.2f", $prodPrice);

            $categoriesNames = [];
            foreach ($categories as $category) {
                if ($product['entity_id'] == $category['entity_id']) {
                    $categoriesNames[] = $category['category_name'];
                }
            }
            $productsData[$key]['g:product_type'] = implode(' > ', $categoriesNames);

            $productsData[$key]['g:brand'] = $product['manufacturer'];
        }
	
	    if (Mage::helper('blugento_feeds')->getGaDownloadOption() == 'csv') {
		    $tableHeaders = array(
			    'g:id'            => 'id',
//			    'g:mpn'           => 'g:mpn',
			    'title'           => 'title',
                'description'     => 'description',
			    'link'            => 'link',
			    'g:image_link'    => 'image_link',
                'g:availability'  => 'availability',
                'g:price'         => 'price',
                'g:sale_price'    => 'sale_price',
                'g:product_type'  => 'product_type',
                'g:brand'         => 'brand',
		    );
		    array_unshift($productsData , $tableHeaders);
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
     * @return array|string
     */
    public function getProductFeedLine($product, $args)
    {
	    $line = array(
		    $product['g:mpn'],
		    $product['g:id'],
		    $product['title'],
		    $product['link'],
		    $product['g:image_link'],
		    $product['description'],
		    $product['g:product_type'],
		    $product['g:price'],
		    $product['g:special_price'],
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
	
	protected function _getProductsCategories($connection) {
		$attributesId = $this->_getAttributesIds($connection, array('name'), 3);
		
		$categoriesSql = 'SELECT e.entity_id AS entity_id, catname.value AS category_name
                        FROM catalog_product_entity e
                        INNER JOIN catalog_category_product cat
                        ON e.entity_id = cat.product_id
                        INNER JOIN catalog_category_entity_varchar catname
                        ON cat.category_id = catname.entity_id AND catname.attribute_id = ' . $attributesId['name'] . ' AND catname.store_id = 0';
		
		$categories = $connection->fetchAll($categoriesSql);
		
		return $categories;
	}
}
