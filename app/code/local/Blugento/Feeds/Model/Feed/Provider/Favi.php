<?php

class Blugento_Feeds_Model_Feed_Provider_Favi extends Blugento_Feeds_Model_Feed_Abstract
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
        $this->_feedName = 'favi';

        return $this->_feedName;
    }

    /**
     * Set feed save model
     *
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_favi';

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
            'favi_categorytext', 'media_gallery', 'delivery_time', 'delivery_cost');

        $attributesId = $this->_getAttributesIds($readConnection, $productAttributes, 4);

        $sql = 'SELECT DISTINCT e.entity_id AS entity_id, e.sku AS sku, name.value AS name, de.value AS price, 
		            sp.value AS special_price, url.value AS url, image.value AS image, des.value AS description, 
		            shdes.value AS short_description, fro.value as special_from, sto.value as special_to, 
		            sts.value as status, optv.value as manufacturer, keyword.value as keywords, 
		            dt.value as delivery_time, dc.value as delivery_cost, cat.value as favi_categorytext ';

        $sql .= 'FROM catalog_product_entity e
	        INNER JOIN catalog_product_entity_int vis
	        ON e.entity_id = vis.entity_id AND vis.value = 4 AND vis.attribute_id = ' . $attributesId['visibility'] . '
	        INNER JOIN catalog_product_entity_int sts
	        ON e.entity_id = sts.entity_id AND sts.attribute_id = ' . $attributesId['status'] . ' AND sts.value = 1
	        LEFT JOIN catalog_product_entity_int manu
	        ON e.entity_id = manu.entity_id AND manu.attribute_id = ' . $attributesId['manufacturer'] . '
	        LEFT JOIN eav_attribute_option_value AS optv
	        ON manu.value = optv.option_id
	        LEFT JOIN catalog_product_entity_varchar name
	        ON name.entity_id = e.entity_id AND name.attribute_id = ' . $attributesId['name'] . ' AND name.store_id = 0
	        LEFT JOIN catalog_product_entity_varchar dt
	        ON dt.entity_id = e.entity_id AND dt.attribute_id = ' . $attributesId['delivery_time'] . ' AND dt.store_id = 0
	        LEFT JOIN catalog_product_entity_varchar dc
	        ON dc.entity_id = e.entity_id AND dc.attribute_id = ' . $attributesId['delivery_cost'] . ' AND dc.store_id = 0
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
	        LEFT JOIN catalog_product_entity_text keyword
	        ON e.entity_id = keyword.entity_id AND keyword.attribute_id = ' . $attributesId['meta_keyword'] . ' AND keyword.store_id = 0
	        LEFT JOIN catalog_product_entity_text shdes
	        ON e.entity_id = shdes.entity_id AND shdes.attribute_id = ' . $attributesId['short_description'] . ' AND shdes.store_id = 0
	        LEFT JOIN catalog_product_entity_datetime fro
	        ON e.entity_id = fro.entity_id AND fro.attribute_id = ' . $attributesId['special_from_date'] . ' AND fro.store_id = 0
	        LEFT JOIN catalog_product_entity_datetime sto
	        ON e.entity_id = sto.entity_id AND sto.attribute_id = ' . $attributesId['special_to_date'] . ' AND sto.store_id = 0 
	        INNER JOIN cataloginventory_stock_item st
	        ON e.entity_id = st.product_id AND st.is_in_stock = 1 AND st.qty > 0 ';

        if (Mage::getStoreConfig('blugento_feeds/favi/include_only_favicategory')) {
            $sql .= 'INNER JOIN catalog_product_entity_varchar cat
	                 ON cat.entity_id = e.entity_id AND cat.attribute_id = ' . $attributesId['favi_categorytext'] . ' AND cat.store_id = 0 AND cat.value IS NOT NULL;';
        } else {
            $sql .= 'LEFT JOIN catalog_product_entity_varchar cat
	                 ON cat.entity_id = e.entity_id AND cat.attribute_id = ' . $attributesId['favi_categorytext'] . ' AND cat.store_id = 0';
        }

        $productsData = array();
        try {
            $products = $readConnection->fetchAll($sql);

            $replaceFlag = false;
            if (Mage::getStoreConfig('blugento_feeds/favi/replace_attributes')) {
                $replaceFlag = true;
            }

            foreach ($products as $key => $product) {

                $productsData[$key]['Identifier'] = $product['sku'];
                $productsData[$key]['Manufacturer'] = $product['manufacturer'];


                $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($product['name']));
                if ($dataFeedSeparator == '|') {
                    $prodName = str_replace('|', ' ', $prodName);
                }
                $productsData[$key]['Name'] = str_replace(',', ' ', $prodName);

                $productsData[$key]['Product_url'] = Mage::getBaseUrl() . $product['url'];

                $prodPrice = $this->_establishPrice($product['price'], $product['special_price'], $product['special_from'], $product['special_to']);
                $productsData[$key]['Price'] = floatval(number_format($prodPrice, 2, ',', ''));

                $prodImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . str_replace('//', '/', 'catalog/product/' . $product['image']);
                if (strpos($prodImage, 'no_selection') || !isset($product['image'])) {
                    $prodImage = '';
                }
                $productsData[$key]['Image_url'] = $prodImage;

                $gallery = $this->_getProductGallery($readConnection, $product['entity_id'], $attributesId['media_gallery']);
                if ($gallery && count($gallery) > 0) {
                    $i = 1;
                    foreach ($gallery as  $img) {
                        if ($img['image'] != $product['image']) {
                            $imageKey = 'Image_url_' . (string)$i;
                            $altenativeImg = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . str_replace('//', '/', 'catalog/product/' . $img['image']);
                            $productsData[$key][$imageKey] = $altenativeImg;
                        }
                        $i++;
                    }
                }

                $productsData[$key]['Category'] = $product['favi_categorytext'];

                $description = Mage::getStoreConfig('blugento_feeds/favi/description') == 1 ? $product['description'] : $product['short_description'];
                $prodDesc = $this->replaceNotInTags("\n", "<BR />", $description);
                $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
                if ($dataFeedSeparator == '|') {
                    $prodDesc = str_replace('|', ' ', $prodDesc);
                }
                $productsData[$key]['Description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter(str_replace(',', ' ', $prodDesc));

                $productObject = Mage::getModel('catalog/product')->load($product['entity_id']);

                $productsData[$key]['Delivery_Time'] = is_numeric($product['delivery_time']) ? $product['delivery_time'] : $productObject->getData('delivery_time_favi');
                $productsData[$key]['Delivery_Cost'] = is_numeric($product['delivery_cost']) ? $product['delivery_cost'] : $productObject->getData('delivery_cost_favi');
                $productsData[$key]['EAN_code'] = $product['sku'];

                //extra attributes
                $extraAttributes = $this->getAttributesMap();
                if (!empty($extraAttributes)) {
                    foreach ($extraAttributes as $code => $extraAttribute) {
                        if ($code == 'is_in_stock') {
                            $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productObject);
                            $productsData[$key][$extraAttribute] = $stock->getIsInStock();
                        } else {
                            $productsData[$key][$extraAttribute] = $productObject->getAttributeText($code) ? : $productObject->getData($code);
                        }

                    }
                }

                // replace attributes
                if ($replaceFlag) {
                    $attributesToReplace = $this->getReplaceAttributesMap();
                    if (!empty($attributesToReplace)) {
                        foreach ($attributesToReplace as $replace => $replaceWith) {
                            if (in_array($replaceWith, ['qty', 'is_in_stock'])) {
                               continue;
                            }
                            $val = $productObject->getAttributeText($replaceWith) ? : $productObject->getData($replaceWith);
                            if (isset($val) && $val != '') {
                                $productsData[$key][$replace] = $val;
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
//            $product['Identifier'],
//            $product['Name'],
//            $product['Description'],
//            $product['Product_url'],
//            $product['Image_url'],
//            $product['Image_url_2'],
//            $product['Image_url_3'],
//            $product['Price'],
//            $product['Net_price'],
//            $product['Manufacturer'],
//            $product['Category'],
//            $product['Delivery_Time'],
//            $product['Delivery_Cost'],
//            $product['EAN_code'],
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

    /**
     * Return the attributes map
     */
    public function getAttributesMap()
    {
        $conf = Mage::getStoreConfig('blugento_feeds/favi/product_attributes');
        $mapValues = Mage::helper('core/unserializeArray')->unserialize($conf);

        $mapFields  = array();
        foreach ($mapValues as $value) {
            $magentoCode = isset($value['magento_code']) ? $value['magento_code'] : null;
            $feedCode    = (isset($value['feed_code']) && $value['feed_code'] !='') ? $value['feed_code'] : $value['magento_code'];

            if ($magentoCode) {
                $mapFields[$magentoCode]  = $feedCode;
            }
        }

        return $mapFields;
    }

    /**
     * Return the replace attributes map
     */
    public function getReplaceAttributesMap()
    {
        $conf = Mage::getStoreConfig('blugento_feeds/favi/replace_product_attributes');
        $mapValues = Mage::helper('core/unserializeArray')->unserialize($conf);

        $mapFields  = array();
        foreach ($mapValues as $value) {
            $replace = isset($value['replaced_attribute']) ? $value['replaced_attribute'] : null;
            $replaceWith  = (isset($value['replaced_attribute_with']) && $value['replaced_attribute_with'] !='') ? $value['replaced_attribute_with'] : $value['replaced_attribute'];

            if ($replace) {
                $mapFields[$replace] = $replaceWith;
            }
        }

        return $mapFields;
    }
}
