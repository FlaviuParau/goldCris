<?php
/**
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
 * @package     Blugento_AlsoViewed
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_AlsoViewed_Model_Product_Collection extends Mage_Core_Model_Abstract
{
	public function getProducts($productIds)
	{
		$readConnection    = Mage::getSingleton('core/resource')->getConnection('core_read');
		$currency          = Mage::app()->getStore()->getCurrentCurrencyCode();
		$productAttributes = array('name', 'url_path', 'price', 'special_price', 'short_description', 'manufacturer', 'small_image', 'image_hover');
		$attributesId      = $this->_getAttributesIds($readConnection, $productAttributes, 4);
		
		$sql = 'SELECT DISTINCT cpe.entity_id AS entity_id, cpe.sku AS sku, cpe.type_id as type_id, cpev.value AS name,
 					cpevch.value AS url, cpedec.value AS price, cpedecsp.value AS special_price, cist.qty AS qty, cpet.value AS short_description
                FROM catalog_product_entity cpe
                LEFT JOIN catalog_product_entity_varchar cpev
                ON cpe.entity_id = cpev.entity_id AND cpev.attribute_id = ' . $attributesId['name'] . '
                LEFT JOIN catalog_product_entity_varchar cpevch
                ON cpe.entity_id = cpevch.entity_id AND cpevch.attribute_id = ' . $attributesId['url_path'] . '
                LEFT JOIN catalog_product_entity_decimal cpedec
                ON cpe.entity_id = cpedec.entity_id AND cpedec.attribute_id = ' . $attributesId['price'] . '
                LEFT JOIN catalog_product_entity_decimal cpedecsp
                ON cpe.entity_id = cpedecsp.entity_id AND cpedecsp.attribute_id = ' . $attributesId['special_price'] . '
                LEFT JOIN cataloginventory_stock_item cist
                ON cpe.entity_id = cist.product_id
                LEFT JOIN catalog_product_entity_text cpet
                ON cpe.entity_id = cpet.entity_id AND cpet.attribute_id = ' . $attributesId['short_description'] . '
                WHERE cpe.entity_id IN (' . $productIds . ')';
		
		try {
			$products = $readConnection->fetchAll($sql);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		$productsData = array();
		
		if (is_array($products)) {
			$categories    = $this->_getProductsCategories($readConnection, $productIds);
			$manufacturers = $this->_getProductsManufacturer($readConnection, $attributesId['manufacturer'], $productIds);
			$images        = $this->_getProductImages($readConnection, $attributesId['small_image'], $attributesId['image_hover'], $productIds);
			
			foreach ($products as $product) {
				
				$manufacturerName = '';
				foreach ($manufacturers as $k => $manufacturer) {
					if ($product['entity_id'] == $manufacturer['entity_id']) {
						$manufacturerName = $manufacturer['manufacturer'];
						unset($manufacturers[$k]);
					}
				}
				
				$quantity = number_format((float)$product['qty'], 2, '.', '');
				$imagesArray = $images[$product['entity_id']];
				
				$productsData[] = array(
					'id'                => $product['entity_id'],
					'type_id'           => $product['type_id'],
					'sku'               => $product['sku'],
					'name'              => $product['name'],
					'url'               => $product['url'],
					'categories'        => $categories[$product['entity_id']],
					'short_description' => $product['short_description'],
					'price'             => sprintf('%.2f', $product['price']),
					'special_price'     =>  $product['special_price'] ? sprintf('%.2f', $product['special_price']) : '',
					'price_discount'    => $product['special_price'] ? $this->_getDiscountValue($product['price'], $product['special_price']) : '',
					'price_percentage'  => $this->_getDiscountPercentage($product['price'], $product['special_price']),
					'currency'          => $currency,
					'quantity'          => $quantity,
					'manufacturer'      => trim($manufacturerName),
					'images'            => $imagesArray,
					'add_to_cart'       => $this->_getAddToCartUrl($product),
					'wishlist_url'      => $this->_getWishlistUrl($product),
					'compare_url'       => $this->_getCompareUrl($product)
				);
			}
		}
		
		return $productsData;
	}
	
	private function _getAttributesIds($connection, $attributes, $typeId)
	{
		$attributes = '"' . implode('", "', $attributes) . '"';
		
		$sql = 'SELECT attribute_id AS id, attribute_code AS name FROM eav_attribute
                WHERE attribute_code IN (' . $attributes . ') AND entity_type_id = ' . $typeId;
		
		try {
			$result = $connection->fetchAll($sql);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		$attrs = array();
		
		if (is_array($result)) {
			foreach ($result as $item) {
				$attrs[$item['name']] = $item['id'];
			}
		}
		
		return $attrs;
	}
	
	private function _getProductsCategories($connection, $productIds)
	{
		$sql = 'SELECT cpe.entity_id AS entity_id, cat.category_id AS category_id
				FROM catalog_product_entity cpe
                INNER JOIN catalog_category_product cat
                ON cpe.entity_id = cat.product_id
                WHERE cpe.entity_id IN (' . $productIds . ')';
		
		try {
			$categories = $connection->fetchAll($sql);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		$categoryArray = array();
		
		if (is_array($categories)) {
			foreach ($categories as $category) {
				$categoryArray[$category['entity_id']][] = $category['category_id'];
			}
		}
		
		return $categoryArray;
	}
	
	private function _getProductsManufacturer($connection, $attrId, $productIds)
	{
		$sql = 'SELECT cpeint.entity_id AS entity_id, eaovman.value AS manufacturer
				FROM catalog_product_entity_int cpeint
                INNER JOIN eav_attribute_option_value eaovman
                ON cpeint.value = eaovman.option_id AND attribute_id = ' . $attrId . '
                WHERE cpeint.entity_id IN (' . $productIds . ')';
		
		try {
			$manufacturers = $connection->fetchAll($sql);
			return $manufacturers;
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	private function _getProductImages($connection, $imageAttrId, $hoverImageAttrId, $productIds)
	{
		$sql = 'SELECT cpe.entity_id AS entity_id, cpeimg.value AS small_image, cpeimg.value AS hover_image
                FROM catalog_product_entity cpe
                LEFT JOIN catalog_product_entity_varchar cpeimg
                ON cpe.entity_id = cpeimg.entity_id AND cpeimg.attribute_id IN (' . $imageAttrId . ',' . $hoverImageAttrId . ')
                WHERE cpe.entity_id IN (' . $productIds . ')';
		
		try {
			$images = $connection->fetchAll($sql);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		$imagesArray = array();
		
		if (is_array($images)) {
			foreach ($images as $image) {
				if ($image['small_image'] == 'no_selection') {
					$image['small_image'] = '';
				}
				
				if ($image['hover_image'] == 'no_selection') {
					$image['hover_image'] = $image['small_image'] ?: '';
				}
				
				if (($image['small_image'] == '' || !$image['small_image']) && ($image['hover_image'] == '' || !$image['hover_image'])) {
					continue;
				}
				
				$imagesArray[$image['entity_id']][] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image['small_image'];
				$imagesArray[$image['entity_id']][] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image['hover_image'];
				$imagesArray[$image['entity_id']]   = array_unique($imagesArray[$image['entity_id']]);
			}
		}
		
		return $imagesArray;
	}
	
	/**
	 * Return price by special price from and to.
	 *
	 * @param string $price
	 * @param string $special
	 * @return string
	 */
	protected function _establishPrice($price, $special)
	{
		if ($special != null && ($special < $price)) {
			$price = $special;
		}
		
		return $price;
	}
	
	protected function _getDiscountValue($price, $special)
	{
		$priceDiscountValue = '';
		if ($special != $price) {
			$priceDiscountValue = '(' . Mage::helper('core')->currency(($special - $price), true, false) . ')';
		}
		
		return $priceDiscountValue;
	}
	
	protected function _getDiscountPercentage($price, $special)
	{
		$priceDiscountPercentage = '';
		if ($special != $price && $price != 0) {
			$priceDiscountPercentage = '-' . (round((100 - ($special / $price) * 100))) . "%";
		}
		
		return $priceDiscountPercentage;
	}
	
	/**
	 * Retrieve url for add product to cart
	 *
	 * @param $product
	 * @param array $additional
	 * @return string
	 */
	protected function _getAddToCartUrl($product, $additional = array())
	{
		$routeParams = array(
			Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')
				->urlEncode(Mage::helper('core/url')->getCurrentUrl()),
			'product' => $product['entity_id'],
			Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()
		);
		
		if (!empty($additional)) {
			$routeParams = array_merge($routeParams, $additional);
		}
		
		if (Mage::app()->getRequest()->getRouteName() == 'checkout'
			&& Mage::app()->getRequest()->getControllerName() == 'cart') {
			$routeParams['in_cart'] = 1;
		}
		
		return Mage::getUrl('checkout/cart/add', $routeParams);
	}
	
	/**
	 * Retrieve url for adding product to wishlist with params
	 *
	 * @param $item
	 * @param array $params
	 *
	 * @return  string|bool
	 */
	public function _getWishlistUrl($item, $params = array())
	{
		$productId = $item['entity_id'];
		if ($productId) {
			$params['product'] = $productId;
			$params[Mage_Core_Model_Url::FORM_KEY] = Mage::getSingleton('core/session')->getFormKey();
			
			return Mage::getUrl('wishlist/index/add', $params);
		}
		
		return false;
	}
	
	/**
	 * Retrieve url for adding product to compare
	 *
	 * @param $product
	 * @return  string|bool
	 */
	public function _getCompareUrl($product)
	{
		$params = array(
			'product' => $product['entity_id'],
			Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()
		);
		
		return Mage::getUrl('catalog/product_compare/add', $params);
	}
}
