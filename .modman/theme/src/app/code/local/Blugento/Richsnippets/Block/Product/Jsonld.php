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
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Richsnippets_Block_Product_Jsonld extends Blugento_Richsnippets_Block_Abstract
{
	/**
	 * @var Mage_Catalog_Model_Product_Type
	 */
	const BUNDLE = 'bundle';
	
	const STAMPED_API_PUBLIC_KEY_CONFIGURATION = 'stamped_app/stamped_app_settings_group/stamped_publickey';
	const STAMPED_API_STORE_URL_CONFIGURATION  = 'stamped_app/stamped_app_settings_group/stampedapi_storeurl';
	const STAMPED_API_SECRET_KEY_CONFIGURATION = 'stamped_app/stamped_app_settings_group/stamped_apisecretkey';

	const YOTPO_API_PUBLIC_KEY_CONFIGURATION = 'yotpo/yotpo_general_group/yotpo_appkey';
	const YOTPO_API_SECRET_KEY_CONFIGURATION = 'yotpo/yotpo_general_group/yotpo_secret';
	
	/**
	 * Blugento Rich Snippets Current Product
	 *
	 * @var Mage_Catalog_Model_Product _product
	 */
	protected $_product;
	
	/**
	 * Blugento Rich Snippets _childProductsCount
	 */
	protected $_childProductsCount = 0;
	
	/**
	 * Blugento Rich Snippets Model
	 *
	 * @var Blugento_Richsnippets_Model_Product_Data _configurableProductModel
	 */
	protected $_configurableProductModel;
	
	/**
	 * Blugento_Richsnippets_Block_Product_Jsonld constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_product                  = Mage::registry('current_product');
		$this->_configurableProductModel = Mage::getModel('blugento_richsnippets/product_data');
	}
	
	/**
	 * Get Data for Product schema.org
	 *
	 * @return Blugento_Richsnippets_Block_Abstract _richSnippets
	 * @throws Mage_Core_Model_Store_Exception
	 * @throws Mage_Core_Exception
	 */
	public function getStructuredData()
	{
		if ($this->_product->isConfigurable()) {
			$this->_configurableProduct($this->_product);
		} elseif ($this->_product->getTypeId() == self::BUNDLE) {
			$this->_bundleProduct($this->_product);
		} else {
			$this->_simpleProduct($this->_product);
		}
		
		return $this->_richSnippets;
	}
	
	/**
	 * Save in _richSnippets data for simple product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @throws Mage_Core_Model_Store_Exception
	 * @throws Mage_Core_Exception
	 */
	protected function _simpleProduct($product)
	{
		$this->_richSnippets = $this->_productGlobalInfo($product);
		
		$this->_getProductAttributes($product);

		$this->_richSnippets['offers'] = array(
			'@type'           => 'Offer',
			'url'             => $product->getProductUrl(),
			'availability'    => $this->_isInStock($product),
			'priceCurrency'   => $this->_showPrice() ? Mage::app()->getStore()->getCurrentCurrencyCode() : '',
			'price'           => $this->_showPrice() ? (float) number_format($product->getFinalPrice(), 2, '.', '') : '',
			'priceValidUntil' => $this->_showPrice() ? date('Y-m-d', strtotime('+1 year')) : '',
			'itemCondition'   => 'https://schema.org/NewCondition',
		);

		$aggregateRating = '';
		$reviews = '';

		if (Mage::helper('core')->isModuleEnabled('Stamped_App') && $this->_isStampedApiConfigured()) {
			$aggregateRating = $this->_addStampedAggregateRating();
			$reviews = $this->_addStampedRating($product->getId());
		} else if (Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo') && $this->_isYotpoApiConfigured()) {
			$aggregateRating = $this->_addYotpoAggregateRating();
			$reviews = $this->_addYotpoRating($product->getId());
		} else {
			$aggregateRating = $this->_addAggregateRating($product->getId());
			$reviews = $this->_returnProductRating($product->getId());
		}

		$this->_richSnippets['aggregateRating'] = $aggregateRating;
		$this->_richSnippets['review'] = $reviews;
	}

	/**
	 * Save in _richSnippets data for configurable product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @throws Mage_Core_Model_Store_Exception
	 * @throws Mage_Core_Exception
	 */
	protected function _configurableProduct($product)
	{
		$priceRange          = $this->_getLowestHighestPrice($product);
		$simpleProductsData  = $this->_getSimpleProductsDataInfo($product);
		$this->_richSnippets = $this->_productGlobalInfo($product);
		
		$this->_getProductAttributes($product);
		
		$this->_richSnippets['offers'] = array(
			'@type'           => 'AggregateOffer',
			'url'             => $product->getProductUrl(),
			'availability'    => $this->_isInStock($product),
			'priceCurrency'   => $this->_showPrice() ? Mage::app()->getStore()->getCurrentCurrencyCode() : '',
			'highPrice'       => $this->_showPrice() ? (float) number_format((float) $priceRange['highPrice'], 2, '.', '') : '',
			'lowPrice'        => $this->_showPrice() ? (float) number_format((float) $priceRange['lowPrice'], 2, '.', '') : '',
			'offerCount'      => $simpleProductsData ? count($simpleProductsData) : 1,
			'priceValidUntil' => $this->_showPrice() ? date('Y-m-d', strtotime('+1 year')) : ''
		);

		$this->_richSnippets['model'] = $simpleProductsData;

		$aggregateRating = '';
		$reviews = '';

		if (Mage::helper('core')->isModuleEnabled('Stamped_App') && $this->_isStampedApiConfigured()) {
			$aggregateRating = $this->_addStampedAggregateRating();
			$reviews = $this->_addStampedRating($product->getId());
		} else if (Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo') && $this->_isYotpoApiConfigured()) {
			$aggregateRating = $this->_addYotpoAggregateRating();
			$reviews = $this->_addYotpoRating($product->getId());
		} else {
			$aggregateRating = $this->_addAggregateRating($product->getId());
			$reviews = $this->_returnProductRating($product->getId());
		}

		$this->_richSnippets['aggregateRating'] = $aggregateRating;
		$this->_richSnippets['review'] = $reviews;
	}

	/**
	 * Save in _richSnippets data for bundle product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @throws Mage_Core_Model_Store_Exception
	 * @throws Mage_Core_Exception
	 */
	protected function _bundleProduct($product)
	{
		$priceRange          = $this->_configurableProductModel->getBundleProductPrice($product);
		$this->_richSnippets = $this->_productGlobalInfo($product);
		
		$this->_getProductAttributes($product);

		$selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
			$product->getTypeInstance(true)->getOptionsIds($product), $product
		);

		$this->_richSnippets['offers'] = array(
			'@type'           => 'AggregateOffer',
			'url'             => $product->getProductUrl(),
			'availability'    => $this->_isInStock($product),
			'priceCurrency'   => $this->_showPrice() ? Mage::app()->getStore()->getCurrentCurrencyCode() : '',
			'highPrice'       => $this->_showPrice() ? (float) number_format($priceRange[1], 2, '.', '') : '',
			'lowPrice'        => $this->_showPrice() ? (float) number_format($priceRange[0], 2, '.', '') : '',
			'offerCount'      => $selectionCollection ? count($selectionCollection) : 1,
			'priceValidUntil' => $this->_showPrice() ? date('Y-m-d', strtotime('+1 year')) : ''
		);

		$aggregateRating = '';
		$reviews = '';

		if (Mage::helper('core')->isModuleEnabled('Stamped_App') && $this->_isStampedApiConfigured()) {
			$aggregateRating = $this->_addStampedAggregateRating();
			$reviews = $this->_addStampedRating($product->getId());
		} else if (Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo') && $this->_isYotpoApiConfigured()) {
			$aggregateRating = $this->_addYotpoAggregateRating();
			$reviews = $this->_addYotpoRating($product->getId());
		} else {
			$aggregateRating = $this->_addAggregateRating($product->getId());
			$reviews = $this->_returnProductRating($product->getId());
		}

		$this->_richSnippets['aggregateRating'] = $aggregateRating;
		$this->_richSnippets['review'] = $reviews;
	}
	
	/**
	 * Return name, sku, image, url, description, mpn and category of current product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	protected function _productGlobalInfo($product)
	{
		$arrayInfo = array(
			'@context'    => 'http://schema.org',
			'@type'       => 'Product',
			'name'        => $product->getName(),
			'sku'         => $product->getSku(),
			'image'       => array((string) Mage::helper('catalog/image')->init($product, 'image')),
			'url'         => $product->getProductUrl(),
			'description' => $this->_getProductDescription($product),
			'mpn'         => $product->getSupplierProductReference() ?: $product->getSku(),
			'brand'       => array(
				'@type' => 'Brand',
				'name'  => $this->_getProductManufacturer($product),
			),
			'category'    => $this->_configurableProductModel->getProductCategories($product->getId())
		);
		
		return $arrayInfo;
	}
	
	/**
	 * Return current product short/long description
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	protected function _getProductDescription($product)
	{
		if ($product->getData('meta_description') != '') {
			return html_entity_decode(strip_tags($product->getData('meta_description')));
		} elseif ($product->getShortDescription()) {
			return html_entity_decode(strip_tags($product->getShortDescription()));
		} else {
			return $this->_richSnippetHelper->getCoreHelper('string')->substr(html_entity_decode(strip_tags($product->getDescription())), 0, 165);
		}
	}
	
	/**
	 * Check if current product is available
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	protected function _isInStock($product)
	{
		if (!$product->isSaleable()) {
			return 'http://schema.org/OutOfStock';
		}
		
		return 'http://schema.org/InStock';
	}
	
	/**
	 * Return the lowest and highest price for configurable product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	protected function _getLowestHighestPrice($product)
	{
		$childProducts = $this->_getChildProductsCollection($product);
		
		$childPriceLowest  = '';
		$childPriceHighest = '';
		
		foreach ($childProducts as $child) {
			if ($child['is_in_stock']) {
				$this->_childProductsCount++;
			}
			
			if ($childPriceLowest == '' || $childPriceLowest > $child['final_price']) {
				$childPriceLowest = $child['final_price'] > 0 ? $child['final_price'] : $product->getFinalPrice();
			}
			
			if ($childPriceHighest == '' || $childPriceHighest < $child['final_price']) {
				$childPriceHighest = $child['final_price'] > 0 ? $child['final_price'] : $product->getFinalPrice();
			}
		}
		
		return array(
			'lowPrice'  => $childPriceLowest,
			'highPrice' => $childPriceHighest
		);
	}
	
	/**
	 * Split configurable product into simple products and return simple product information
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array|null
	 */
	protected function _getSimpleProductsDataInfo($product)
	{
		if ($this->_richSnippetHelper->splitConfigurableProduct()) {
			
			$allowedAttr = $this->_getChildProductsCollection($product);
			$productData = array();
			$aggregateRating = '';
			$review = '';

			if (Mage::helper('core')->isModuleEnabled('Stamped_App') && $this->_isStampedApiConfigured()) {
				$aggregateRating = $this->_addStampedAggregateRating();
				$review = $this->_addStampedRating($product->getId());
			} else if (Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo') && $this->_isYotpoApiConfigured()) {
				$aggregateRating = $this->_addYotpoAggregateRating();
				$review = $this->_addYotpoRating($product->getId());
			} else {
				$aggregateRating = $this->_addAggregateRating($product->getId());
				$review = $this->_returnProductRating($product->getId());
			}

			foreach ($allowedAttr as $attribute) {
				
				$attributeCode  = $attribute['attribute_code'];
				$attributeValue = $attribute['attribute_value'];
				
				foreach ($this->_supperAttributes() as $value) {
					if ($attributeCode === $value['attributes']) {
						$attributeCode = $value['name'];
					}
				}

				$productData[] = array(
					'@type'           => 'ProductModel',
					'name'            => $attribute['name'] ?: $product->getName(),
					'sku'             => $attribute['sku'],
					'mpn'             => $attribute['sku'],
					'description'     => $attribute['description'] ?: $product->getShortDescription(),
					'brand'           => array(
						'@type' => 'Brand',
						'name'  => $attribute['manufacturer']
					),
					'image'           => array((string) $this->helper('catalog/image')->init($product, 'image', $attribute['image'])),
					$attributeCode    => $attributeValue,
					'aggregateRating' => $aggregateRating,
					'review'          => $review,
					'offers' => array(
						'@type'           => 'Offer',
						'url'             => $product->getProductUrl(),
						'price'           => $this->_showPrice() ? (float) number_format(($attribute['final_price'] > 0) ? $attribute['final_price'] : $product->getFinalPrice(), 2, '.', '') : '',
						'priceCurrency'   => $this->_showPrice() ? Mage::app()->getStore()->getCurrentCurrencyCode() : '',
						'priceValidUntil' => $this->_showPrice() ? date('Y-m-d', strtotime('+1 year')) : '',
						'availability'    => $attribute['is_in_stock'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock',
						'itemCondition'   => 'https://schema.org/NewCondition',
					)
				);
			}
			
			return $productData;
		}
		
		return null;
	}
	
	/**
	 * Get Current Product Manufacturer Name
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	protected function _getProductManufacturer($product)
	{
		$brand = '';
		
		if ($product->getAttributeText('manufacturer') != '') {
			$brand = $product->getAttributeText('manufacturer');
		}
		
		return $brand;
	}

	/**
	 * If product has rating, update _richSnippets with reviews summary
	 *
	 * @param $productId
	 * @return array|null|string
	 */
	protected function _addAggregateRating($productId)
	{
		if ($this->_checkIfReviewsEnabled()) {
			$ratingDatas = $this->_configurableProductModel->getProductReviewsSummary($productId);
			$productData = array();

			if (is_array($ratingDatas) && count($ratingDatas) > 0) {
				foreach ($ratingDatas as $ratingData) {
					$json['reviewCount'] = (float) $ratingData['reviews_count'];
					$json['ratingValue'] = (float) number_format($ratingData['rating_summary'] / 20, 2);

					if ($json['ratingValue'] == 0 || $json['reviewCount'] == 0) {
						$productData = null;
					} else {
						$productData = array(
							'@type'       => 'AggregateRating',
							'bestRating'  => 5,
							'worstRating' => 0,
							'ratingValue' => $json['ratingValue'],
							'reviewCount' => $json['reviewCount'],
						);
					}
				}
				
				return $productData;
			}
		}

		return null;
	}
	
	/**
	 * If YotpoReviews module is enabled, update _richSnippets reviews
	 *
	 * @return array|null|string
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function _addYotpoAggregateRating()
	{
		if ($this->_checkIfReviewsEnabled()) {
			$_yotpoRichSnippets = Mage::helper('yotpo/richSnippets')->getRichSnippet();

			if (is_array($_yotpoRichSnippets) && count($_yotpoRichSnippets) > 0) {
				if ($_yotpoRichSnippets['average_score'] == 0 || $_yotpoRichSnippets['reviews_count'] == 0) {
					return null;
				}

				return array(
					'@type'       => 'AggregateRating',
					'bestRating'  => 5,
					'worstRating' => 0,
					'ratingValue' => (float) $_yotpoRichSnippets['average_score'],
					'ratingCount' => (float) $_yotpoRichSnippets['reviews_count'],
				);
			} else {
				return null;
			}
		}
		
		return null;
	}
	
	/**
	 * If Stamped Reviews module is enabled, update _richSnippets reviews
	 *
	 * @return array|null|string
	 */
	protected function _addStampedAggregateRating()
	{
		if ($this->_checkIfReviewsEnabled()) {
			$_stampedRichSnippets = Mage::helper('stamped')->getRichSnippetData();
			
			if (is_array($_stampedRichSnippets) && count($_stampedRichSnippets) > 0) {
				if ($_stampedRichSnippets['average_score'] == 0 || $_stampedRichSnippets['reviews_count'] == 0) {
					return null;
				}

				return array(
					'@type'       => 'AggregateRating',
					'bestRating'  => 5,
					'worstRating' => 0,
					'ratingValue' => (float) number_format($_stampedRichSnippets['average_score'], 2),
					'ratingCount' => (float) $_stampedRichSnippets['reviews_count'],
				);
			}
			
			return null;
		}
		
		return null;
	}
	
	/**
	 * Return current product review details
	 *
	 * @param int $productId
	 * @return array|null|string
	 */
	protected function _returnProductRating($productId)
	{
		$productRatings = $this->_addRating($productId);
		
		if ($productRatings != null) {
			if (count($productRatings)) {
				return $productRatings;
			}
		}
		
		return null;
	}
	
	/**
	 * If product has rating, update _richSnippets with detailed rating reviews
	 *
	 * @param $productId
	 * @return array|null|string
	 */
	protected function _addRating($productId)
	{
		if ($this->_checkIfReviewsEnabled()) {
			$reviewData = array();
			$reviews    = $this->_configurableProductModel->getProductReviews($productId);
			
			if (count($reviews) > 0) {
				
				foreach ($reviews as $review) {
					$ratings   = array();
					$ratings[] = $review['percent'];
					
					$avg = array_sum($ratings) / count($ratings);
					$avg = (float) number_format(floor(($avg / 20) * 2) / 2, 2);

					$datePublished = explode(' ', $review['created_at']);

					$reviewData[] = array(
						'@type'         => 'Review',
						'author'        => array(
							'@type' => 'Person',
							'name'  => $this->escapeHtml($review['nickname'])
						),
						'datePublished' => str_replace('/', '-', $datePublished[0]),
						'name'          => $this->escapeHtml($review['title']),
						'reviewBody'    => nl2br($this->escapeHtml($review['detail'])),
						'reviewRating'  => array(
							'@type'       => 'Rating',
							'ratingValue' => $avg
						)
					);
				}
			}
			
			return $reviewData;
		}
		
		return null;
	}
	
	/**
	 * If product has stamped rating, update _richSnippets with detailed rating reviews
	 *
	 * @param $productId
	 * @return array|null|string
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	protected function _addStampedRating($productId)
	{
		if ($this->_checkIfReviewsEnabled()) {
			$reviewData = array();
			$reviews    = $this->_getStampedReviewData($productId);
			
			if (count($reviews['data']) > 0) {
				
				foreach ($reviews['data'] as $review) {
					$reviewData[] = array(
						'@type'         => 'Review',
						'author'        => array(
							'@type' => 'Person',
							'name'  => $this->escapeHtml($review['author'])
						),
						'datePublished' => str_replace('/', '-', $review['reviewDate']),
						'name'          => $this->escapeHtml($review['reviewTitle']),
						'reviewBody'    => nl2br($this->escapeHtml($review['reviewMessage'])),
						'reviewRating'  => array(
							'@type'       => 'Rating',
							'ratingValue' => (float) number_format($review['reviewRating'], 2)
						)
					);
				}
			}
			
			return $reviewData;
		}
		
		return null;
	}
	
	/**
	 * If product has yotpo rating, update _richSnippets with detailed rating reviews
	 *
	 * @param $productId
	 * @return array|null|string
	 */
	protected function _addYotpoRating($productId)
	{
		if ($this->_checkIfReviewsEnabled()) {
			$reviewData = array();
			$reviews    = $this->_getYotpoReviewData($productId);

			if (count($reviews['response']['reviews']) > 0) {
				
				foreach ($reviews['response']['reviews'] as $review) {
					$datePublished = explode('T', $review['created_at']);
					
					$reviewData[] = array(
						'@type'         => 'Review',
						'author'        => array(
							'@type' => 'Person',
							'name'  => $this->escapeHtml($review['user']['display_name'])
						),
						'datePublished' => $datePublished[0],
						'name'          => $this->escapeHtml($review['title']),
						'reviewBody'    => nl2br($this->escapeHtml($review['content'])),
						'reviewRating'  => array(
							'@type'       => 'Rating',
							'ratingValue' => (float) number_format($review['score'], 2)
						)
					);
				}
			}
			
			return $reviewData;
		}

		return null;
	}
	
	/**
	 * Update _richSnippets with current product mapped attributes
	 *
	 * @param Mage_Catalog_Model_Product $product
	 */
	protected function _getProductAttributes($product)
	{
		$productAttributes       = $this->_richSnippetHelper->productInformation();
		$productAttributesValues = ($productAttributes != '') ?
			$this->_richSnippetHelper->getCoreHelper('unserializeArray')->unserialize($productAttributes) : '';
		$productAttributeSetId   = $product->getAttributeSetId();

		if (is_array($productAttributesValues) && count($productAttributesValues) > 0) {
			foreach ($productAttributesValues as $value) {
				if ($productAttributeSetId == $value['attribute_set'] && ($product->getAttributeText($value['attributes']) || $product->getData($value['attributes']))) {
					$this->_richSnippets[$value['name']] = array(
						'name'  => $this->_richSnippetModel->getAttributeValue($product, $value['attributes'])
					);
				}
			}
		}
	}
	
	/**
	 * Return simple product information from configurable product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array|null
	 */
	protected function _getChildProductsCollection($product)
	{
		return $this->_configurableProductModel->getChildProductsCollection($product);
	}
	
	/**
	 * Check if reviews option are enabled
	 *
	 * @return bool
	 */
	private function _checkIfReviewsEnabled()
	{
		return $this->_richSnippetHelper->productReview();
	}
	
	/**
	 * Return array with information from Stamped Reviews Api
	 *
	 * @param int $productId
	 * @return array|null|mixed
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _getStampedReviewData($productId)
	{
        if ($cachedData = Mage::app()->getCache()->load(Blugento_Richsnippets_Helper_Data::STAMPED_REVIEWS_CACHE_ID . '_' . $productId)) {
            $reviewData = unserialize($cachedData);
        } else {
            $apiKey   = $this->_getStampedApiPublicKey();
            $storeUrl = $this->_getStampedApiStoreUrl();
            $path     = 'http://stamped.io/api/widget/reviews?productId='. $productId .'&minRating&storeUrl='. $storeUrl . '&apiKey=' . $apiKey;

            $reviewData = $this->_makeCall($path) ?: null;
            if ($reviewData) {
                try {
                    Mage::app()->getCache()
                        ->save(serialize($reviewData),
                            Blugento_Richsnippets_Helper_Data::STAMPED_REVIEWS_CACHE_ID . '_' . $productId,
                            array(Mage_Core_Block_Abstract::CACHE_GROUP),
                            3600
                        );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
		
		return $reviewData;
	}

	/**
	 * Get Public Api Key from Stamped module
	 *
	 * @return mixed|string
	 */
	private function _getStampedApiPublicKey()
	{
		return Mage::getStoreConfig(self::STAMPED_API_PUBLIC_KEY_CONFIGURATION);
	}
	
	/**
	 * Get Secret Api Key from Stamped module
	 *
	 * @return mixed|string
	 */
	private function _getStampedApiSecretKey()
	{
		return Mage::getStoreConfig(self::STAMPED_API_SECRET_KEY_CONFIGURATION);
	}
	
	/**
	 * Check if both app key and secret exist for Stamped Api
	 *
	 * @return bool
	 */
	private function _isStampedApiConfigured()
	{
		if ($this->_getStampedApiPublicKey() == null || $this->_getStampedApiSecretKey() == null) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Store Url for Api Call in Stamped Reviews
	 *
	 * @return mixed|string
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _getStampedApiStoreUrl()
	{
		$storeUrl = Mage::getStoreConfig(self::STAMPED_API_STORE_URL_CONFIGURATION);
		if (!$storeUrl) {
			$storeUrl = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		}
		
		return $storeUrl;
	}
	
	/**
	 * Return array with information from Yotpo Reviews Api
	 *
	 * @param int $productId
	 * @return array|null|mixed
	 */
	private function _getYotpoReviewData($productId)
	{
        if ($cachedData = Mage::app()->getCache()->load(Blugento_Richsnippets_Helper_Data::YOTPO_REVIEWS_CACHE_ID . '_' . $productId)) {
            $reviewData = unserialize($cachedData);
        } else {
            $apiPublicKey = $this->_getYotpoApiPublicKey();
            $path         = 'https://api.yotpo.com/v1/widget/' . $apiPublicKey . '/products/' . $productId . '/reviews.json';

            $reviewData = $this->_makeCall($path) ?: null;
            if ($reviewData) {
                try {
                    Mage::app()->getCache()
                        ->save(serialize($reviewData),
                            Blugento_Richsnippets_Helper_Data::YOTPO_REVIEWS_CACHE_ID . '_' . $productId,
                            array(Mage_Core_Block_Abstract::CACHE_GROUP),
                            3600
                        );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

		return $reviewData;
	}
	
	/**
	 * Get Public Api Key from Yotpo module
	 *
	 * @return mixed|string
	 */
	private function _getYotpoApiPublicKey()
	{
		return Mage::getStoreConfig(self::YOTPO_API_PUBLIC_KEY_CONFIGURATION);
	}
	
	/**
	 * Get Secret Api Key from Yotpo module
	 *
	 * @return mixed|string
	 */
	private function _getYotpoApiSecretKey()
	{
		return Mage::getStoreConfig(self::YOTPO_API_SECRET_KEY_CONFIGURATION);
	}
	
	/**
	 * Check if both app key and secret exist for Yotpo Api
	 *
	 * @return bool
	 */
	private function _isYotpoApiConfigured()
	{
		if ($this->_getYotpoApiPublicKey() == null || $this->_getYotpoApiSecretKey() == null) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Determine if product price is visible or not
	 *
	 * @return mixed|string
	 */
	private function _showPrice() {
		return $this->_richSnippetHelper->isPriceVisible();
	}
	
	/**
	 * Retrieve information for reviews
	 *
	 * @param string $path
	 * @return array|mixed|null
	 */
	private function _makeCall($path)
	{
		if ($this->_checkIfReviewsEnabled()) {
			try {
				$result = $this->_makeApiCall($path);

				if (in_array($result['code'], array(200, 201))) {
					$results = json_decode($result['response'], true);

					return $results;
				} else {
					throw new Exception('API Key or API Secret is invalid, please check.');
				}
			} catch (Exception $ex) {
				Mage::log('Failed to execute API Get Reviews. Error: ' . $ex);
				return null;
			}
		}
		
		return null;
	}

	/**
	 * Make the Api call
	 *
	 * @param string $path
	 * @return array|mixed|null
	 */
	private function _makeApiCall($path)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $path);

		$resp  = curl_exec($curl);
		$code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return ['code' => $code, 'response' => $resp];
	}
	
	/**
	 * Get super attributes used for configurable products
	 *
	 * @return array
	 */
	private function _supperAttributes()
	{
		$attributes       = array();
		$supperAttributes = $this->_richSnippetHelper->getSupperAttributes();
		$supperValues     = $supperAttributes != '' ?
			$this->_richSnippetHelper->getCoreHelper('unserializeArray')->unserialize($supperAttributes) : '';
		
		if (is_array($supperValues) && count($supperValues) > 0) {
			$attributes = $supperValues;
		}
		
		return $attributes;
	}
}
