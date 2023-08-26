<?php

class Blugento_ProductsWidget_Model_Bestselling extends Mage_Core_Model_Abstract
{
	/**
	 * Get best selling products from sales order item collection
	 *
	 * @param string $from
	 * @param string $to
	 * @param null|int $customerId
	 * @return array|null
	 */
	public function processBestSellers($from, $to, $customerId = null)
	{
		$query = 'SELECT `order_items`.`product_id` AS product_id, `order_items`.`product_type` AS product_type, `order_items`.`sku` AS sku, sum(`order_items`.`qty_ordered`) AS qty_sold
			FROM `sales_flat_order_item` `order_items`
			LEFT JOIN `sales_flat_order` `order` ON `order`.`entity_id` = `order_items`.`order_id` AND (`order`.`created_at` BETWEEN "' . $from . '" AND "' . $to . '")
			LEFT JOIN `catalog_product_entity` `e` ON `e`.`entity_id` = `order_items`.`product_id`  AND `e`.`entity_type_id` = 4
			WHERE `e`.`type_id` <> "fontcolor"';
		
		if ($customerId) {
			$query .= ' AND `order`.`customer_id` = ' . $customerId;
		}
		
		$query .= ' GROUP BY product_id, product_type, sku ORDER BY qty_sold DESC';
		
		try {
			return $this->_getConnection()->fetchAll($query);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		
		return null;
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
