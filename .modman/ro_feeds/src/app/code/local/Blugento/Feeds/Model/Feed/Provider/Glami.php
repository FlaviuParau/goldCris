<?php

class Blugento_Feeds_Model_Feed_Provider_Glami extends Blugento_Feeds_Model_Feed_Abstract
{
	/**
	 * Set feed separator
	 *
	 * @return string
	 */
	public function setDataFeedSeparator()
	{
		$this->_dataFeedSeparator = '|';
		
		return $this->_dataFeedSeparator;
	}
	
	/**
	 * Set feed type
	 *
	 * @return string
	 */
	public function setContentType()
	{
		$this->_contentType = 'text/xml';
		
		return $this->_contentType;
	}
	
	/**
	 * Set feed name
	 *
	 * @return string
	 */
	public function setFeedName()
	{
		$this->_feedName = 'glami';
		
		return $this->_feedName;
	}
	
	/**
	 * Set feed save model
	 *
	 * @return mixed|void
	 */
	public function setFeedSaveModel()
	{
		$this->_feedSaveModel = 'blugento_feeds/feed_save_provider_glami';
		
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
			
			return Mage::getModel($this->_feedSaveModel)->buildXMLString($this->getProducts(array()));
		} catch (Exception $e) {
			throw $e;
		}
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
	 * Get products ready for caching
	 *
	 * @param array $args
	 * @return array Feed lines
	 * @throws Exception
	 */
	public function getProducts($args)
	{
		$this->_setExecutionLimits();
		
		$readConnection    = Mage::getSingleton('core/resource')->getConnection('core_read');
		$dataFeedSeparator = $this->_dataFeedSeparator;
		$productAttributes = array('visibility', 'status', 'manufacturer', 'name', 'price', 'special_price', 'url_path',
			'image', 'description', 'meta_keyword', 'short_description', 'special_from_date', 'special_to_date',
			'material', 'c2c_material', 'marime', 'c2c_marime', 'glami_categorytext', 'glami_listare', 'glami_culoare',
            'media_gallery');
		
		$attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);
		
		$sql = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, de.value AS price, 
		            sp.value AS special_price, url.value AS url, image.value AS image, des.value AS description, 
		            shdes.value AS short_description, fro.value as special_from, sto.value as special_to, 
		            sts.value as status, optv.value as manufacturer, keyword.value as keywords, 
		            st.is_in_stock as in_stock, cat.value as categories, colopt.value as culoare, 
		            lisopt.value as listare ';
		
		if ($attributesId['material'] && !$attributesId['c2c_material']) {
			$sql .= ', cpev.value as material ';
		} elseif (!$attributesId['material'] && $attributesId['c2c_material']) {
			$sql .= ', cpevi.value as material ';
		} elseif ($attributesId['material'] && $attributesId['c2c_material']) {
			$sql .= ', cpev.value as material, cpevi.value as material ';
		}
		
		if ($attributesId['marime'] || $attributesId['c2c_marime']) {
			$sql .= ', cpei.value as marime ';
		}

		$sql .= 'FROM catalog_product_entity e
	        INNER JOIN catalog_product_entity_int vis
	        ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId['visibility'] . '
	        INNER JOIN catalog_product_entity_int sts
	        ON e.entity_id = sts.entity_id AND sts.attribute_id = ' . $attributesId['status'] . ' AND sts.value = 1
	        LEFT JOIN catalog_product_entity_int lis
	        ON e.entity_id = lis.entity_id AND lis.attribute_id = ' . $attributesId['glami_listare'] . '
	        LEFT JOIN eav_attribute_option_value AS lisopt
	        ON lis.value = lisopt.option_id
	        LEFT JOIN catalog_product_entity_int manu
	        ON e.entity_id = manu.entity_id AND manu.attribute_id = ' . $attributesId['manufacturer'] . '
	        LEFT JOIN eav_attribute_option_value AS optv
	        ON manu.value = optv.option_id
	        LEFT JOIN catalog_product_entity_int color
	        ON e.entity_id = color.entity_id AND color.attribute_id = ' . $attributesId['glami_culoare'] . '
	        LEFT JOIN eav_attribute_option_value AS colopt
	        ON color.value = colopt.option_id
	        LEFT JOIN catalog_product_entity_varchar cat
	        ON e.entity_id = cat.entity_id AND cat.attribute_id = ' . $attributesId['glami_categorytext'] . ' AND cat.store_id = 0
	        LEFT JOIN catalog_product_entity_varchar name
	        ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId['name'] . ' AND name.store_id = 0
	        LEFT JOIN catalog_product_entity_decimal de
	        ON e.entity_id = de.entity_id AND de.attribute_id = ' . $attributesId['price'] . ' AND de.store_id = 0
	        LEFT JOIN catalog_product_entity_decimal sp
	        ON e.entity_id = sp.entity_id AND sp.attribute_id = ' . $attributesId['special_price'] . ' AND sp.store_id = 0
	        INNER JOIN cataloginventory_stock_item st
	        ON e.entity_id = st.product_id AND st.is_in_stock = 1 AND st.qty > 0
	        LEFT JOIN catalog_product_entity_varchar url
	        ON e.entity_id = url.entity_id AND url.attribute_id = ' . $attributesId['url_path'] . ' AND url.store_id = 0
	        LEFT JOIN catalog_product_entity_varchar image
	        ON e.entity_id = image.entity_id AND image.attribute_id = ' . $attributesId['image'] . ' AND image.store_id = 0
	        LEFT JOIN catalog_product_entity_text des
	        ON e.entity_id = des.entity_id AND des.attribute_id = ' . $attributesId['description'] . ' AND des.store_id = 0
	        LEFT JOIN catalog_product_entity_text keyword
	        ON e.entity_id = keyword.entity_id AND keyword.attribute_id = ' . $attributesId['meta_keyword'] . ' AND des.store_id = 0
	        LEFT JOIN catalog_product_entity_text shdes
	        ON e.entity_id = shdes.entity_id AND shdes.attribute_id = ' . $attributesId['short_description'] . ' AND shdes.store_id = 0
	        LEFT JOIN catalog_product_entity_datetime fro
	        ON e.entity_id = fro.entity_id AND fro.attribute_id = ' . $attributesId['special_from_date'] . ' AND fro.store_id = 0
	        LEFT JOIN catalog_product_entity_datetime sto
	        ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId['special_to_date'] . ' AND fro.store_id = 0';
		
		if ($attributesId['material'] && !$attributesId['c2c_material']) {
			$sql .= ' LEFT JOIN catalog_product_entity_varchar cpev
                ON e.entity_id = cpev.entity_id AND cpev.attribute_id = ' . $attributesId['material'] . ' AND cpev.store_id = 0';
		} elseif (!$attributesId['material'] && $attributesId['c2c_material']) {
			$sql .= ' LEFT JOIN catalog_product_entity_int cpevi
                ON e.entity_id = cpevi.entity_id AND cpevi.attribute_id = ' . $attributesId['c2c_material'] . ' AND cpevi.store_id = 0';
		} elseif ($attributesId['material'] && $attributesId['c2c_material']) {
			$sql .= ' LEFT JOIN catalog_product_entity_varchar cpev
                ON e.entity_id = cpev.entity_id AND cpev.attribute_id = ' . $attributesId['material'] . '
                LEFT JOIN catalog_product_entity_int cpevi
                ON e.entity_id = cpevi.entity_id AND cpevi.attribute_id = ' . $attributesId['c2c_material'] . ' AND cpev.store_id = 0';
		}

        $sizeCode = 'marime';
		if ($attributesId['marime'] && !$attributesId['c2c_marime']) {
			$sql .= ' LEFT JOIN catalog_product_entity_int cpei
                ON e.entity_id = cpei.entity_id AND cpei.attribute_id = ' . $attributesId['marime'] . ' AND cpei.store_id = 0';
		} elseif (!$attributesId['marime'] && $attributesId['c2c_marime']) {
            $sizeCode = 'c2c_marime';
			$sql .= ' LEFT JOIN catalog_product_entity_int cpei
                ON e.entity_id = cpei.entity_id AND cpei.attribute_id = ' . $attributesId['c2c_marime'] . ' AND cpei.store_id = 0';
		} elseif ($attributesId['marime'] && $attributesId['c2c_marime']) {
			$sql .= ' LEFT JOIN catalog_product_entity_int cpei
                ON e.entity_id = cpei.entity_id AND cpei.attribute_id IN (' . $attributesId['marime'] . ', ' . $attributesId['c2c_marime'] . ') AND cpei.store_id = 0';
		}

        $productsData = array();
		try {
            $products = $readConnection->fetchAll($sql);

            foreach ($products as $key => $product) {
                if (strtolower($product['listare']) == 'da') {
                    $productsData[$key]['ITEM_ID'] = $product['sku'];

                    $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
                    if ($dataFeedSeparator == '|') {
                        $prodName = str_replace('|', ' ', $prodName);
                    }
                    $productsData[$key]['PRODUCTNAME'] = str_replace(',', ' ', $prodName);

                    $description = Mage::getStoreConfig('blugento_feeds/glami/description') == 1 ? $product['description'] : $product['short_description'];
                    $prodDesc = $this->replaceNotInTags("\n", "<BR />", $description);
                    $prodDesc = str_replace(array("\r", "\t"), '', strip_tags($prodDesc));
                    if ($dataFeedSeparator == '|') {
                        $prodDesc = str_replace('|', ' ', $prodDesc);
                    }
                    $productsData[$key]['DESCRIPTION'] = Mage::helper('cms')->getPageTemplateProcessor()->filter(str_replace(',', ' ', $prodDesc));

                    $productsData[$key]['URL'] = Mage::getBaseUrl() . $product['url'];

                    $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product['image'];
                    if (strpos($prodImage, 'no_selection') || !isset($product['image'])) {
                        $prodImage = '';
                    }
                    $productsData[$key]['IMGURL'] = $prodImage;

                    $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['special_from'], $product['special_to']);
                    $productsData[$key]['PRICE_VAT'] = number_format($prodPrice, 2, ',', '');

                    $productsData[$key]['MANUFACTURER'] = $product['manufacturer'];

                    $productsData[$key]['CATEGORYTEXT'] = $product['categories'];

                    if ($product['material'] != '') {
                        $material = explode(' ', $product['material']);

                        if (count($material) > 1) {
                            $productsData[$key]['MATERIAL'] = array(
                                'PARAM_NAME' => 'material',
                                'VAL' => $material[1],
                                'PERCENTAGE' => $material[0],
                            );
                        } else {
                            $material = explode(' ', Mage::getModel('catalog/product')
                                ->load($product['entity_id'])
                                ->getAttributeText('c2c_material'));

                            if (count($material) > 1) {
                                $productsData[$key]['MATERIAL'] = array(
                                    'PARAM_NAME' => 'material',
                                    'VAL' => $material[1],
                                    'PERCENTAGE' => $material[0],
                                );
                            } else {
                                $productsData[$key]['MATERIAL'] = array(
                                    'PARAM_NAME' => 'material',
                                    'VAL' => $material[0],
                                );
                            }

                        }
                    }

                    if ($product['marime'] != '') {
                        $productsData[$key]['MARIME'] = array(
                            'PARAM_NAME' => 'marime',
                            'VAL' => Mage::getModel('catalog/product')
                                ->load($product['entity_id'])
                                ->getAttributeText($sizeCode),
                        );
                    }

                    if ($product['culoare'] != '') {
                        $productsData[$key]['CULOARE'] = array(
                            'PARAM_NAME' => 'culoare',
                            'VAL' => $product['culoare'],
                        );
                    }

                    $productsData[$key]['DELIVERY_DATE'] = 0;

                    $gallery = $this->_getProductGallery($readConnection, $product['entity_id'], $attributesId['media_gallery']);
                    if ($gallery && count($gallery) > 0) {
                        foreach ($gallery as $img) {
                            if ($img['image'] != $product['image']) {
                                $altenativeImg = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $img['image'];
                                $productsData[$key]['IMGURL_ALTERNATIVE'][] = $altenativeImg;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
		    Mage::logException($e);
        }

		return $productsData;
	}
	
	/**
	 * Get product line feed
	 *
	 * @param array $product
	 * @param $args
	 * @return array
	 */
	public function getProductFeedLine($product, $args)
	{
		return array(
			$product['ITEM_ID'],
			$product['PRODUCTNAME'],
			$product['DESCRIPTION'],
			$product['URL'],
			$product['IMGURL'],
			$product['PRICE_VAT'],
			$product['MANUFACTURER'],
			$product['CATEGORYTEXT'],
			$product['PARAM'],
			$product['DELIVERY_DATE'],
		);
	}
	
	/**
	 * Get product attributes
	 *
	 * @param $connection
	 * @param array $attributes
	 * @param int $typeId
	 * @return array
	 */
	private function _getAttributesIds($connection, $attributes, $typeId)
	{
		$attributes = '"' . implode('", "', $attributes) . '"';
		
		$sql = 'SELECT attribute_code AS name, attribute_id AS id FROM eav_attribute
                WHERE attribute_code IN (' . $attributes . ') AND entity_type_id = ' . $typeId;

		try {
            return $connection->fetchPairs($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
	}

	private function _getProductGallery($connection, $productId, $galleryId)
    {
        $sql = 'SELECT value AS image 
                FROM catalog_product_entity_media_gallery
                WHERE attribute_id = ' . $galleryId . ' AND entity_id = ' . $productId;

        try {
            $images = $connection->fetchAll($sql);

            return $images;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }
}
