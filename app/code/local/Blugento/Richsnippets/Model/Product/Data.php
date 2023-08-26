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

class Blugento_Richsnippets_Model_Product_Data extends Mage_Core_Model_Abstract
{
	/**
	 * Get child products collection from configurable product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array|null
	 */
	public function getChildProductsCollection($product)
	{
		$query = "SELECT `cpsl`.`product_id` FROM `catalog_product_super_link` AS `cpsl`
 			   INNER JOIN `catalog_product_entity` AS `cpe` ON `cpe`.`entity_id` = `cpsl`.`product_id` AND `cpe`.`required_options` = 0
 			   WHERE `cpsl`.`parent_id` = {$product->getId()}
 			   ";
		
		try {
			$productIds      = $this->_getConnection()->fetchAll($query);
			$childrenIds     = array();
			$customerGroupId = Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer()->getGroupId() : 0;
			// TODO: In the future try to adjust the query to take into consideration if it returns empty results per store
			// TODO: Since we use query "method" not model, there is a possibility that not all the information is kept correctly for each store
			$storeId         = Mage::app()->getStore()->getId() == 1 ? 0 : Mage::app()->getStore()->getId();
			$webSiteId       = Mage::app()->getWebsite()->getId();
			
			if (count($productIds) > 0) {
				foreach ($productIds as $row) {
					$childrenIds[] = $row['product_id'];
				}
				
				$childrenIds = implode(',', $childrenIds);
				
				$select      = "SELECT `cpe`.`entity_id` AS `id`, `cisi`.`is_in_stock` AS `is_in_stock`, `cpip`.`final_price` AS `final_price`,
	                             `cpev`.`value` AS `name`, `cpe`.`sku` AS `sku`, `cpevh`.value as `image`, `ea`.`attribute_code` AS `attribute_code`,
	                             `eaov`.`value` AS `attribute_value`, `cpet`.`value` as `description`, `eaoval`.`value` AS `manufacturer`
							 FROM `catalog_product_entity` AS `cpe`
							 INNER JOIN `cataloginventory_stock_item` AS `cisi` ON `cisi`.`product_id` = `cpe`.`entity_id`
							 INNER JOIN `catalog_product_index_price` AS `cpip` ON `cpip`.`entity_id` = `cpe`.`entity_id` AND `cpip`.`customer_group_id` = {$customerGroupId} AND `cpip`.`website_id` = {$webSiteId}
							 INNER JOIN `catalog_product_entity_int` AS `cpei` ON `cpei`.`entity_id` = `cpe`.`entity_id` AND `cpei`.`attribute_id` IN (
	                             SELECT `cpsa`.`attribute_id` FROM `catalog_product_super_attribute` AS `cpsa` WHERE `cpsa`.`product_id` = {$product->getId()}
	                         )
							 INNER JOIN `eav_attribute` AS `ea` ON `ea`.`attribute_id` = `cpei`.`attribute_id`
							 INNER JOIN `eav_attribute_option_value` AS `eaov` ON `eaov`.`option_id` = `cpei`.`value` AND `eaov`.`store_id` = {$storeId}
							 LEFT JOIN `catalog_product_entity_varchar` AS `cpev` ON `cpev`.`entity_id` = `cpe`.`entity_id` AND `cpev`.`attribute_id` = (
								 SELECT `ea`.`attribute_id` FROM `eav_attribute` AS `ea` WHERE `ea`.`entity_type_id` = 4 AND `ea`.`attribute_code` LIKE 'name'
							 ) AND `cpev`.`store_id` = {$storeId}
							 LEFT JOIN `catalog_product_entity_text` AS `cpet` ON `cpet`.`entity_id` = `cpe`.`entity_id` AND `cpet`.`attribute_id` = (
							 	 SELECT `ea`.`attribute_id` FROM `eav_attribute` AS `ea` WHERE `ea`.`entity_type_id` = 4 AND `ea`.`attribute_code` LIKE 'short_description'
							 ) AND `cpet`.`store_id` = {$storeId}
							 LEFT JOIN `catalog_product_entity_varchar` AS `cpevh` ON `cpevh`.`entity_id` = `cpe`.`entity_id` AND `cpevh`.`attribute_id` = (
							 	 SELECT `ea`.`attribute_id` FROM `eav_attribute` AS `ea` WHERE `ea`.`entity_type_id` = 4 AND `ea`.`attribute_code` LIKE 'image'
							 )
						     LEFT JOIN `catalog_product_entity_int` AS `cpeint` ON `cpeint`.`entity_id` = `cpe`.`entity_id` AND `cpeint`.`attribute_id` IN (
	                             SELECT `ea`.`attribute_id` FROM `eav_attribute` AS `ea` WHERE `ea`.`entity_type_id` = 4 AND `ea`.`attribute_code` LIKE 'manufacturer'
	                         )
							 LEFT JOIN `eav_attribute_option_value` AS `eaoval` ON `eaoval`.`option_id` = `cpeint`.`value`
							 WHERE (`cpe`.`entity_id` IN ({$childrenIds}))
							 ";
				
				try {
					return $this->_getConnection()->fetchAll($select);
				} catch (Exception $e) {
					Mage::logException($e);
				}
			}
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	/**
	 * Get Product Categories from Current Product
	 *
	 * @param int $productId
	 * @return string|null
	 */
	public function getProductCategories($productId)
	{
		// TODO: change to app store: `store_id` = {$storeId}
		// $storeId = Mage::app()->getStore()->getId();
		
		$query = "SELECT `ccev`.`value` AS `category_name` FROM `catalog_category_entity_varchar` AS `ccev`
			   WHERE `ccev`.`entity_id` IN (
			   	   SELECT `ccp`.`category_id` FROM `catalog_category_product` AS `ccp` WHERE `ccp`.`product_id` = {$productId}
			   ) AND `ccev`.`attribute_id` = (
			       SELECT `ea`.`attribute_id` FROM `eav_attribute` AS `ea` WHERE `ea`.`entity_type_id` = 3 AND `ea`.`attribute_code` LIKE 'name'
			   ) AND `ccev`.`store_id` = 0
			   ";
		
		try {
			$categories = $this->_getConnection()->fetchAll($query);
			$productCat = array();
			
			foreach ($categories as $category) {
				$productCat[] = $category['category_name'];
			}
			
			return implode(', ', $productCat);
			
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	/**
	 * Get current product reviews details
	 *
	 * @param int $productId
	 * @return array|null
	 */
	public function getProductReviews($productId)
	{
		$statusId = Mage::helper('review')->isEnabled() ? '1,4' : '1';
		
		$query    = "SELECT `review`.*, `rating_vote`.`percent`, `detail`.`detail_id`, `detail`.`title`, `detail`.`detail`,
 						`detail`.`nickname`, `detail`.`customer_id`
	              FROM `review` AS `review`
	              INNER JOIN `rating_option_vote` AS `rating_vote` ON `rating_vote`.`review_id` = `review`.`review_id`
	              INNER JOIN `review_detail` AS `detail` ON `detail`.`review_id` = `review`.`review_id`
	              WHERE `review`.`entity_id` = 1 AND `review`.`entity_pk_value` = {$productId} AND `review`.`status_id` IN ({$statusId})
	              ORDER BY `review`.`created_at` DESC
	              ";
		
		try {
			return $this->_getConnection()->fetchAll($query);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	/**
	 * Get current product reviews summary
	 *
	 * @param int $productId
	 * @return array|null
	 */
	public function getProductReviewsSummary($productId)
	{
		$statusId = Mage::helper('review')->isEnabled() ? '1,4' : '1';
		
		$query = "SELECT `res`.`reviews_count`, `res`.`rating_summary`
 			   FROM `review_entity_summary` AS `res`
 			   LEFT JOIN `review` AS `r` ON `r`.`entity_pk_value` = `res`.`entity_pk_value`
 			   WHERE `res`.`entity_pk_value` = {$productId} AND `r`.`status_id` IN ({$statusId}) AND `res`.`store_id` = " . Mage::app()->getStore()->getId();
		
		try {
			return $this->_getConnection()->fetchAll($query);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	/**
	 * Get attributes used for configurable products
	 *
	 * @return array|null
	 */
	public function supperAttributes()
	{
		$query = "SELECT `ea`.`attribute_code` AS `attribute_code` FROM `eav_attribute` AS `ea`
					WHERE `ea`.`attribute_id` IN (SELECT `cpsa`.`attribute_id` FROM `catalog_product_super_attribute` AS `cpsa`)";
		
		try {
			return $this->_getConnection()->fetchAll($query);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
	}
	
	/**
	 * Return current bundle product high price and low price
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return array
	 */
	public function getBundleProductPrice($product)
	{
		return Mage::getModel('bundle/product_price')->getPrices($product);
	}
	
	/**
	 * Retrieve connection
	 *
	 * @return mixed
	 */
	private function _getConnection()
	{
		return $this->_getResourceConnection()->getConnection('core_read');
	}
	
	/**
	 * Get the resource model
	 *
	 * @return Mage_Core_Model_Resource
	 */
	private function _getResourceConnection()
	{
		/** @var $coreResource Mage_Core_Model_Resource */
		$coreResource = Mage::getSingleton('core/resource');
		
		return $coreResource;
	}
}
