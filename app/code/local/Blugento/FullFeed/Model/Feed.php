<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_FullFeed
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FullFeed_Model_Feed extends Mage_Core_Model_Abstract
{
    const CSV_FILE_TYPE = 'csv';
    const XML_FILE_TYPE = 'xml';

    const FILE_SAVE_PATH = 'fullfeed';

    protected $sourceModelAttributes = array('status', 'blugento_cart_custom', 'visibility', 'tax_class_id',
        'lenses_diopter', 'payment_restriction', 'can_be_ordered_online');

    /**
     * Run the cron to generate the feed
     *
     * @return Blugento_FullFeed_Model_Feed $this
     */
    public function cronGenerateFeed()
    {
        $logEnabled = Mage::getStoreConfig('blugento_fullfeed/general/enable_log');

        if ($logEnabled) {
            Mage::log('Server cron running', null, 'full_feed.log', true);
        }

        /** @var Blugento_FullFeed_Helper_Data $helper */
        $helper = Mage::helper('blugento_fullfeed');

        $types = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);

        $result = 'not run';
        foreach ($types as $type) {
            $feedStore = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/store_id');

            if ($helper->isValidToRun($type, $feedStore)) {
                if ($logEnabled) {
                    Mage::log('Valid to run for STORE ID: ' . $feedStore . ' | TYPE: ' . $type, null, 'full_feed.log', true);
                }
                $result = $this->runFeedGeneration($type, $feedStore);
            }
        }

        if ($logEnabled) {
            Mage::log('Feed result:: ' . $result, null, 'full_feed.log', true);
        }

        return $this;
    }

    /**
     * Run the feed generation
     *
     * @param int $type
     * @return string
     */
    public function runFeedGeneration($type, $storeId=null)
    {
        if ($storeId) {
            Mage::app()->setCurrentStore($storeId);
        }

        /** @var Blugento_FullFeed_Helper_Data $helper */
        $helper = Mage::helper('blugento_fullfeed');

        $type = $type ? $type : 1;

        $storeId = $storeId ? $storeId : Mage::getStoreConfig( 'blugento_fullfeed/feed_' . $type . '/store_id');

        if (!$storeId && $storeId !=0) {
            return $helper->__('Missing store id');
        }

        $fileType = $helper->getFeedFileType($type, $storeId);

        if ($fileType == self::CSV_FILE_TYPE) {
            $result = $this->_generateCsvFeed($type, $storeId);
        } else {
            $result = $this->_generateXmlFeed($type, $storeId);
        }

        $this->_setLastRunTime($type);

        return $result;
    }

    /**
     * Change cache time if is set on 10000 to 1
     * 10000 is used only for testing
     *
     */
    public function cronResetCacheTime()
    {
        Mage::log('start', null, 'fullfeed_cachetime');
        for ($type=1; $type<=7; $type++) {
            if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/cache_time') == 10000) {
                Mage::log('type: ' . $type, null, 'fullfeed_cachetime');
                Mage::getConfig()->saveConfig('blugento_fullfeed/feed_' . $type . '/cache_time', 1)->reinit();
            }
        }
        Mage::log('end', null, 'fullfeed_cachetime');
    }

    /**
     * Generate the CSV feed file
     *
     * @param int $type
     */
    private function _generateCsvFeed($type, $storeId=null)
    {
        $result = 'success';

        $filePathName = $this->_getFeedFilePathAndName($type, '.csv', $storeId);

        try {
            $attributeSort = $this->_getAttributesSort($type, $storeId);

            $mappedValues = $this->_getMappedValues($type, $storeId);

            $products = $this->_getFeedProducts($type, $storeId);

            if(!file_exists($filePathName)){
                $file = fopen($filePathName, 'w') or die("Can't create file");
            } else {
                $file = fopen($filePathName,"w");
            }

            $delimiter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/delimiter', $storeId)
                ? Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/delimiter', $storeId) : '~';
            $enclosure = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/enclosure', $storeId)
                ? Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/enclosure', $storeId) : '"';

            $imageCodes = array('image', 'small_image', 'thumbnail');
            $cleanData = array();
            $countCat = array();
            $categoryDisplayType = $this->_getCategoryDisplayType($type, $storeId);

            foreach ($products as $productData) {

                $displayProduct = $this->_isValidToDisplay($productData, $type, $storeId);
                if (!$displayProduct) {
                    continue;
                }

                $product  = array();
                foreach ($productData as $code=>$value) {

                    //set mapped values
                    if (isset($mappedValues[$code]) && $mappedValues[$code] != '') {
                        $value = $this->_getTransformedMappedValues($code, $value, $mappedValues, $type);
                    }

                    if (in_array($code, $imageCodes)) {
                        if ($value && $value != 'no_selection') {
                            $value = Mage::getBaseUrl('media') . 'catalog/product/' . $value;
                        } else {
                            $value = '';
                        }
                    }

                    if ($code == 'qty' && !isset($attributeSort['qty'])) {
                        continue;
                    }
                    if ($code == 'status' && !isset($attributeSort['status'])) {
                        continue;
                    }
                    if ($code == 'is_in_stock' && !isset($attributeSort['is_in_stock'])) {
                        continue;
                    }

                    if (Mage::getStoreConfig('blugento_fullfeed/options/html_in_feed')) {
                        $product[$code] = $value;
                    } else {
                        $product[$code] = $this->_cleanString($value, self::CSV_FILE_TYPE, $storeId);
                    }

                    if ($code == 'category') {
                        $mainCategorySeparator = $this->_getMainCategorySeparator($type, $storeId);
                        switch ($categoryDisplayType) {
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_ALL:
                                $product[$code] = $this->_cleanString($value, self::CSV_FILE_TYPE, $storeId);
                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_MAIN:
                                $categories = explode($mainCategorySeparator, $value);
                                $product[$code] = isset($categories[0]) ? trim($categories[0]) : '';
                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_FINAL:
                                $categories = explode($mainCategorySeparator, $value);
                                $product[$code] = end($categories) ? $this->_cleanString(end($categories), self::CSV_FILE_TYPE, $storeId) : '';
                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_INDIVIDUAL:
                                $categories = explode($mainCategorySeparator, $value);
                                $i = 1;
                                $countCat[] = count($categories);
                                foreach ($categories as $category) {
                                    $product[$code . '_' . $i] = $this->_cleanString($category, self::CSV_FILE_TYPE, $storeId);
                                    $i++;
                                }
                                unset($product['category']);
                                break;
                        }
                    }

                    if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/prices_combined', $storeId)) {
                        if (isset($attributeSort['special_price']) && isset($product['special_price']) && $product['special_price'] != '') {
                            $product['price'] = $product['special_price'] < $product['price'] ? $product['special_price'] : $product['price'];
                            unset($product['special_price']);
                        }
                        if ($product['special_price'] == '') unset($product['special_price']);
                    }

                    if (!isset($attributeSort['entity_id'])) {
                        if (isset($product['entity_id'])) {
                            unset($product['entity_id']);
                        }
                    }
                }

                $cleanData[] = $product;
            }

            /** add csv header */
            if ($this->_addCsvHeader($type, $storeId)) {
                if (count($countCat)) {
                    $countCat = max($countCat);
                }
                fputcsv($file, $this->_getCsvHeaders($products, $type, $categoryDisplayType, $countCat, $storeId), $delimiter, $enclosure);
            }

            /** add csv rows */
            foreach ($cleanData as $data)
            {
                fputcsv($file, $data, $delimiter, $enclosure);
            }

            fclose($file);

            if (!file_exists($filePathName)) {
                $message = Mage::helper('blugento_fullfeed')->__('Feed file was not created.');
                Mage::throwException($message);
            }

            $this->_saveFilePath($type, '.csv', $storeId);

        } catch (Exception $e) {
            return $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Return the feed file absolute path
     *
     * @param int $type
     * @param string $extension
     * @return string
     */
    private function _getFeedFilePathAndName($type, $extension, $storeId=null)
    {
        $storeId = $storeId ? $storeId : 0;

        $fileName = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/file_name', $storeId) ?
            Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/file_name', $storeId) : 'feed';
        $fileName = str_replace(' ', '_', $fileName) . $extension;

        if ($fileName || $fileName !='') {
            $path = Mage::getBaseDir('media') . DS . self::FILE_SAVE_PATH;
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path . DS . $fileName;
    }

    /**
     * Save the feed file path in sys config
     *
     * @param int $type
     * @param int $storeId
     */
    private function _saveFilePath($type, $extension, $storeId)
    {
        $storeId = $storeId ? $storeId : 0;

        $fileName = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/file_name', $storeId) ?
            Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/file_name', $storeId) : 'feed';
        $fileName = str_replace(' ', '_', $fileName) . $extension;

        if ($fileName || $fileName !='') {
            $url = str_replace('cdn.', '',Mage::getBaseUrl('media') . self::FILE_SAVE_PATH . '/' . $fileName);
        }

        $sql = "SELECT store_id FROM core_store";
        $stores = $this->_getReadConnection()->fetchAll($sql);

        foreach ($stores as $store) {
            Mage::getConfig()->saveConfig("blugento_fullfeed/feed_$type/file_path", $url, 'default', $store['store_id']);
        }
    }

    /**
     * Return the category separator
     *
     * @param int $type
     * @return mixed
     */
    private function _getChildCategorySeparator($type, $storeId=null)
    {
        $separator = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/child_category_separator', $storeId);
        return $separator ? $separator : ' # ';
    }

    /**
     * Return the category separator
     *
     * @param int $type
     * @return mixed
     */
    private function _getMainCategorySeparator($type, $storeId=null)
    {
        $separator = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/main_category_separator', $storeId);
        return $separator ? $separator : '/';
    }

    /**
     * Return the category display type
     *
     * @param int $type
     * @return mixed
     */
    private function _getCategoryDisplayType($type, $storeId=null)
    {
        return Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/category_display', $storeId);
    }

    /**
     * Check if configurable product is salable
     *
     * @param $productId
     * @return bool
     */
    private function isSalable($productId)
    {
        $flag = false;
        $simpleIds = $this->getSimpleIds($productId);
        foreach ($simpleIds as $simpleId) {
            $stockData = $this->getProductStockData((int)$simpleId['child_id']);
            if ($stockData['qty'] > 0 && $stockData['is_in_stock'] == 1) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }

    /**
     * Get simple ids of configurable
     *
     * @param $productId
     * @return mixed
     */
    private function getSimpleIds($productId)
    {
        try {
            $query = "SELECT child_id FROM catalog_product_relation WHERE parent_id = " . $productId;
            $result = $this->_getReadConnection()->query($query);
            $rows = $result->fetchAll();
            return $rows;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Get stock data of a product
     *
     * @param $productId
     * @return mixed
     */
    private function getProductStockData($productId)
    {
        try {
            $query = "SELECT qty, is_in_stock FROM cataloginventory_stock_item WHERE product_id = " . $productId;
            $result = $this->_getReadConnection()->query($query);
            $row = $result->fetch();
            return $row;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Check if product is valid to be add on feed
     *
     * @param array $product
     * @param int $type
     * @return bool
     */
    private function _isValidToDisplay($product, $type, $storeId=null)
    {
        $onlyVisible = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/only_visible', $storeId);
        $showOutOfStock = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_outofstock', $storeId);

        if (!$onlyVisible) {
            return true;
        }

        // check if product (configurable) is salable
        $salable = true;
        $_resource = Mage::getSingleton('catalog/product')->getResource();
        if (isset($product['entity_id'])) {
            $productId = $product['entity_id'];
            $productType = $_resource->getAttributeRawValue($productId, 'type_id', Mage::app()->getStore());
            if ($productType == 'configurable') {
                if (!$this->isSalable($productId)) {
                    $salable = false;
                }
            }
        } else {
            if (isset($product['sku'])) {
                $productId = Mage::getModel('catalog/product')->getIdBySku($product['sku']);
                $productType = $_resource->getAttributeRawValue($productId, 'type_id', Mage::app()->getStore());
                if ($productType == 'configurable') {
                    if (!$this->isSalable($productId)) {
                        $salable = false;
                    }
                }
            }
        }

        $status    = isset($product['status']) ? $product['status'] : 2;
        $qty       = isset($product['qty']) ? $product['qty'] : null;
        $isInStock = isset($product['is_in_stock']) && $product['is_in_stock'] != '' ? $product['is_in_stock'] : 0;

        if ($showOutOfStock) {
            $isInStock = 1;
        }

//        if (($status && $status == 2) || ($qty && ($qty <=0 || $isInStock == 0))) {
        if (($status && $status == 2) || $isInStock == 0 || !$salable) {
            return false;
        }

        return true;
    }

    /**
     * Check if CSV file header need to be created
     *
     * @param int $type
     * @return bool
     */
    private function _addCsvHeader($type, $storeId=null)
    {
        return Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/add_csv_header', $storeId);
    }

    /**
     * Return indexes of products array as headers for CSV file
     *
     * @param array $products
     * @return array
     */
    protected function _getCsvHeaders($products, $type, $categoryDisplayType, $countCat, $storeId=null)
    {
        $product = current($products);
        $attributes = array_keys($product);

        $attributeMap = $this->_getAttributesMap($type, $storeId);

        $csvHeaders = array();
        foreach ($attributes as $attribute) {
            if (isset($attributeMap[$attribute])) {
                if ($attribute == 'category' && $categoryDisplayType == Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_INDIVIDUAL) {
                    $i = 1;
                    while ($i <= $countCat) {
                        $csvHeaders[] = $attributeMap[$attribute] . '_' . $i;
                        $i++;
                    }
                } elseif ($attribute == 'special_price' && Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/prices_combined', $storeId)) {
                    continue;
                } else {
                    $csvHeaders[] = $attributeMap[$attribute];
                }
            }
        }

        return $csvHeaders;
    }

    /**
     * Generate the XML feed file
     *
     * @param int $type
     */
    private function _generateXmlFeed($type, $storeId=null)
    {
        $result = 'success';

        $attributes = $this-> _getAttributesMap($type, $storeId);

        $filePathName = $this->_getFeedFilePathAndName($type, '.xml', $storeId);

        try {
            if(file_exists($filePathName)) {
                unlink($filePathName);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        try {
            $imageCodes = array('image', 'small_image', 'thumbnail');

            $products = $this->_getFeedProducts($type, $storeId);

            $attributeSort = $this->_getAttributesSort($type, $storeId);

            $mappedValues = $this->_getMappedValues($type, $storeId);

            foreach ($products as $productData) {

                $displayProduct = $this->_isValidToDisplay($productData, $type, $storeId);
                if (!$displayProduct) {
                    continue;
                }

                $product = array();

                foreach ($productData as $code=>$value) {

                    //set mapped values
                    if (isset($mappedValues[$code]) && $mappedValues[$code] != '') {
                        $value = $this->_getTransformedMappedValues($code, $value, $mappedValues, $type);
                    }

                    if (in_array($code, $imageCodes)) {
                        if ($value && $value != 'no_selection') {
                            $value = Mage::getBaseUrl('media') . 'catalog/product/' . $value;
                        } else {
                            $value = '';
                        }
                    }

                    if ($code == 'qty' && !isset($attributeSort['qty'])) {
                        continue;
                    }
                    if ($code == 'status' && !isset($attributeSort['status'])) {
                        continue;
                    }
                    if ($code == 'is_in_stock' && !isset($attributeSort['is_in_stock'])) {
                        continue;
                    }

                    $product[$code] = $value;
                }

                if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/prices_combined', $storeId)) {
                    if (isset($attributeSort['special_price']) && isset($product['special_price']) && $product['special_price'] != '') {
                        $product['price'] = $product['special_price'] < $product['price'] ? $product['special_price'] : $product['price'];
                        unset($product['special_price']);
                    }
                    if ($product['special_price'] == '') unset($product['special_price']);
                }

                if (!isset($attributeSort['entity_id'])) {
                    if (isset($product['entity_id'])) {
                        unset($product['entity_id']);
                    }
                }

                $cleanData[] = $product;
            }

            $xml = new DOMDocument('1.0', 'UTF-8');
            $xml->preserveWhiteSpace = FALSE;

            $productsXml = $xml->createElement("products");

            if (!count($cleanData)) {
                $xml->appendChild($productsXml);
            }

            foreach ($cleanData as $productData) {

                $product = $xml->createElement("product");
                $productsXml->appendChild($product);

                foreach ($productData as $code=>$value) {
                    if ($code == 'category') {
                        $mainCategorySeparator = $this->_getMainCategorySeparator($type, $storeId);
                        $categoryDisplayType = $this->_getCategoryDisplayType($type, $storeId);
                        switch ($categoryDisplayType) {
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_ALL:
                                $value = $this->_cleanString($value, self::XML_FILE_TYPE, $storeId);

                                $attribute = $xml->createElement($attributes[$code]);
                                $attribute->nodeValue = $value;
                                $product->appendChild($attribute);

                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_MAIN:
                                $categories = explode($mainCategorySeparator, $value);
                                $value = isset($categories[0]) ? trim($categories[0]) : '';
                                $value = $this->_cleanString($value, self::XML_FILE_TYPE, $storeId);

                                $attribute = $xml->createElement($attributes[$code]);
                                $attribute->nodeValue = $value;
                                $product->appendChild($attribute);
                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_FINAL:
                                $categories = explode($mainCategorySeparator, $value);
                                $value = end($categories) ? $this->_cleanString(end($categories), self::XML_FILE_TYPE, $storeId) : '';

                                $attribute = $xml->createElement($attributes[$code]);
                                $attribute->nodeValue = $value;
                                $product->appendChild($attribute);
                                break;
                            case Blugento_FullFeed_Helper_Data::CATEGORY_TYPE_INDIVIDUAL:
                                $categories = explode($mainCategorySeparator, $value);
                                $i = 1;
                                foreach ($categories as $category) {
                                    $value = $this->_cleanString($category, self::XML_FILE_TYPE, $storeId);

                                    $attribute = $xml->createElement($attributes[$code] . '_' . $i);
                                    $attribute->nodeValue = $value;
                                    $product->appendChild($attribute);
                                    $i++;
                                }
                                break;
                        }
                    } else if ($code == 'media_gallery') {
                        $mediaImages = array();
                        $gallerySeparator = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/gallery_separator', $storeId);
                        if (strpos($value, $gallerySeparator)) {
                            $mediaImages = explode($gallerySeparator, $value);
                        } else {
                            if ($value && $value!='') {
                                $mediaImages = array($value);
                            }
                        }

                        if (count($mediaImages)) {
                            $gallery = $xml->createElement('Pictures');
                            $product->appendChild($gallery);

                            foreach ($mediaImages as $image) {
                            	if (isset($productData['image']) && $productData['image'] != '') {
		                            if (trim($image) != trim($productData['image'])) {
			                            $picture = $xml->createElement('picture');
			                            $picture->nodeValue = $this->_cleanString($image, self::XML_FILE_TYPE, $storeId);
			                            $gallery->appendChild($picture);
		                            }
	                            }
                            }
                        }

                    } else {
                        $attribute = $xml->createElement($attributes[$code]);
                        $value = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $value);
                        if (Mage::getStoreConfig('blugento_fullfeed/options/html_in_feed')) {
                            $attribute->appendChild($xml->createCDATASection($value));
                        } else {
                            $attribute->nodeValue = $this->_cleanString($value, self::XML_FILE_TYPE, $storeId);
                        }
                        $product->appendChild($attribute);
                    }
                }

                $xml->appendChild($productsXml);
            }

            $xml->save($filePathName);

            $this->_saveFilePath($type, '.xml', $storeId);

        } catch (Exception $e) {
            Mage::logException($e);
            return $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Clean the string
     *
     * @param string $string
     * @param int $type
     * @return mixed|string
     */
    private function _cleanString($string, $type, $storeId=null)
    {
        $string = strip_tags($string);
        $string = trim($string);

        //replace special characters
        $specialCharsHtml = ['&Aacute;', '&Eacute;', '&Euml;', '&Iacute;', '&Oacute;', '&Ouml;', '&Uacute;', '&Uuml;', '&aacute;', '&eacute;', '&euml;', '&iacute;', '&oacute;', '&ouml;', '&uacute;', '&uuml;', '&Acirc;', '&acirc;', '&Icirc;', '&icirc;', '&nbsp;', '&amp;'];
        $specialChars = ['Á', 'É', 'Ë', 'Í', 'Ó', 'Ö', 'Ú', 'Ü', 'á', 'é', 'ë', 'í', 'ó', 'ö', 'ú', 'ü', 'Â', 'â', 'Î', 'î', ' ', '&'];
        $string = str_replace($specialCharsHtml, $specialChars, $string);

        $result = $string;
        if ($type == self::CSV_FILE_TYPE) {
            $delimiter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/delimiter', $storeId);
            $enclosure = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/enclosure', $storeId);

            $result = str_replace($delimiter, ' ', $result);
            $result = str_replace($enclosure, ' ', $result);
        } else {
            $result = str_replace(array('&','>','<','"'), array('&amp;','&gt;','&lt;','&quot;'), $result);
        }

        return $result;
    }

    /**
     * Return the products that will be saved in feed
     *
     * @param int $type
     * @return array
     */
    private function _getFeedProducts($type, $storeId=null)
    {
        try {
            $defaultStoreProducts = $this->_getDefaultStoreProducts($type, $storeId);

            if (!$storeId) {
                return $defaultStoreProducts;
            }

            $customStoreProducts = $this->_getCustomStoreProducts($type, $storeId);

            foreach ($customStoreProducts as $key => $productData) {
                //unset special price from default store if no special price on custom store
                if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/no_special_from_default', $storeId)) {
                    if (!$customStoreProducts[$key]['special_price'] || $customStoreProducts[$key]['special_price'] == '') {
                        unset($defaultStoreProducts[$key]['special_price']);
                    }
                }
                foreach ($productData as $code => $data) {
                    if (!$data || $data == '') {
                        $customStoreProducts[$key][$code] = isset($defaultStoreProducts[$key][$code]) ? $defaultStoreProducts[$key][$code] : null;
                    }
                }
            }

            return $customStoreProducts;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return array();
    }

    /**
     * Return the default|admin (id=0) store products
     *
     * @param int $type
     * @param int $storeId
     * @return mixed
     */
    private function _getDefaultStoreProducts($type, $storeId=null)
    {
        $addStoreIdToFeed = false;
        $addWebsitesToFeed = false;

        $attributes = $this->_getAttributesMap($type, $storeId);

        $attributeSort = $this->_getAttributesSort($type, $storeId);
        $backendTypes  = $this->_getAttributesType($type);

        $mediaGallery  = $this->_getMediaGallery();
//        $parentIds     = $this->_getParentIds();

        $websiteId = $storeId && $storeId != 0 ? Mage::getModel('core/store')->load($storeId)->getWebsiteId() : NULL;

        $select = array('e.entity_id as entity_id');

        $sql = '';
        $i = 1;

        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/only_visible', $storeId)) {
            if (!isset($attributeSort['qty'])) {
                $attributeSort['qty'] = 99;
            }
            if (!isset($attributeSort['status'])) {
                $attributeSort['status'] = 99;
            }
            if (!isset($attributeSort['is_in_stock'])) {
                $attributeSort['is_in_stock'] = 99;
            }
        }
        $checkSpecial = false;
        if (isset($attributeSort['special_price']) && !isset($attributeSort['special_from_date']) && !isset($attributeSort['special_to_date'])) {
            $attributeSort['special_from_date'] = 99;
            $attributeSort['special_to_date'] = 99;
            $checkSpecial = true;
        }

        $multiselectAttr = array();
        foreach ($attributeSort as $code=>$value) {
            $addStoreIdToFeed = $code != 'store_id' ? $addStoreIdToFeed : true;
            $addWebsitesToFeed = $code != 'websites' ? $addWebsitesToFeed : true;

            $magentoAttributeCode = $code;
            $frontendInput = $this->_getAttributeInput($code);
            $backendType          = isset($backendTypes[$magentoAttributeCode]) ? $backendTypes[$magentoAttributeCode]: '';

            $attributeTableMap = array (
                'varchar'  => 'catalog_product_entity_varchar',
                'decimal'  => 'catalog_product_entity_decimal',
                'qty'      => 'cataloginventory_stock_item',
                'int'      => 'catalog_product_entity_int',
                'text'     => 'catalog_product_entity_text',
                'datetime' => 'catalog_product_entity_datetime',
                'static'   => 'catalog_product_entity'
            );

            $columnsTableMap = array (
                'sku'           => 'sku',
                'qty'           => 'qty',
                'backorders'    => 'backorders',
                'manage_stock'  => 'manage_stock',
                'attribute_set' => 'attribute_set_name'
            );
            $tableName = isset($attributeTableMap[$backendType]) ? $attributeTableMap[$backendType] : null;
            $colName   = isset($columnsTableMap[$code]) ? $columnsTableMap[$code] : 'value';

            $attributeCodes = $this->_getProductsAttributeCode($attributeSort);
            $attrId = $attributeCodes[$code];

            if(!$tableName) {
                if ($code == 'category') {
                    $sql .= " ";
                    $select[] = 'e.entity_type_id AS category';
                }
                if ($code == 'is_in_stock') {
                    $sql .= " ";
                    $select[] = 'e.has_options AS is_in_stock';
                }
                if ($code == 'qty') {
                    $sql .= " ";
                    $select[] = 'e.required_options AS qty';
                }
                if ($code == 'type_id') {
                    $sql .= " ";
                    $select[] = 'e.type_id AS type_id';
                }
//                if ($code == 'parent_id') {
//                    $sql .= " ";
//                    $select[] = 'e.attribute_set_id AS parent_id';
//                }
                if ($code != 'backorders' && $code != 'manage_stock' && $code != 'attribute_set') {
                    continue;
                }
            }

            $select[] = $code . '.' . $colName . ' AS `' . $code . '`';

            $aliasTem = $code . '_' .$i;

            if ($backendType == 'int') {
                if (in_array($code, $this->sourceModelAttributes) || $frontendInput == 'text') {
                    $sql .= " LEFT JOIN 
                    $tableName `$code` ON e.entity_id = $code.entity_id
                        AND $code.attribute_id = $attrId
                        AND $code.store_id = 0";
                } else {
                    $sql .= " LEFT JOIN 
                    $tableName `$aliasTem` ON e.entity_id = $aliasTem.entity_id
                        AND $aliasTem.attribute_id = $attrId";
                    $sql .= " LEFT JOIN 
                    eav_attribute_option_value `$code` ON $aliasTem.value = $code.option_id 
                    AND $code.store_id = 0";

                    $i++;
                }
            } else {
                if ($code == 'sku') {
                    $sql .= " LEFT JOIN 
                        $tableName `$code` ON e.entity_id = $code.entity_id
                ";
                } elseif ($code == 'backorders' || $code == 'manage_stock') {
                    $sql .= " LEFT JOIN 
                        cataloginventory_stock_item `$code` ON e.entity_id = $code.product_id
                ";
                } elseif ($code == 'attribute_set') {
                    $sql .= " LEFT JOIN eav_attribute_set `$code` 
                        ON e.attribute_set_id = $code.attribute_set_id
                ";
                } else {
                    $sql .= " LEFT JOIN 
                        $tableName `$code` ON e.entity_id = $code.entity_id
                        AND $code.attribute_id = $attrId
                        AND $code.store_id = 0
                ";
                }
            }

            if ($backendType == 'text') {
                $sqlX = "
                    SELECT  o.option_id, ov.value
                    FROM eav_attribute e
                    LEFT JOIN eav_attribute_option o ON o.ATTRIBUTE_ID = e.ATTRIBUTE_ID
                    LEFT JOIN eav_attribute_option_value ov ON ov.OPTION_ID = o.OPTION_ID
                    WHERE e.ATTRIBUTE_CODE = '$code'
                    AND ov.STORE_ID = 0
                ";
                $attrDetails = $this->_getReadConnection()->fetchAll($sqlX);
                if (count($attrDetails)) {
                    $ss = array();
                    foreach ($attrDetails as $attrDetail) {
                        $optionId = $attrDetail['option_id'];
                        $value = $attrDetail['value'];
                        $ss[$optionId] = $value;
                    }
                    $multiselectAttr[$code] = $ss;
                }
            }
        }

        $sqlProducts = 'SELECT DISTINCT ' . implode(', ', $select);
        $sqlProducts .= ' FROM catalog_product_entity e';

        if ($websiteId) {
            $sqlProducts .=' INNER JOIN catalog_product_website w ON e.entity_id = w.product_id AND website_id = ' . $websiteId;
        }

        //filter products by visibility
        $visibilityFilter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_visibility', $storeId);
        if ($visibilityFilter != '1,2,3,4') {
            $visibilityAttribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'visibility');
            $visibilityAttributeId = $visibilityAttribute->getId();
            $sqlProducts .=' INNER JOIN catalog_product_entity_int vis ON e.entity_id = vis.entity_id AND vis.attribute_id = ' . $visibilityAttributeId . ' AND vis.value IN (' . $visibilityFilter . ')';
        }

        //filter by category
        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_category', $storeId)) {
            $filterType = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/category_type', $storeId);
            $categoriesFilteredBy = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/categories', $storeId);
            if ($filterType && $categoriesFilteredBy) {
                if ($filterType == 'include') {
                    $sqlProducts .=' INNER JOIN catalog_category_product cats ON cats.product_id = e.entity_id AND cats.category_id in (' . $categoriesFilteredBy . ')';
                } else {
                    $sqlProducts .=' INNER JOIN catalog_category_product cats ON cats.product_id = e.entity_id AND cats.category_id not in (' . $categoriesFilteredBy . ')';
                }
            }
        }

        //filter products by manufacturer
        $manufacturerFilter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_manufacturer', $storeId);
        $manufacturerValue = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/manufacturer', $storeId);
        if ($manufacturerFilter) {
            if (isset($manufacturerValue) && is_numeric($manufacturerValue)) {
                $manufacturerAttribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
                $manufacturerAttributeId = $manufacturerAttribute->getId();
                $sqlProducts .= ' INNER JOIN catalog_product_entity_int mnf ON e.entity_id = mnf.entity_id AND mnf.attribute_id = ' . $manufacturerAttributeId . ' AND mnf.value = ' . $manufacturerValue;
            }
        }

        $sqlProducts .= $sql;

        //exclude products with prices from promo rules
        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/exclude_catalogrule_products', $storeId)) {
                $sqlProducts .=' WHERE e.entity_id NOT IN (SELECT product_id FROM catalogrule_product)';
        }

        //exclude specific SKUs
        $skusToBeExcluded = $this->getExcludedSkuList($type, $storeId);
        if (!empty($skusToBeExcluded)) {
            if (strpos($sqlProducts, 'WHERE') !== false) {
                $sqlProducts .=' AND e.sku NOT IN ("'. implode('","', $skusToBeExcluded) .'")';
            } else {
                $sqlProducts .=' WHERE e.sku NOT IN ("'. implode('","', $skusToBeExcluded) .'")';
            }
        }

        //Split in subqueries of 1000 rows if too many
        $rowsCounted = $this->countRows();
        if (isset($rowsCounted) && $rowsCounted > 1000) {
            $batches = ceil($rowsCounted / 1000);
            $sqlProducts .= ' LIMIT 1000';
            $baseSqlProducts = $sqlProducts;
            $firstBatchProducts = $this->_getReadConnection()->fetchAll($sqlProducts);
            $products = $firstBatchProducts;
            sleep(2);
            for ($i = 1; $i < $batches; $i++) {
                $offset = ' OFFSET ' . $i * 1000;
                $sqlProducts = $baseSqlProducts . $offset;
                $batchProducts = $this->_getReadConnection()->fetchAll($sqlProducts);
                $products = array_merge($products, $batchProducts);
                sleep(2);
            }
        } else {
            $products = $this->_getReadConnection()->fetchAll($sqlProducts);
        }

        if (isset($attributeSort['configurable_sku'])) {
            $products = $this->_getParentSku($products);
        }

        if (isset($attributeSort['url_path']) && Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/full_url', $storeId)) {
            $store = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/store_id', $storeId);
            $products = $this->_generateProductFullUrl($products, $store);
        }

        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/simple_price', $storeId) == 2) {
            $products = $this->_calculateAssociatedProductsPricing($products, $attributeSort);
        }

        $stockData = array();
        if (isset($attributes['qty']) || isset($attributes['is_in_stock']) || Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/only_visible', $storeId)) {
            $stockData  = $this->_getStockdata();
        }

//        $addEntityId = array_key_exists('entity_id', $attributeSort);

        if (true || isset($attributes['qty']) || isset($attributes['is_in_stock']) || isset($attributes['category'])
            || isset($attributes['entity_id'])) {
            foreach ($products as $key=>$product) { // TODO:: refine this
                if ($checkSpecial) {
                    if (($product['special_from_date'] != null && $product['special_from_date'] > now())
                        || ($product['special_to_date'] != null && $product['special_to_date'] < now())) {
                        $products[$key]['special_price'] = '';
                    }
                    unset($products[$key]['special_from_date']);
                    unset($products[$key]['special_to_date']);
                }

                // get final price  in special_price if catalog rules applied
                if (isset($products[$key]['special_price'])) {
                    $finalPrice = $this->_getFinalPrice($product, $websiteId);

                    if ($finalPrice && $finalPrice != '') {
                        $products[$key]['special_price'] = $finalPrice;
                    }
                }

                if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/remove_decimals', $storeId)) {
                    if (isset($product['price'])) {
                        $products[$key]['price'] = intval($product['price']);
                    }
                    if (isset($product['special_price'])) {
                        $products[$key]['special_price'] = intval($product['special_price']);
                    }
                    if (isset($product['msrp'])) {
                        $products[$key]['msrp'] = intval($product['msrp']);
                    }
                }

                if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_currency', $storeId)) {
                    $currencyCode = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
                    if (isset($products[$key]['price'])) {
                        $products[$key]['price'] = $products[$key]['price'] . ' ' . $currencyCode;
                    }
                    if (isset($products[$key]['special_price']) && $products[$key]['special_price'] != '') {
                        $products[$key]['special_price'] = $products[$key]['special_price'] . ' ' . $currencyCode;
                    }
                    if (isset($products[$key]['msrp']) && $products[$key]['msrp'] != '') {
                        $products[$key]['msrp'] = $products[$key]['msrp'] . ' ' . $currencyCode;
                    }
                    if (isset($products[$key]['group_price']) && $products[$key]['group_price'] != '') {
                        $products[$key]['group_price'] = $products[$key]['group_price'] . ' ' . $currencyCode;
                    }
                    if (isset($products[$key]['tier_price']) && $products[$key]['tier_price'] != '') {
                        $products[$key]['tier_price'] = $products[$key]['tier_price'] . ' ' . $currencyCode;
                    }
                }
                foreach ($product as $code=>$val) {
                    if (isset($multiselectAttr[$code])) {
                        $val = explode(',', $val);
                        $opti = array();
                        foreach ($val as $v) {
                            if (isset($multiselectAttr[$code][$v])) {
                                $opti[] = $multiselectAttr[$code][$v];
                            }
                        }
                        $products[$key][$code] = implode('#', $opti);
                    }
                }

                $entityId  = isset($product['entity_id']) ? $product['entity_id'] : null;
                if ($entityId) {
                    if (isset($attributes['category'])) {
                        $categories = $this->_getAllCategories($type, $storeId, $entityId);
                        if (isset($categories[$entityId])) {
                            $products[$key]['category'] = $categories[$entityId];
                        }
                    }
                    if (count($stockData) && isset($stockData[$entityId]['qty'])) {
                        $products[$key]['qty'] = $stockData[$entityId]['qty'];
                    }
                    if (count($stockData) && isset($stockData[$entityId]['in_stock'])) {
                        $products[$key]['is_in_stock'] = $stockData[$entityId]['in_stock'];
                    }

//                    if (isset($attributes['parent_id'])) {
//                        $products[$key]['parent_id'] = $parentIds[$entityId];
//                    }
//                    if (!$addEntityId && isset($products[$key]['entity_id'])) {

                }
                if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/only_visible', $storeId)) {

                    $inStock  = isset($stockData[$entityId]['in_stock']) ? $stockData[$entityId]['in_stock'] : null;
                    $status   = isset($product['status']) ? $product['status'] : '';

                    if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_outofstock', $storeId)){
                        $inStock = 1;
                    }

                    if (!$inStock || $status != 1) {
                        unset($products[$key]);
                    }
                }

                $prodGallery = array();
                if (isset($attributes['media_gallery'])) {
                    $prodGallery = isset($mediaGallery[$entityId]) ? $mediaGallery[$entityId] : array();

                    if (count($prodGallery)) {
                        $productImage = isset($product['image']) ? $product['image'] : '';

                        foreach ($prodGallery as $keyG=>$galleryImage) {
                            if (isset($galleryImage[$keyG]) && $galleryImage[$keyG] == $productImage) {
                                unset($prodGallery[$keyG]);
                            }
                        }
                    }
                }

                if (isset($attributes['media_gallery']) && count($prodGallery)) {
                    $images = array();
                    foreach ($prodGallery as $image) {
                        $imagePath = isset($image[0]) ? $image[0] : null;
                        if ($imagePath && $imagePath != 'no_selection') {
                            $images[] = Mage::getBaseUrl('media') . 'catalog/product/' . $imagePath;
                        }
                    }

                    if (count($images)) {
                        $products[$key]['media_gallery'] = implode(Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/gallery_separator', $storeId), $images);
                    }
                }

                // format wysiwyg urls in description
                if (isset($products[$key]['description'])) {
                    $products[$key]['description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($products[$key]['description']);
                }

                // format wysiwyg urls in short description
                if (isset($products[$key]['short_description'])) {
                    $products[$key]['short_description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($products[$key]['short_description']);
                }

                if (!isset($attributeSort['entity_id'])) {
                    if (isset($products[$key]['entity_id'])) {
                        unset($products[$key]['entity_id']);
                    }
                }

                // add store id and website
                if ($addStoreIdToFeed) {
                    $products[$key]['store_id'] = $storeId;
                }

                if ($addWebsitesToFeed) {
                    $products[$key]['websites'] = $this->getWebsite($storeId);
                }
            }
        }

        return $products;
    }

    /**
     * Return the specific store products
     *
     * @param int $type
     * @param int $storeId
     * @return mixed
     */
    private function _getCustomStoreProducts($type, $storeId=null)
    {
        $addStoreIdToFeed = false;
        $addWebsitesToFeed = false;

        $attributes = $this->_getAttributesMap($type, $storeId);

        $attributeSort = $this->_getAttributesSort($type, $storeId);
        $backendTypes  = $this->_getAttributesType($type);

        $storeId = $storeId ? $storeId : 0;
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();


        $select = array('e.entity_id as entity_id');

        $sql = '';
        $i = 1;

        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/only_visible', $storeId)) {
            if (!isset($attributeSort['qty'])) {
                $attributeSort['qty'] = 99;
            }
            if (!isset($attributeSort['status'])) {
                $attributeSort['status'] = 99;
            }
            if (!isset($attributeSort['is_in_stock'])) {
                $attributeSort['is_in_stock'] = 99;
            }
        }

        $checkSpecial = false;
        if (isset($attributeSort['special_price']) && !isset($attributeSort['special_from_date']) && !isset($attributeSort['special_to_date'])) {
            $attributeSort['special_from_date'] = 99;
            $attributeSort['special_to_date'] = 99;
            $checkSpecial = true;
        }

        foreach ($attributeSort as $code=>$value) {
            $addStoreIdToFeed = $code != 'store_id' ? $addStoreIdToFeed : true;
            $addWebsitesToFeed = $code != 'websites' ? $addWebsitesToFeed : true;

            $magentoAttributeCode = $code;
            $frontendInput = $this->_getAttributeInput($code);
            $backendType          = isset($backendTypes[$magentoAttributeCode]) ? $backendTypes[$magentoAttributeCode] : '';

            $attributeTableMap = array (
                'varchar'  => 'catalog_product_entity_varchar',
                'decimal'  => 'catalog_product_entity_decimal',
                'qty'      => 'cataloginventory_stock_item',
                'int'      => 'catalog_product_entity_int',
                'text'     => 'catalog_product_entity_text',
                'datetime' => 'catalog_product_entity_datetime',
                'static'   => 'catalog_product_entity'
            );

            $columnsTableMap = array (
                'sku'           => 'sku',
                'qty'           => 'qty',
                'backorders'    => 'backorders',
                'manage_stock'  => 'manage_stock',
                'attribute_set' => 'attribute_set_name'
            );

            $tableName = isset($attributeTableMap[$backendType]) ? $attributeTableMap[$backendType] : null;
            $colName   = isset($columnsTableMap[$code]) ? $columnsTableMap[$code] : 'value';

            $attributeCodes = $this->_getProductsAttributeCode($attributeSort);

            $attrId = $attributeCodes[$code];

            if(!$tableName) {
                if ($code == 'category') {
                    $sql .= " ";
                    $select[] = 'e.entity_type_id AS category';
                }
                if ($code == 'is_in_stock') {
                    $sql .= " ";
                    $select[] = 'e.has_options AS is_in_stock';
                }
                if ($code == 'qty') {
                    $sql .= " ";
                    $select[] = 'e.required_options AS qty';
                }
//                if ($code == 'parent_id') {
//                    $sql .= " ";
//                    $select[] = 'e.attribute_set_id AS parent_id';
//                }
                if ($code != 'backorders' && $code != 'manage_stock' && $code != 'attribute_set') {
                    continue;
                }
            }

            $select[] = $code . '.' . $colName . ' AS ' . $code;

            $aliasTem = $code . '_' .$i;

            if ($backendType == 'int') {
                if (in_array($code, $this->sourceModelAttributes) || $frontendInput == 'text') {
                    $sql .= " LEFT JOIN 
                    $tableName $code ON e.entity_id = $code.entity_id
                        AND $code.attribute_id = $attrId
                        AND $code.store_id = $storeId";
                } else {
                    $sql .= " LEFT JOIN 
                    $tableName $aliasTem ON e.entity_id = $aliasTem.entity_id
                        AND $aliasTem.attribute_id = $attrId";
                    $sql .= " LEFT JOIN 
                    eav_attribute_option_value $code ON $aliasTem.value = $code.option_id 
                    AND $code.store_id = $storeId";

                    $i++;
                }
            } else {
                if ($code == 'sku') {
                    $sql .= " LEFT JOIN 
                        $tableName $code ON e.entity_id = $code.entity_id
                ";
                } elseif ($code == 'attribute_set') {
                    $sql .= " LEFT JOIN eav_attribute_set `$code` 
                        ON e.attribute_set_id = $code.attribute_set_id
                ";
                } else {
                    $sql .= " LEFT JOIN 
                        $tableName $code ON e.entity_id = $code.entity_id
                        AND $code.attribute_id = $attrId
                        AND $code.store_id = $storeId
                ";
                }
            }
            // TODO:: for multistore feed use also: AND $code.store_id = $storeCodeId
        }

        $sqlProducts = 'SELECT DISTINCT ' . implode(', ', $select);
        $sqlProducts .= ' FROM catalog_product_entity e';
        if ($websiteId) {
            $sqlProducts .=' INNER JOIN catalog_product_website w ON e.entity_id = w.product_id AND website_id = ' . $websiteId;
        }

        //filter products by visibility
        $visibilityFilter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_visibility', $storeId);
        if ($visibilityFilter != '1,2,3,4') {
            $visibilityAttribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'visibility');
            $visibilityAttributeId = $visibilityAttribute->getId();
            $sqlProducts .=' INNER JOIN catalog_product_entity_int vis ON e.entity_id = vis.entity_id AND vis.attribute_id = ' . $visibilityAttributeId . ' AND vis.value IN (' . $visibilityFilter . ')';
        }

        //filter by category
        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_category', $storeId)) {
            $filterType = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/category_type', $storeId);
            $categoriesFilteredBy = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/categories', $storeId);
            if ($filterType && $categoriesFilteredBy) {
                if ($filterType == 'include') {
                    $sqlProducts .=' INNER JOIN catalog_category_product cats ON cats.product_id = e.entity_id AND cats.category_id in (' . $categoriesFilteredBy . ')';
                } else {
                    $sqlProducts .=' INNER JOIN catalog_category_product cats ON cats.product_id = e.entity_id AND cats.category_id not in (' . $categoriesFilteredBy . ')';
                }
            }
        }

        //filter products by manufacturer
        $manufacturerFilter = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/filter_by_manufacturer', $storeId);
        $manufacturerValue = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/manufacturer', $storeId);
        if ($manufacturerFilter) {
            if (isset($manufacturerValue) && is_numeric($manufacturerValue)) {
                $manufacturerAttribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
                $manufacturerAttributeId = $manufacturerAttribute->getId();
                $sqlProducts .= ' INNER JOIN catalog_product_entity_int mnf ON e.entity_id = mnf.entity_id AND mnf.attribute_id = ' . $manufacturerAttributeId . ' AND mnf.value = ' . $manufacturerValue;
            }
        }

        $sqlProducts .= $sql;

        //exclude products with prices from promo rules
        if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/exclude_catalogrule_products', $storeId)) {
            $sqlProducts .=' WHERE e.entity_id NOT IN (SELECT product_id FROM catalogrule_product)';
        }

        //exclude specific SKUs
        $skusToBeExcluded = $this->getExcludedSkuList($type, $storeId);
        if (!empty($skusToBeExcluded)) {
            if (strpos($sqlProducts, 'WHERE') !== false) {
                $sqlProducts .=' AND e.sku NOT IN ("'. implode('","', $skusToBeExcluded) .'")';
            } else {
                $sqlProducts .=' WHERE e.sku NOT IN ("'. implode('","', $skusToBeExcluded) .'")';
            }
        }

        //Split in subqueries of 1000 rows if too many
        $rowsCounted = $this->countRows();
        if (isset($rowsCounted) && $rowsCounted > 1000) {
            $batches = ceil($rowsCounted / 1000);
            $sqlProducts .= ' LIMIT 1000';
            $baseSqlProducts = $sqlProducts;
            $firstBatchProducts = $this->_getReadConnection()->fetchAll($sqlProducts);
            $products = $firstBatchProducts;
            sleep(2);
            for ($i = 1; $i < $batches; $i++) {
                $offset = ' OFFSET ' . $i * 1000;
                $sqlProducts = $baseSqlProducts . $offset;
                $batchProducts = $this->_getReadConnection()->fetchAll($sqlProducts);
                $products = array_merge($products, $batchProducts);
                sleep(2);
            }
        } else {
            $products = $this->_getReadConnection()->fetchAll($sqlProducts);
        }

        if (isset($attributes['qty']) || isset($attributes['is_in_stock'])) {
            $stockData  = $this->_getStockdata();
        }

        if (isset($attributeSort['url_path']) && Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/full_url', $storeId)) {
            $store = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/store_id', $storeId);
            $shopUrl = isset($store) ? Mage::app()->getStore($store)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) : Mage::getBaseUrl();
            $shopUrl = str_replace('index.php/', '', $shopUrl);
            foreach ($products as $key => $product) {
                if ($product['url_path'] || $product['url_path'] != '') {
                    $products[$key]['url_path'] = $shopUrl . $product['url_path'];
                }
            }
        }

//        $addEntityId = array_key_exists('entity_id', $attributeSort);

        foreach ($products as $key=>$product) {
            if ($checkSpecial) {
                if (($product['special_from_date'] != null && $product['special_from_date'] > now())
                    || ($product['special_to_date'] != null && $product['special_to_date'] < now())) {
                    $products[$key]['special_price'] = '';
                }
                unset($products[$key]['special_from_date']);
                unset($products[$key]['special_to_date']);
            }

            // get final price  in special_price if catalog rules applied
            if (isset($products[$key]['special_price'])) {
                $finalPrice = $this->_getFinalPrice($product, $websiteId);

                if ($finalPrice && $finalPrice != '') {
                    $products[$key]['special_price'] = $finalPrice;
                }
            }

            if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/remove_decimals', $storeId)) {
                if (isset($product['price'])) {
                    $products[$key]['price'] = intval($product['price']);
                }
                if (isset($product['special_price'])) {
                    $products[$key]['special_price'] = intval($product['special_price']);
                }
                if (isset($product['msrp'])) {
                    $products[$key]['msrp'] = intval($product['msrp']);
                }
            }

            if (Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_currency', $storeId)) {
                $currencyCode = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
                if (isset($products[$key]['price'])) {
                    $products[$key]['price'] = $products[$key]['price'] . ' ' . $currencyCode;
                }
                if (isset($products[$key]['special_price']) && $products[$key]['special_price'] != '') {
                    $products[$key]['special_price'] = $products[$key]['special_price'] . ' ' . $currencyCode;
                }
                if (isset($products[$key]['msrp']) && $products[$key]['msrp'] != '') {
                    $products[$key]['msrp'] = $products[$key]['msrp'] . ' ' . $currencyCode;
                }
                if (isset($products[$key]['group_price']) && $products[$key]['group_price'] != '') {
                    $products[$key]['group_price'] = $products[$key]['group_price'] . ' ' . $currencyCode;
                }
                if (isset($products[$key]['tier_price']) && $products[$key]['tier_price'] != '') {
                    $products[$key]['tier_price'] = $products[$key]['tier_price'] . ' ' . $currencyCode;
                }
            }

            // format wysiwyg urls in description
            if (isset($products[$key]['description'])) {
                $products[$key]['description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($products[$key]['description']);
            }

            // format wysiwyg urls in short description
            if (isset($products[$key]['short_description'])) {
                $products[$key]['short_description'] = Mage::helper('cms')->getPageTemplateProcessor()->filter($products[$key]['short_description']);
            }

            $entityId  = isset($product['entity_id']) ? $product['entity_id'] : null;
            if ($entityId) {
                if (isset($attributes['category'])) {
                    $categories = $this->_getAllCategories($type, $storeId, $entityId);
                    if (isset($categories[$entityId])) {
                        $products[$key]['category'] = $categories[$entityId];
                    }
                }
                if (isset($attributes['qty'])) {
                    $products[$key]['qty'] = $stockData[$entityId]['qty'];
                }
                if (isset($attributes['is_in_stock'])) {
                    $products[$key]['is_in_stock'] = $stockData[$entityId]['in_stock'];
                }
                //                    if (isset($attributes['parent_id'])) {
                //                        $products[$key]['parent_id'] = $parentIds[$entityId];
                //                    }
                //                    if (!$addEntityId && isset($products[$key]['entity_id'])) {
                if (!isset($attributeSort['entity_id'])) {
                    if (isset($products[$key]['entity_id'])) {
                        unset($products[$key]['entity_id']);
                    }
                }
            }

            // add store id
            if ($addStoreIdToFeed) {
                $products[$key]['store_id'] = $storeId;
            }

            // add website code
            if ($addWebsitesToFeed) {
                $products[$key]['websites'] = $this->getWebsite($storeId);
            }
        }

        return $products;
    }

    /**
     * Return all the product parent-child relations
     *
     * @return array
     */
//    private function _getParentIds()
//    {
//        $sql = "SELECT parent_id, child_id FROM catalog_product_relation";
//
//        $result = $this->_getReadConnection()->fetchAll($sql);
//
//        $parentIds = array();
//        foreach ($result as $relationData) {
//            $parentId = isset($relationData['parent_id']) ? $relationData['parent_id'] : '';
//            $childId  = isset($relationData['child_id']) ? $relationData['child_id'] : '';
//
//            $parentIds[$childId] = $parentId;
//        }
//
//        return $parentIds;
//    }

    /**
     * Return all categories
     *
     * @return array
     */
    private function _getAllCategories($type, $storeId=null, $productId)
    {
        $defaultCategId = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/default_categ_id', $storeId);

        $sql = "SELECT c2.value as name, c2.store_id as store_id, ce.parent_id as parent_id, ce.entity_id as entity_id
                FROM catalog_category_product c1
                INNER JOIN catalog_category_entity_varchar c2 ON c1.category_id = c2.entity_id AND c2.attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'name' AND entity_type_id = 3)
                INNER JOIN catalog_category_entity ce ON (c1.category_id = ce.entity_id)
                WHERE c1.product_id = $productId";

        if ($defaultCategId && $defaultCategId != '') {
            $sql .= " AND ce.path LIKE '1/" . $defaultCategId . "/%'";
        }

        $sql .= " ORDER BY ce.entity_id, c2.store_id DESC";

        $result = $this->_getReadConnection()->query($sql);

        $excludeDefCategory = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/exclude_default_category', $storeId);

        $categoriesZ  = array();
        $firstLevelId = array();

        $resultRows = [];
        $storeId = isset($storeId) ? $storeId : 0;

        while ($category = $result->fetch()) {
            $catName = isset($category['name']) ? trim(str_replace(',', ' ', $category['name'])) : '';
            $parentId = isset($category['parent_id']) ? $category['parent_id'] : '';
            $entityId = isset($category['entity_id']) ? $category['entity_id'] : '';
            $catNameStoreId = isset($category['store_id']) ? $category['store_id'] : '';

            //remove duplicate values (different store_id)
            if (in_array($productId . '-' . $parentId . '-' . $entityId, $resultRows) || $catNameStoreId > $storeId) {
                continue;
            } else {
                $resultRows[] = $productId . '-' . $parentId . '-' . $entityId;
            }

            if ($parentId == 1) {
                $firstLevelId[] = $entityId;
            }

            if(!isset($categoriesZ[$productId])) {
                $categoriesZ[$productId][$entityId] = array(
                    'catId'=>$entityId,
                    'parent_id'=>$parentId,
                    'catName'=>$catName,
                );
            } else {
                $categoriesZ[$productId][$entityId] = array(
                    'catId'=>$entityId,
                    'parent_id'=>$parentId,
                    'catName'=>$catName,
                );
            }
        }

        $allCategories = array();

        $childCategorySeparator = $this->_getChildCategorySeparator($type, $storeId);
        $mainCategorySeparator = $this->_getMainCategorySeparator($type, $storeId);

        foreach ($categoriesZ as $prodId=>$categories) {
            $catNames = array();
            $catIds = array();
            $exist  = array();

            foreach ($categories as $category) {
                $parentId = $category['parent_id'];
                $catId = $category['catId'];
                $catIds[$catId] = $category['catName'];
                if ($parentId > $catId) {
                    $catIds[$parentId] = $categories[$parentId]['catName'];
                }
                $categoryName = $category['catName'];

                if (isset($catIds[$parentId])) {
                    $parentCat = $this->_getParentNameWithDefault($categories, $parentId, $childCategorySeparator);
                    if ($parentCat) {
                        $catNames[] = $parentCat . $childCategorySeparator . $catIds[$parentId] . $childCategorySeparator . $categoryName;

                    } else {
                        $skip = false;
//                        foreach ($categories as $catX) {
//                            if ($catX['parent_id'] == $category['catId']) {
//                                $skip = true;
//                            }
//                        }
                        if (!$skip) {
                            $catNames[] = $catIds[$parentId] . $childCategorySeparator . $categoryName;
                        }
                    }
                } else {
                    if ($category['catName'] != 'Default Category') {
                        $catNames[] = $category['catName'];
                    }
                }
            }

            foreach ($catNames as $key=>$name) {
                if(in_array($name, $exist)) {
                    unset($catNames[$key]);
                }

                if ($excludeDefCategory) {
                    $defaultCategoriesToExclude = explode (',', Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/default_category_names', $storeId));
                    foreach ($defaultCategoriesToExclude as $defaultCategoryToExclude) {
                        if ($defaultCategoryToExclude == $name) {
                            unset($catNames[$key]);
                            break;
                        }
                        $catNames[$key] = str_replace($defaultCategoryToExclude . $childCategorySeparator, '', $name);
                    }
                }
            }

            $allCategories[$prodId] = str_replace(array('//', '///'), '', implode($mainCategorySeparator, $catNames));
        }

        return $allCategories;
    }

    private function _getParentNameWithDefault($categories, $parentId, $childCategorySeparator)
    {
        $parId = $categories[$parentId]['parent_id'];

        $catName = '';
        if (isset($categories[$parId]['catName'])) {
            $catName = $categories[$parId]['catName'];
            $parId = $categories[$parId]['parent_id'];
        }

        if (isset($categories[$parId]['catName'])) {
            $catName = $categories[$parId]['catName'] . $childCategorySeparator . $catName;
            $parId = $categories[$parId]['parent_id'];
        }

        if (isset($categories[$parId]['catName'])) {
            $catName = $categories[$parId]['catName'] . $childCategorySeparator . $catName;
            $parId = $categories[$parId]['parent_id'];
        }

        if (isset($categories[$parId]['catName'])) {
            $catName = $categories[$parId]['catName'] . $childCategorySeparator . $catName;
        }

       return $catName;
    }

    private function _getMediaGallery()
    {
        $sql = "
            SELECT entity_id, value
            FROM catalog_product_entity_media_gallery
            WHERE attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'media_gallery')
        ";

        $result = $this->_getReadConnection()->fetchAll($sql);

        $images = array();
        foreach ($result as $image) {
            $entityId = isset($image['entity_id']) ? $image['entity_id'] : null;
            $path     = isset($image['value']) ? $image['value'] : null;
            if (!$entityId || !$path) {
                continue;
            }

            if (!isset($images[$entityId])) {
                $images[$entityId] = array(array($path));
            } else {
                $images[$entityId][] = array($path);
            }

        }

        return $images;
    }

    /**
     * Return all products stock data
     *
     * @return array
     */
    private function _getStockdata()
    {
        $sql = "SELECT product_id, qty, is_in_stock FROM cataloginventory_stock_item";

        $result = $this->_getReadConnection()->fetchAll($sql);

        $stockInfo = array();
        foreach ($result as $stockData) {
            $id      = isset($stockData['product_id']) ? $stockData['product_id'] : '';
            $qty     = isset($stockData['qty']) ? $stockData['qty'] : '';
            $inStock = isset($stockData['is_in_stock']) ? $stockData['is_in_stock'] : '';

            $stockInfo[$id] = array (
                'qty'      => floatval($qty),
                'in_stock' => $inStock,
            );
        }

        return $stockInfo;
    }

    /**
     * Return the products attribute code
     *
     * @param array $attributes
     * @return array
     */
    private function _getProductsAttributeCode($attributes)
    {
        $codes = array_keys($attributes);

        $mapCodes = array();
        foreach ($codes as $code) {

            $sql = "SELECT attribute_id FROM eav_attribute WHERE entity_type_id = 4 AND attribute_code = '$code'";

            $mapCodes[$code] = $this->_getReadConnection()->fetchOne($sql);
        }

        return $mapCodes;
    }

    /**
     * Return the attributes type
     *
     * @param int|null $type
     * @return array
     */
    private function _getAttributesType($type=null)
    {
        $attributes = array();

        $productAttrs = Mage::getResourceModel('catalog/product_attribute_collection');

        foreach ($productAttrs as $productAttr) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttr */

            $attrCode = $productAttr->getAttributeCode();
            $attrType = $productAttr->getBackendType();

            $attributes[$attrCode] = $attrType;
        }

        return $attributes;
    }

    /**
     * Return the attribute frontend input
     *
     * @param string $code
     * @return string
     */
    private function _getAttributeInput($code)
    {
        $sql = "SELECT frontend_input FROM eav_attribute WHERE entity_type_id = 4 and attribute_code = '$code';";
        try {
            $row = $this->_getReadConnection()->fetchRow($sql);
            return $row['frontend_input'];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return the attributes map
     *
     * @param int|null $type
     * @return array
     */
    private function _getAttributesMap($type=null, $storeId=null)
    {
        $type = $type ? $type : 1;
        $conf = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/product_attributes', $storeId);
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
     * Return the database mapped values
     *
     * @param int|null $type
     * @return array
     */
    private function _getMappedValues($type=null, $storeId=null)
    {
        $type = $type ? $type : 1;
        $conf = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/product_attributes', $storeId);
        $mapValues = Mage::helper('core/unserializeArray')->unserialize($conf);

        $mappedValues  = array();
        foreach ($mapValues as $value) {
            $magentoCode = isset($value['magento_code']) ? $value['magento_code'] : null;
            $mappedValue    = isset($value['mapped_values']) ? $value['mapped_values'] : null;

            if ($magentoCode) {
                $mappedValues[$magentoCode]  = $mappedValue;
            }
        }

        return $mappedValues;
    }

    /**
     * Return the attributes sort order
     *
     * @param int|null $type
     * @return array
     */
    private function _getAttributesSort($type=null, $storeId=null)
    {
        $type = $type ? $type : 1;
        $conf = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/product_attributes', $storeId);
        $mapValues = Mage::helper('core/unserializeArray')->unserialize($conf);

        $sortOrders = array();
        foreach ($mapValues as $value) {
            $magentoCode = isset($value['magento_code']) ? $value['magento_code'] : null;
            $sortOrder   = isset($value['sort_order']) ? $value['sort_order'] : null;

            if ($magentoCode && $sortOrder) {
                $sortOrders[$magentoCode] = $sortOrder;
            }
        }
        asort($sortOrders);

        return $sortOrders;
    }

    /**
     * Return the parent product sku
     *
     * @param array $products
     * @return array
     */
    private function _getParentSku($products)
    {
        $sqlConfigurableSku = 'SELECT configurable.product_id as entity_id, e.sku as configurable_sku FROM catalog_product_entity e
           INNER JOIN catalog_product_super_link configurable ON e.entity_id = configurable.parent_id    
        ';

        $configurableProductSkus = $this->_getReadConnection()->fetchAll($sqlConfigurableSku);

        $confSkus = array();
        foreach ($configurableProductSkus as $value) {
            $confSkus[$value['entity_id']] = $value['configurable_sku'];
        }

        foreach ($products as $key => $prod) {
            $products[$key]['configurable_sku'] = isset($confSkus[$prod['entity_id']]) ? $confSkus[$prod['entity_id']] : '';
        }
        return $products;
    }

    /**
     * Add shop url in to product url
     *
     * @param array $products
     * @param string $storeId
     * @return array
     */
    private function _generateProductFullUrl($products, $storeId)
    {
        $shopUrl = isset($storeId) ? Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) : Mage::getBaseUrl();
        $shopUrl = str_replace('index.php/', '', $shopUrl);
        foreach ($products as $key => $product) {
            $products[$key]['url_path'] = $shopUrl . $product['url_path'];
        }

        return $products;
    }

    /**
     * Calculate simple products price/special_price by configurable
     * product price/special_price and associated product difference
     *
     * @param array $products
     * @param array $attributeSort
     * @return mixed
     */
    private function _calculateAssociatedProductsPricing($products, $attributeSort)
    {
        if (isset($attributeSort['price']) || isset($attributeSort['special_price'])) {
            $entityIds = array();
            foreach ($products as $key => $product) {
                $entityIds[] = $product['entity_id'];
            }

            $confAttributes = $this->_getConfigurableAttributes($entityIds);

            if ($confAttributes && count($confAttributes) > 0) {
                $sql = 'SELECT eint.entity_id, pr.pricing_value AS difference_price
                    FROM catalog_product_entity_int eint
                    INNER JOIN catalog_product_super_attribute_pricing pr
                    ON eint.`value` = pr.`value_index`
                    WHERE eint.`attribute_id` IN (' . implode(',', $confAttributes) . ') AND eint.`entity_id` IN (' . implode(',', $entityIds) . ')';

                try {
                    $pricing = $this->_getReadConnection()->fetchAll($sql);
                    if (count($pricing) > 0) {
                        if (isset($attributeSort['price'])) {
                            $products = $this->_calculateAssociatedProductsPrice($products, $pricing, $entityIds);
                        }

                        if (isset($attributeSort['special_price'])) {
                            $products = $this->_calculateAssociatedProductsSpecialPrice($products, $pricing, $entityIds);
                        }
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $products;
    }

    /**
     * Get configurable attribute ids
     *
     * @param array $productIds
     * @return array|null
     */
    private function _getConfigurableAttributes($productIds)
    {
        try {
            $sql = 'SELECT spr.`attribute_id`
                FROM catalog_product_super_link link
                INNER JOIN catalog_product_super_attribute spr
                ON link.`parent_id` = spr.`product_id`
                WHERE link.`product_id` IN (' . implode(',', $productIds) . ')';

            $attributes = $this->_getReadConnection()->fetchAll($sql);

            $attr = array();
            foreach ($attributes as $attribute) {
                $attr[$attribute['attribute_id']] = $attribute['attribute_id'];
            }

            return $attr;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Calculate simple products price by configurable product price and associated product difference
     *
     * @param array $products
     * @param array $pricing
     * @param array $entityIds
     * @return array
     */
    private function _calculateAssociatedProductsPrice($products, $pricing, $entityIds)
    {
        $basePrices = $this->_getBaseConfigurablePrice($entityIds);
        if ($basePrices && count($basePrices) > 0) {
            foreach ($products as $key => $product) {
                $newPrice = 0;
                foreach ($basePrices as $price) {
                    if ($product['entity_id'] == $price['product_id']) {
                        $newPrice += $price['price'];
                    }
                }

                foreach ($pricing as $difference) {
                    if ($product['entity_id'] == $difference['entity_id']) {
                        $newPrice += $difference['difference_price'];
                    }
                }

                if ($newPrice > 0) {
                    $products[$key]['price'] = $newPrice;
                }
            }
        }
        return $products;
    }

    /**
     * Calculate simple products special_price by configurable product special_price and associated product difference
     *
     * @param array $products
     * @param array $pricing
     * @param array $entityIds
     * @return array
     */
    private function _calculateAssociatedProductsSpecialPrice($products, $pricing, $entityIds)
    {
        $baseSpPrices = $this->_getBaseConfigurableSpecialPrice($entityIds);
        $baseSpFromPrices = $this->_getBaseConfigurableSpecialFromPrice($entityIds);
        $baseSpToPrices = $this->_getBaseConfigurableSpecialToPrice($entityIds);

        if ($baseSpPrices && count($baseSpPrices) > 0) {
            foreach ($products as $key => $product) {
                $newPrice = 0;
                foreach ($baseSpPrices as $spPrice) {
                    if ($product['entity_id'] == $spPrice['product_id']) {
                        $newPrice += $spPrice['special_price'];
                    }
                }

                foreach ($pricing as $difference) {
                    if ($product['entity_id'] == $difference['entity_id']) {
                        $newPrice += $difference['difference_price'];
                    }
                }

                if ($newPrice > 0) {
                    $products[$key]['special_price'] = $newPrice;
                }

                //replace special_from_date with parent product special_from_date
                foreach ($baseSpFromPrices as $spFromPrice) {
                    if ($product['entity_id'] == $spFromPrice['product_id']) {
                        $products[$key]['special_from_date'] = $spFromPrice['special_from_date'];
                    }
                }
                //replace special_to_date with parent product special_to_date
                foreach ($baseSpToPrices as $spToPrice) {
                    if ($product['entity_id'] == $spToPrice['product_id']) {
                        $products[$key]['special_to_date'] = $spToPrice['special_to_date'];
                    }
                }
            }
        }

        return $products;
    }

    /**
     * Return configurable products price
     *
     * @param array $productIds
     * @return array|null
     */
    private function _getBaseConfigurablePrice($productIds)
    {
        try {
            $sql = 'SELECT deci.entity_id AS parent_id, link.product_id, deci.value AS price
                    FROM catalog_product_super_link link
                    INNER JOIN catalog_product_entity_decimal deci
                    ON link.`parent_id` = deci.entity_id
                    LEFT JOIN eav_attribute eav
                    ON deci.`attribute_id` = eav.`attribute_id`
                    WHERE link.`product_id` IN (' . implode(',', $productIds) . ') AND eav.attribute_code LIKE "price"';

            $prices = $this->_getReadConnection()->fetchAll($sql);

            return $prices;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Return configurable products special_price
     *
     * @param array $productIds
     * @return array|null
     */
    private function _getBaseConfigurableSpecialPrice($productIds)
    {
        try {
            $sql = 'SELECT deci.entity_id AS parent_id, link.product_id, deci.value AS special_price
                    FROM catalog_product_super_link link
                    INNER JOIN catalog_product_entity_decimal deci
                    ON link.`parent_id` = deci.entity_id
                    LEFT JOIN eav_attribute eav
                    ON deci.`attribute_id` = eav.`attribute_id`
                    WHERE link.`product_id` IN (' . implode(',', $productIds) . ') AND eav.attribute_code LIKE "special_price"';

            $spPrices = $this->_getReadConnection()->fetchAll($sql);

            return $spPrices;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Return configurable products special_from_date
     *
     * @param array $productIds
     * @return array|null
     */
    private function _getBaseConfigurableSpecialFromPrice($productIds)
    {
        try {
            $sql = 'SELECT dtm.entity_id AS parent_id, link.product_id, dtm.value AS special_from_date
                    FROM catalog_product_super_link link
                    INNER JOIN catalog_product_entity_datetime dtm
                    ON link.`parent_id` = dtm.entity_id
                    LEFT JOIN eav_attribute eav
                    ON dtm.`attribute_id` = eav.`attribute_id`
                    WHERE link.`product_id` IN (' . implode(',', $productIds) . ') AND eav.attribute_code LIKE "special_from_date"';

            $spFromPrices = $this->_getReadConnection()->fetchAll($sql);

            return $spFromPrices;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Return configurable products special_to_date
     *
     * @param array $productIds
     * @return array|null
     */
    private function _getBaseConfigurableSpecialToPrice($productIds)
    {
        try {
            $sql = 'SELECT dtm.entity_id AS parent_id, link.product_id, dtm.value AS special_to_date
                    FROM catalog_product_super_link link
                    INNER JOIN catalog_product_entity_datetime dtm
                    ON link.`parent_id` = dtm.entity_id
                    LEFT JOIN eav_attribute eav
                    ON dtm.`attribute_id` = eav.`attribute_id`
                    WHERE link.`product_id` IN (' . implode(',', $productIds) . ') AND eav.attribute_code LIKE "special_to_date"';

            $spToPrices = $this->_getReadConnection()->fetchAll($sql);

            return $spToPrices;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Set the feed last run time
     *
     * @param int $type
     */
    private function _setLastRunTime($type)
    {
        Mage::getConfig()->saveConfig('blugento_fullfeed/feed_' . $type . '/last_run_time', date('Y-m-d H:i:s', time()), 'default', 0)->reinit();
    }

    /**
     * Return the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        $resource = Mage::getSingleton('core/resource');

        return $resource->getConnection('core_read');
    }

    private function _getFinalPrice($product, $websiteId=NULL)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        try {
            $query = "SELECT final_price FROM catalog_product_index_price WHERE entity_id = {$product['entity_id']} AND customer_group_id = 0 AND entity_id IN (SELECT product_id FROM catalogrule_product)";
            if ($websiteId) {
                $query .= " AND website_id = $websiteId;";
            }
            $result = $connection->query($query);
            $row = $result->fetch();
            return $row['final_price'];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    private function getWebsite($storeId)
    {
        $sql = 'SELECT cw.code
                FROM core_store cs
                INNER JOIN core_website cw
                ON cs.website_id = cw.website_id
                WHERE cs.store_id = ' . $storeId;

        try {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            return $connection->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Get Mapping Transformed Values
     *
     * @param $code
     * @param $value
     * @param $mappedValues
     * @return mixed
     */
    private function _getTransformedMappedValues($code, $value, $mappedValues, $type)
    {
        if ($code == 'price') {
            $showCurrency = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_currency');
            $currency = '';
            if ($showCurrency) {
                $prices = explode(' ', $value);
                $price = $prices[0];
                $currency = $prices[1];
            } else {
                $price = $value;
            }

            $formula = preg_replace('/[^0-9\-\/x\+\*\.]/', '', $mappedValues['price']);
            $formula = 'return ' . str_replace(['x', 'X'], [$price, $price], $formula) . ';';

            if ($currency) {
                return eval($formula) . ' ' . $currency;
            } else {
                return eval($formula);
            }
        } elseif ($code == 'special_price') {
            if ($value) {
                $showCurrency = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/show_currency');
                $currency = '';
                if ($showCurrency) {
                    $specialPrices = explode(' ', $value);
                    $specialPrice = $specialPrices[0];
                    $currency = $specialPrices[1];
                } else {
                    $specialPrice = $value;
                }

                $formula = preg_replace('/[^0-9\-\/x\+\*\.]/', '', $mappedValues['special_price']);
                $formula = 'return ' . str_replace(['x', 'X'], [$specialPrice, $specialPrice], $formula) . ';';

                if ($currency) {
                    return eval($formula) . ' ' . $currency;
                } else {
                    return eval($formula);
                }
            } else {
                return $value;
            }
        } else {
            $valuesToChange = [];
            $mappedCodeValues = explode('#', $mappedValues[$code]);
            if (is_array($mappedCodeValues)) {
                foreach ($mappedCodeValues as $mappedCodeValue) {
                    $valueToChange = explode(':', $mappedCodeValue);
                    $valuesToChange[$valueToChange[0]] = $valueToChange[1];
                }

            } else {
                $valueToChange = explode(':', $mappedValues[$code]);
                $valuesToChange[$valueToChange[0]] = $valueToChange[1];
            }

            return $valuesToChange[$value] ? $valuesToChange[$value] : $value;
        }

        return $value;
    }

    private function countRows()
    {
        try {
            $query = "select count(*) as val from catalog_product_entity";
            $result = $this->_getReadConnection()->query($query);
            $row = $result->fetch();
            return $row['val'];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    private function getExcludedSkuList($type, $storeId)
    {
        $skuList = [];

        $excludedSKUs = Mage::getStoreConfig('blugento_fullfeed/feed_' . $type . '/exclude_specific_skus', $storeId);
        if ($excludedSKUs && $excludedSKUs != '') {
            $excludedSKUs = explode(PHP_EOL, $excludedSKUs);
            foreach ($excludedSKUs as $excludedSKU) {
                if ($excludedSKU != '') {
                    $skuList[] = trim($excludedSKU);
                }
            }
        }

        return $skuList;
    }
}
