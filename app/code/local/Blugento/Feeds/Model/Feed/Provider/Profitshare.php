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

class Blugento_Feeds_Model_Feed_Provider_Profitshare extends Blugento_Feeds_Model_Feed_Abstract
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
        $this->_feedName = 'profitshare';
        return $this->_feedName;
    }

    /**
     * Set feed save model
     * @return mixed|void
     */
    public function setFeedSaveModel()
    {
        $this->_feedSaveModel = 'blugento_feeds/feed_save_provider_profitshare';
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

        $storeId = $this->_getStoreId($args);

        $dataFeedSeparator = ','; // default '|'

        // Get product IDs
        try {
            // Get simple products
            $visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);

            $products = Mage::getModel('catalog/product')->getCollection();
            $products->addAttributeToFilter('status', 1); // enabled
            $products->addAttributeToFilter('visibility', $visibility); // catalog, search
            $products->addAttributeToFilter('type_id', array('in' => array('simple', 'configurable', 'grouped', 'bundle')));
            //$products->addAttributeToSelect(array('name','description','price','special_price','special_from_date','special_to_date','status','visibility','manufacturer','image'));
            $products->addAttributeToSelect('*');
            // Get product data
            return $this->_getProductData($products, $dataFeedSeparator);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get product details
     *
     * @param $product
     * @param $showDescription
     * @param $showImage
     * @param $addVat
     * @param $vatValue
     * @param $dataFeedSeparator
     * @param $dataFeedCurrency
     * @param $inStockMsg
     * @param $outOfStockMsg
     * @param $manufacturers
     * @param $categories
     * @return array
     */
    public function getProductDetails($product, $showDescription, $showImage,
                                      $addVat, $vatValue, $dataFeedSeparator, $dataFeedCurrency, $inStockMsg, $outOfStockMsg,
                                      &$manufacturers, &$categories)
    {

        $weight    = $product->getWeight();
        $short_description = $product->getShortDescription();
        $prodName  = $this->_sanitizeString($product->getName());
        $description  = substr(strip_tags($product->getDescription()), 0, 400);
        $description = trim($description);
        $description = str_replace(array('â€', 'â€ž','\r\n','\r','\n','"',PHP_EOL), array('-', '-','','','','-',''),$description);
        $description = str_replace(array("\r", "\n"), '', $description);
        $store = Mage::app()->getStore();
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        $percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));
        $prodUrl   = $this->getProductUrl($product->getProductUrl());
        $prodImage = $product->getImageUrl();
        $priceWithoutTax = $product->getPrice() - (($percent / 100) * $product->getPrice());
        $price = $product->getPrice();
        $specialpriceWithoutTax = $product->getFinalPrice() - (($percent / 100) * $product->getFinalPrice());
        $specialPrice =$specialpriceWithoutTax <  $price ? $specialpriceWithoutTax : '';
        $manufacturerId = $product->getManufacturer();
        $stockInfo = Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product);
        $inStock = $stockInfo->getIsInStock();

        $freeShipping = 0;
        $gift = 0;
        if ($inStock) {
            $status = 1;
        } else {
            $status = 0;
        }

        if ($inStock) {
            $instock = 'in stoc';
        } else {
            $instock = 'stoc epuizat';
        }


        // Add VAT to prices
        if ($addVat == 1) {
            $prodPrice = $prodPrice * $vatValue;
        }

        $manufacturer = $product->getManufacturer();
        if ($manufacturer) {
            if (!array_key_exists($manufacturer, $manufacturers)) {
                $manufacturers[$manufacturer] = $product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
            }
            $manufacturer = $manufacturers[$manufacturer];
        } else {
            $manufacturer = '';
        }

        // Get the product categories
        $categoryName = '';
        $parentcategoryName = '';

        foreach($product->getCategoryIds() as $categoryId){

            $category = Mage::getModel('catalog/category')->load($categoryId);
            $parentCategory = Mage::getModel('catalog/category')->load($category->getParentId());
            $categoryName = $category->getName();
            $parentcategoryName = $parentCategory->getName();
        }

        // Clean product name (new lines)
        $prodName = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodName));
        $prodDesc = $this->replaceNotInTags("\n", "<BR />", $prodDesc);
        $prodDesc = str_replace(array("\n", "\r", "\t"), '', strip_tags($prodDesc));
        if ($dataFeedSeparator == '|') {
            $prodName = str_replace('|', ',', $prodName);
            $prodDesc = str_replace('|', ',', $prodDesc);
        }

        $result = array(

            'category_id'       => $categoryId,
            'category_name'     => trim($categoryName),
            'parent_category_name' => trim($parentcategoryName),
            'manufacturer'      => $manufacturer,
            'manufacturer_id'   => $manufacturerId,
            'prod_id'           => $product->getId(),
            'product_code'      => utf8_encode($product->getSku()),
            'prod_name'         => $prodName,
            'prod_desc'         => Mage::helper('cms')->getPageTemplateProcessor()->filter($description),
            'prod_url'          => $prodUrl,
            'prod_image'        => $prodImage,
            'price_without_tax' => $priceWithoutTax,
            'price'             => $price,
            'special_price'     => $specialPrice,
            'datafeed_currency' => $dataFeedCurrency,
            'in_stock'          => $instock,
            'free_shipping'     => $freeShipping,
            'gift'              => $gift,
            'status'            => $status
        );

        return $result;
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
     * Prepare product data lines to cache or print
     *
     * @param $allProds
     * @param $dataFeedSeparator
     * @return array
     */
    protected function _getProductData($allProds, $dataFeedSeparator)
    {
        // Array to check if product is already send
        $alreadySent = array();

        $showDescription  = $this->getOption('show_description', 0, true);
        $showImage        = $this->getOption('show_image', 1, true);
        $addVat           = $this->getOption('add_vat', 0, true);
        $vatValue         = $this->getOption('vat_value', 1);
        $inStockMsg       = $this->getOption('in_stock', 'In stoc');
        $outOfStockMsg    = $this->getOption('out_of_stock', 'Indisponibil');
        $dataFeedCurrency = 'RON'; //$this->getOption('currency', 'RON cu TVA');

        $manufacturers = array();
        $categories = array();

        $productData = array();

        foreach($allProds as $product) {

            $productId = $product->getId();

            // If we've sent this one, skip the rest - this is to ensure that we do not get duplicate products
            if (@$alreadySent[$productId] == 1) {
                continue;
            }

            // Get product details
            $details = $this->getProductDetails($product, $showDescription, $showImage, $addVat, $vatValue, $dataFeedSeparator, $dataFeedCurrency, $inStockMsg, $outOfStockMsg, $manufacturers, $categories);
            if ($details['category_id'] != '') {
                // Output the data feed content
                $productData[] = $this->getProductFeedLine($details, array(
                    'dataFeedSeparator' => $dataFeedSeparator,
                    'currency' => $dataFeedCurrency
                ));
            }

            $alreadySent[$productId] = 1;
        }

        return $productData;
    }


    public function getProductFeedLine($product, $args)
    {
        $line = array(
            $product['category_id'],
            $product['category_name'],
            $product['parent_category_name'],
            $product['manufacturer'],
            $product['manufacturer_id'],
            $product['prod_id'],
            $product['product_code'],
            $product['prod_name'],
            $product['prod_desc'],
            $product['prod_url'],
            $product['prod_image'],
            $product['price_without_tax'],
            $product['price'],
            $product['special_price'],
            $product['datafeed_currency'],
            $product['in_stock'],
            $product['free_shipping'],
            $product['gift'],
            $product['status']

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
}