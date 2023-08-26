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
 * @package     Blugento_ErpProcess
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ErpProcess_Model_Process extends Mage_Core_Model_Abstract
{
    private $_order = null;

    /** Default product values */
    protected $_type           = 'simple';
    protected $_attributeSetId = 4; // Default
    protected $_status         = 2; // Disabled
    protected $_entityTypeId   = 4; // catalog_product
    protected $_visibility     = 4; // Catalog, search
    protected $_taxClassId     = 1; // Standard (19%)
    protected $_websiteId      = 1; // base
    protected $_stockId        = 1; // Default


    /**
     *  Update product price
     *
     * @param Varien_Data_Collection $items
     * @return Varien_Object
     */
    public function updateProductPrice($items)
    {
        /* @var Blugento_ErpProcess_Helper_Data $helper */
        $helper = Mage::helper('blugento_erpprocess');

        $response = new Varien_Object();
        $msrpEnabled = false;
        $specialEnabled = false;

        $errors    = array();
        $spErrors    = array();
        $msrpErrors    = array();
        $skus      = array();
        $msrpSkus      = array();
        $specialSkus = array();
        $skuPrice  = array();
        $skuSpPrice  = array();
        $skuMsrp   = array();
        $updatedPrices = array();
        $updatedSpPrices = array();
        $updatedMsrp = array();
        $remainingIds = array();

        $priceSql = 'SELECT attribute_id FROM eav_attribute WHERE attribute_code LIKE "price";';
        $msrpSql = 'SELECT attribute_id FROM eav_attribute WHERE attribute_code LIKE "msrp";';

        $priceAttributeId = $this->_getReadConnection()->fetchOne($priceSql);
        $spPriceAttributeId = $this->_getReadConnection()->fetchOne('SELECT attribute_id FROM eav_attribute WHERE attribute_code LIKE "special_price";');
        $msrpAttributeId = $this->_getReadConnection()->fetchOne($msrpSql);

        try {
            foreach($items as $item) {
                $sku   = $item->getSku();
                $price = $item->getPrice();

                array_push($skus, $sku);
                $skuPrice[$sku] = $price;

                $specialPrice = $item->getSpecialPrice();
                if ($specialPrice !== null || $specialPrice !== '') {
                    array_push($specialSkus, $sku);
                    $skuSpPrice[$sku] = $item->getSpecialPrice();
                    $specialEnabled = true;
                }

                if ($msrp = $item->getMsrp()) {
                    array_push($msrpSkus, $sku);
                    $skuMsrp[$sku] = $msrp;
                    $msrpEnabled = true;
                }
            }

            if (count($skus)) {
                $sql_price = 'SELECT e.entity_id AS id, e.sku AS sku, d.attribute_id AS attribute_id, d.value AS value
                        FROM catalog_product_entity e
                        JOIN catalog_product_entity_decimal d
                        ON e.entity_id = d.entity_id
                        WHERE d.attribute_id IN (' . $priceAttributeId . ')
                        AND (e.sku LIKE "' . $skus[0] . '"';

                unset($skus[0]);

                foreach ($skus as $sk) {
                    $sql_price .= ' OR e.sku LIKE "' . $sk . '"';
                }
                $sql_price .= ')';

                $result_price = $this->_getReadConnection()->fetchAll($sql_price);

                if (count($result_price)) {
                    foreach ($result_price as $item) {
                        $itemSku = trim($item['sku']);
                        if ($item['value'] != $skuPrice[$itemSku]) {
                            $sql = 'UPDATE catalog_product_entity_decimal 
                                        SET value = ' . $skuPrice[$itemSku] . '
                                        WHERE entity_id = ' . $item['id'] . '
                                        AND attribute_id = ' . $priceAttributeId;

                            try {
                                $this->_getWriteConnection()->query($sql);
                                $updatedPrices[] = $itemSku;
                            } catch (Exception $e) {
                                $errors[] = $itemSku;
                                Mage::logException($e);
                            }
                        }
                    }
                }

                if ($specialEnabled) {
                    $sql_spprice = 'SELECT e.entity_id AS id, e.sku AS sku, sd.attribute_id AS attribute_id, sd.value AS value
                        FROM catalog_product_entity e
                        JOIN catalog_product_entity_decimal sd
                        ON e.entity_id = sd.entity_id
                        WHERE sd.attribute_id IN (' . $spPriceAttributeId . ')
                        AND (e.sku LIKE "' . $specialSkus[0] . '"';

                    unset($specialSkus[0]);

                    foreach ($specialSkus as $specialSku) {
                        $sql_spprice .= ' OR e.sku LIKE "' . $specialSku . '"';
                    }
                    $sql_spprice .= ')';

                    $result_special = $this->_getReadConnection()->fetchAll($sql_spprice);

                    if (count($result_special)) {
                        foreach ($result_special as $spKey => $spItem) {
                            $itemSku = trim($spItem['sku']);

                            $sql = '';
                            if ($skuSpPrice[$itemSku] === 0) {
                                $sql = 'DELETE FROM catalog_product_entity_decimal
                                        WHERE entity_id = ' . $spItem['id'] . ' 
                                        AND attribute_id = ' . $spPriceAttributeId;
                            } else if ($spItem['value'] != $skuSpPrice[$itemSku]) {
                                $sql = 'UPDATE catalog_product_entity_decimal
                                        SET value = ' . $skuSpPrice[$itemSku] . '
                                        WHERE entity_id = ' . $spItem['id'] . '
                                        AND attribute_id = ' . $spPriceAttributeId;
                            }

                            if ($sql != '') {
                                try {
                                    $this->_getWriteConnection()->query($sql);
                                    $updatedSpPrices[] = $itemSku;
                                } catch (Exception $e) {
                                    $spErrors[] = $itemSku;
                                    Mage::logException($e);
                                }
                            }
                            unset($skuSpPrice[$itemSku]);
                        }
                    }

                    $remainingSkus = array_keys($skuSpPrice);

                    $remaining_sql = 'SELECT sku, entity_id AS id
                            FROM catalog_product_entity
                            WHERE sku IN ("' . implode('", "', $remainingSkus) . '")';

                    try {
                        $remainingIds = $this->_getWriteConnection()->fetchPairs($remaining_sql);
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }

                    if (count($remainingIds)) {
                        foreach ($remainingIds as $rSku => $rId) {
                            if ($skuSpPrice[$rSku] > 0) {
                                $sql_insert = 'INSERT INTO catalog_product_entity_decimal (entity_type_id, attribute_id, store_id, entity_id, value)
                                    VALUES (4, ' . $spPriceAttributeId . ', 0, ' . $rId . ', ' . $skuSpPrice[$rSku] . ')';
                                try {
                                    $this->_getWriteConnection()->query($sql_insert);
                                    $updatedSpPrices[] = $rSku;
                                } catch (Exception $e) {
                                    $spErrors[] = $rSku;
                                    Mage::logException($e);
                                }
                            }
                        }
                    }
                }

                if ($msrpEnabled) {
                    $sql_msrp = 'SELECT e.entity_id AS id, e.sku AS sku, d.attribute_id AS attribute_id, d.value AS value
                        FROM catalog_product_entity e
                        JOIN catalog_product_entity_decimal d
                        ON e.entity_id = d.entity_id
                        WHERE d.attribute_id IN (' . $msrpAttributeId . ')
                        AND (e.sku LIKE "' . $msrpSkus[0] . '"';

                    unset($msrpSkus[0]);

                    foreach ($msrpSkus as $sk) {
                        $sql_msrp .= ' OR e.sku LIKE "' . $sk . '"';
                    }
                    $sql_msrp .= ')';

                    $result_msrp = $this->_getReadConnection()->fetchAll($sql_msrp);

                    if (count($result_msrp)) {
                        foreach ($result_msrp as $item) {
                            if ($item['value'] != $skuMsrp[trim($item['sku'])]) {
                                $sql = 'UPDATE catalog_product_entity_decimal
                                        SET value = ' . $skuMsrp[trim($item['sku'])] . '
                                        WHERE entity_id = ' . $item['id'] . '
                                        AND attribute_id = ' . $msrpAttributeId;

                                try {
                                    $this->_getWriteConnection()->query($sql);
                                    $updatedMsrp[] = $item['sku'];
                                } catch (Exception $e) {
                                    $msrpErrors[] = $item['sku'];
                                    Mage::logException($e);
                                }
                            }
                        }
                    }
                }

                if (count($updatedPrices) > 0 || count($updatedMsrp) > 0 || count($updatedSpPrices) > 0) {
                    $helper->_reindexData('catalog_product_price');
                }
            } else {
                $errors[] = 'There is no data returned from ERP.';
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $response->setErrors($errors);
        $response->setSpecialPriceErrors($spErrors);
        $response->setMsrpErrors($msrpErrors);
        $response->setUpdatedPrice($updatedPrices);
        $response->setUpdatedSpecialPrice($updatedSpPrices);
        $response->setUpdatedMsrp($updatedMsrp);

        return $response;
    }

    /**
     *  Update product stock
     *
     * @param Varien_Data_Collection $items
     * @return Varien_Object
     */
    public function updateProductStock($items)
    {
        /* @var Blugento_ErpProcess_Helper_Data $helper */
        $helper = Mage::helper('blugento_erpprocess');

        $response = new Varien_Object();

        $errors    = array();
        $skus      = array();
        $skuQty    = array();
        $updated   = array();
        try {
            foreach($items as $item) {
                $sku = $item->getSku();
                $qty = $item->getQty();

                array_push($skus, $sku);
                $skuQty[$sku] = $qty;
            }

            if (count($skus)) {
                $updated = array();
                $partialskus = array_chunk($skus, 100);
                foreach ($partialskus as $partialsk) {
                    $sql = 'SELECT e.entity_id AS id, e.sku AS sku, q.qty AS qty, q.backorders, q.use_config_backorders
                        FROM catalog_product_entity e 
                        JOIN cataloginventory_stock_item q
                        ON e.entity_id = q.product_id 
                        WHERE e.sku LIKE ' . '\'' .$skus[0] . '\'';
                    foreach ($partialsk as $sk) {
                        $sql .= ' OR e.sku LIKE ' . '\'' . $sk . '\'';
                    }
                    $result = $this->_getReadConnection()->fetchAll($sql);

                    if (count($result)) {
                        foreach ($result as $item) {
                            if ($item['qty'] != $skuQty[trim($item['sku'])]) {
                                $tableName = $this->_getResourceConnection()->getTableName('cataloginventory_stock_item');
                                $isInStock = $this->checkIsInStock($skuQty, $item);

                                $sql = ' UPDATE  ' . $tableName . ' SET ';
                                $sql .= ' qty  = ' . $skuQty[trim($item['sku'])];
                                $sql .= ' , is_in_stock  = ' . $isInStock;
                                $sql .= '  WHERE product_id = ' . $item['id'];

                                try {
                                    $this->_getWriteConnection()->query($sql);
                                    $updated[] = $item['sku'];
                                } catch (Exception $e) {
                                    $errors[] = $item['sku'];
                                    Mage::logException($e);
                                }
                            }
                        }
                    }
                }
                if (count($updated) > 0) {
                    $helper->_reindexData('cataloginventory_stock');
                }
            } else {
                $errors[] = 'There is no data returned from ERP.';
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $response->setErrors($errors);
        $response->setUpdatedStock($updated);

        return $response;
    }

    /**
     *  Update orders
     *
     * @param Varien_Data_Collection $orders
     * @return Varien_Object
     */
    public function updateOrder($orders)
    {
        $response = new Varien_Object();
        $processed = array();

        try {
            $ordersArr = array();
            foreach ($orders as $order) {
                $ordersArr[$order->getId()]['increment'] = $order->getIncrement();
                $ordersArr[$order->getId()]['status'] = strtolower($order->getStatus());
                $ordersArr[$order->getId()]['awb'] = $order->getAwbNumber();
            }

            $orderIds = array_keys($ordersArr);
            $tableName = $this->_getResourceConnection()->getTableName('sales_flat_order');

            $sql = 'SELECT entity_id AS id, awb_number AS awb, status
                    FROM ' . $tableName . '
                    WHERE entity_id IN (' . implode(",", $orderIds) . ')
            ';

            $dbOrders = $this->_getReadConnection()->fetchAll($sql);

            foreach ($dbOrders as $dbOrder) {
                if ($this->_compareOrders($dbOrder, $ordersArr[$dbOrder['id']])) {
                    $status = $ordersArr[$dbOrder['id']]['status'];
                    $awb = $ordersArr[$dbOrder['id']]['awb'];
                    $increment = $ordersArr[$dbOrder['id']]['increment'];

                    $updateSql = 'UPDATE ' . $tableName . ' SET ';

                    if ($this->_compareStatus($dbOrder['status'], $status)) {
                        $updateSql .= 'status = "' . $status . '", ';

                        $gridTable = $this->_getResourceConnection()->getTableName('sales_flat_order_grid');

                        $gridSql = 'UPDATE ' . $gridTable . ' 
                                    SET status = "' . $status . '" 
                                    WHERE `entity_id` = ' . $dbOrder['id'];

                        try {
                            $this->_getWriteConnection()->query($gridSql);
                        } catch (Exception $e) {
                            Mage::logException($e);
                        }
                    }

                    if ($awb) {
                        $updateSql .= 'awb_number = "' . $awb . '" ';
                    }

                    $updateSql .= 'WHERE `entity_id` = ' . $dbOrder['id'];
                    $updateSql = str_replace(', WHERE',' WHERE', $updateSql);

                    try {
                        $this->_getWriteConnection()->query($updateSql);
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }

                    $this->_processOrder($status, $dbOrder['id']);
                    $processed[] = $increment ? $increment : $dbOrder['id'];
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $response->setProcessedOrders($processed);

        return $response;
    }

    /**
     * Update order status
     * 
     * @param string $incrementId
     */
    public function updateOrderStatus($incrementId)
    {
        if ($incrementId) {
            try {
                $order = Mage::getModel('sales/order')->load($incrementId, 'increment_id');

                if ($order->getStatus() == 'pending') {
                    $order->setStatus('processing');
                    $order->setState('processing');
                    $order->save();
                } else if ($order->getStatus() == 'processing') {
                    $order->setStatus('complete');
                    $order->setState('complete');
                    $order->save();
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Get new orders that are not integrated yet.
     *
     * @param string $table
     * @param string $key
     * @param string $select
     * @param string $status
     * @return Varien_Object|null
     */
    public function getNewOrders($table, $key, $select, $status = 0)
    {
        $sql = 'SELECT ' . $select . '
                FROM ' . $table . ' erp
                INNER JOIN sales_flat_order so
                ON erp.' . $key .' = so.entity_id
                LEFT JOIN sales_flat_order_address soa
                ON erp.' . $key .' = soa.parent_id
                LEFT JOIN sales_flat_order_payment sop
                ON erp.' . $key .' = sop.parent_id
                WHERE erp.integration_status = ' . $status . ' AND soa.address_type = "shipping" AND erp.created_at < NOW() - INTERVAL 5 MINUTE;';

        $sqlForVirtualOrders = 'SELECT ' . $select . '
                FROM ' . $table . ' erp
                INNER JOIN sales_flat_order so
                ON erp.' . $key .' = so.entity_id AND so.is_virtual = 1
                LEFT JOIN sales_flat_order_address soa
                ON erp.' . $key .' = soa.parent_id
                LEFT JOIN sales_flat_order_payment sop
                ON erp.' . $key .' = sop.parent_id
                WHERE erp.integration_status = ' . $status . ' AND erp.created_at < NOW() - INTERVAL 5 MINUTE;';

        $orders = new Varien_Object();

        try {
            $result = $this->_getReadConnection()->fetchAll($sql);
            $virtualOrdersResult = $this->_getReadConnection()->fetchAll($sqlForVirtualOrders);

            if (count($result)) {
                $helper = Mage::helper('blugento_erpprocess');
                $itemSelect = $helper->getItemSelect();

                foreach ($result as $key => $order) {
                    $result[$key]['items'] = $this->_getOrderItems($order['order_id'], $itemSelect);
                }
            } else {
                $result = null;
            }

            if (count($virtualOrdersResult)) {
                $helper = Mage::helper('blugento_erpprocess');
                $itemSelect = $helper->getItemSelect();

                foreach ($virtualOrdersResult as $key => $order) {
                    $virtualOrdersResult[$key]['items'] = $this->_getOrderItems($order['order_id'], $itemSelect);
                }
            } else {
                $virtualOrdersResult = null;
            }
            if ($virtualOrdersResult) {
                $finalResult = array_merge($result, $virtualOrdersResult);
            } else {
                $finalResult = $result;
            }

        } catch (Exception $e) {
            Mage::logException($e);
            $finalResult = null;
        }

        return $orders->setOrders($finalResult);
    }

    /**
     * Return orders increment id by order statuses
     *
     * @param array $statuses
     * @return Varien_Object|null
     */
    public function getOrdersByStatus($statuses)
    {
        $sts = '"' . implode('", "', $statuses) . '"';

        $sql = 'SELECT entity_id, increment_id 
                FROM sales_flat_order
                WHERE status IN (' . $sts . ')';

        try {
            $result = $this->_getReadConnection()->fetchAll($sql);

            $orders = new Varien_Object();

            return $orders->setIncrements($result);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Create products.
     *
     * @param Varien_Data_Collection $products
     * @return Varien_Object
     */
    public function createProduct($products)
    {
        $created = array();
        $existing = array();
        $errors = array();

        if (count($products)) {
            $existingSkus = $this->_getAllSkus();

            try {
                foreach ($products as $product) {
                    $sku = $product->getSku();

                    if (!in_array($sku, $existingSkus)) {
                        $name = $product->getName();
                        $price = $product->getPrice();
                        $specialPrice = $product->getSpecialPrice();
                        $qty = $product->getQty();

                        $attributesId = $this->_getAttributesId();

                        $entityId = $this->_createBaseProduct($sku);
                        $this->_addProductAttributeValues($entityId, $name, $attributesId['name'], 'varchar');
                        $this->_addProductAttributeValues($entityId, $this->_status, $attributesId['status'], 'int');
                        $this->_addProductAttributeValues($entityId, $this->_visibility, $attributesId['visibility'], 'int');
                        $this->_addProductAttributeValues($entityId, $this->_taxClassId, $attributesId['tax_class_id'], 'int');
                        $this->_addProductAttributeValues($entityId, $price, $attributesId['price'], 'decimal');

                        if ($specialPrice) {
                            $this->_addProductAttributeValues($entityId, $specialPrice, $attributesId['special_price'], 'decimal');
                        }

                        $this->_setProductStock($entityId, $qty);
                        $this->_setProductWebsite($entityId);

                        $created[] = $sku;
                    } else {
                        $existing[] = $sku;
                    }
                }

            } catch (Exception $e) {
                Mage::logException($e);
            }
        } else {
            $errors[] = 'There is no data returned from ERP.';
        }

        $response = new Varien_Object();
        $response->setErrors($errors);
        $response->setCreatedProducts($created);
        $response->setExistingProducts($existing);

        return $response;
    }

    public function updateOrdersFromErp($orders)
    {
        $website = Mage::app()->getWebsite();

        $updated = array();
        $not = array();
        foreach ($orders as $k => $order) {
            /* @var Mage_Sales_Model_Order $oldOrder */
            $oldOrder = Mage::getModel('sales/order')->load($order['order_id']);

            if ($oldItems = $oldOrder->getAllItems()) {
                foreach ($oldItems as $itm) {
                    $itm->delete();
                }
            }

            $oldOrder->setBaseGrandTotal(0);
            $oldOrder->setBaseSubtotal(0);
            $oldOrder->setBaseTaxAmount(0);
            $oldOrder->setGrandTotal(0);
            $oldOrder->setSubtotal(0);
            $oldOrder->setTaxAmount(0);
            $oldOrder->setBaseSubtotalInclTax(0);
            $oldOrder->setSubtotalInclTax(0);
            $oldOrder->setTotalItemCount(0);

            $oldOrder->save();

            /* @var Mage_Sales_Model_Convert_Order $orderConvert */
            $orderConvert = Mage::getModel('sales/convert_order');

            $quote = $orderConvert
                ->toQuote($oldOrder)
                ->setIsActive(false)
                ->setReservedOrderId($oldOrder->getIncrementId())
                ->save();

            /* @var Mage_Customer_Model_Customer $quote */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getId())
                ->loadByEmail($order['email']);

            //Assign the customer to quote
            if ($customer->getId()) {
                $quote->assignCustomer($customer);
            }

            //Add products to quote
            foreach ($order['items'] as $item) {
                if ($productId = $this->_getProductIdBySku($item['sku'])) {
                    $product = Mage::getModel('catalog/product')->load($productId);

                    $product->setPrice($item['price']);

                    if ($product->getSpecialPrice()) {
                        $product->setSpecialPrice($item['price']);
                    }

                    $quote->addProduct($product, $item['qty']);
                }
            }

            //Add billing address to quote
            $quote->getBillingAddress()->addData($order['billing_address']);

            //Add shipping address to quote
            $shippingAddressData = $quote->getShippingAddress()->addData($order['shipping_address']);

            //Collect shipping rates on quote
            $shippingAddressData->setCollectShippingRates(true)->collectShippingRates();

            //Set shipping method and payment method on the quote
            $shippingAddressData->setShippingMethod($order['shipping_method'])
                ->setPaymentMethod($order['payment_method']);

            //Set payment method for the quote
            $quote->getPayment()->importData(array('method' => $order['payment_method']));

            try {
                //Collect totals & save quote
                $quote->collectTotals()->save();

                $quoteItems    = $quote->getAllItems();
                $subtotal      = 0;
                $baseSubtotal  = 0;
                $taxAmount     = 0;
                $baseTaxAmount = 0;
                foreach ($quoteItems as $key => $quoteItem) {
                    $orderItem = Mage::getModel('sales/convert_quote')->itemToOrderItem($quoteItem);

                    $subtotal      = $subtotal + $quoteItem->getRowTotalInclTax();
                    $baseSubtotal  = $baseSubtotal + $quoteItem->getBaseRowTotal();
                    $taxAmount     = $taxAmount + $quoteItem->getTaxAmount();
                    $baseTaxAmount = $baseTaxAmount + $quoteItem->getBaseTaxAmount();

                    $quoteItems[$key] = $orderItem->setOrderID($oldOrder->getId());
                    $quoteItems[$key]->save();
                }

                $createdOrder = Mage::getModel('sales/convert_quote')->toOrder($quote, $oldOrder);

                // Set shipping amount
                $createdOrder->setShippingAmount($order['shipping_amount'] - $order['shipping_tax']);
                $createdOrder->setShippingInclTax($order['shipping_amount']);
                $createdOrder->setBaseShippingAmount($order['shipping_amount'] - $order['shipping_tax']);
                $createdOrder->setBaseInclShippingAmount($order['shipping_amount']);

                // Set order amount
                $createdOrder->setData("tax_amount", $taxAmount + $order['shipping_tax']);
                $createdOrder->setData("base_tax_amount", $baseTaxAmount);
                $createdOrder->setData("subtotal", $baseSubtotal);
                $createdOrder->setData("base_subtotal", $baseSubtotal);

                $createdOrder->setGrandTotal($subtotal + $order['shipping_amount']);
                $createdOrder->setBaseGrandTotal($subtotal + $order['shipping_amount']);

                // Set order status
                $createdOrder->setData('status', $order['status']);

                $createdOrder->save();

                $updated[] = $createdOrder->getIncrementId();
            } catch (Exception $e) {
                Mage::logException($e);
                $not[] = $oldOrder->getIncrementId();
            }
        }

        $response = new Varien_Object();
        $response->setUpdated($updated);
        $response->setNotUpdated($not);

        return $response;
    }

    /**
     * Check if order exists
     *
     * @param string $incrementId
     * @return int|bool|null
     */
    public function existOrder($incrementId)
    {
        $sql = "SELECT entity_id FROM sales_flat_order WHERE increment_id LIKE '$incrementId'";

        try {
            return $this->_getReadConnection()->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Get product id by product sku
     *
     * @param string $sku
     * @return int|null
     */
    private function _getProductIdBySku($sku)
    {
        $sql = "SELECT entity_id FROM catalog_product_entity WHERE sku LIKE '$sku'";

        try {
            return $this->_getReadConnection()->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Store invoice
     *
     * @param $invoices
     * @param $config
     */
    public function storeInvoice($invoices, $config)
    {
        if ($orderIds = $this->_ordersHaveInvoiceUrl($invoices, $config)) {
            $ids = array();
            $increments = array();
            foreach ($orderIds as $id) {
                $increments[$id['entity_id']] = $id['increment_id'];
                $ids[$id['increment_id']] = $id['entity_id'];
            }

            foreach ($invoices as $invoice) {
                $invoiceOrder = $invoice->getId();

                if ($config->getOrderType() == 'entity') {
                    if (!in_array($invoiceOrder, $ids)) {
                        continue;
                    }
                    $orderId = $invoiceOrder;
                }

                if ($config->getOrderType() == 'increment') {
                    if (!in_array($invoiceOrder, $increments)) {
                        continue;
                    }
                    $orderId = $ids[$invoiceOrder];
                }

                if ($config->getType() == 'remote') {
                    $url = $invoice->getDownloadUrl();
                    if ($url) {
                        $this->_saveRemoteInvoice($orderId, $url);
                    }
                } elseif ($config->getType() == 'content') {
                    $content = $invoice->getContent();
                    $this->_saveContentInvoice($orderId, $content, $config->getExtension());
                } elseif ($config->getType() == 'ftp') {
                    $filename = $invoice->getFilename();
                    $this->_saveFtpInvoice($orderId, $filename, $config->getFtpConfig(), $config->getExtension());
                }
            }
        }
    }

    /**
     * @param string $orderNo
     * @param string $invoiceNo
     */
    public function createInvoiceForOrder($orderNo, $invoiceNo)
    {
        // TODO set custom invoice number
        $this->_createInvoice($orderNo);
    }

    /**
     * Save remote invoice from url
     *
     * @param int $id
     * @param string $url
     */
    private function _saveRemoteInvoice($id, $url)
    {
        try {
            $dir = Mage::getBaseDir('media') . DS . 'invoices';

            $filename = explode('/', $url);
            $filename = end($filename);
            $destination = $dir . DS . $filename;

            if (!file_exists($destination)) {
                if (!file_exists($dir)) {
                    mkdir($dir);
                }

                $file = file($url);
                file_put_contents($destination, $file);
            }

            $this->_saveInvoiceUrl($filename, $id);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Save invoice content
     *
     * @param int $id
     * @param string $content
     * @param string $ext
     */
    private function _saveContentInvoice($id, $content, $ext)
    {
        try {
            $dir = Mage::getBaseDir('media') . DS . 'invoices';

            $filename = $id . '-' . time() . '.' . $ext;
            $destination = $dir . DS . $filename;

            if (!file_exists($destination)) {
                if (!file_exists($dir)) {
                    mkdir($dir);
                }

                file_put_contents($destination, $content);
            }

            $this->_saveInvoiceUrl($filename, $id);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Save invoice from a ftp server
     *
     * @param $id
     * @param $ftpInvoice
     * @param $ftp
     * @param $extension
     */
    private function _saveFtpInvoice($id, $ftpInvoice, $ftp, $extension)
    {
        try {
            $dir = Mage::getBaseDir('media') . DS . 'invoices';
            $filename = $id . '-' . time() . '.' . $extension;
            $destination = $dir . DS . $filename;
            $ftpPath = $ftp['ftp_path'] ? $ftp['ftp_path'] . DS . $ftpInvoice : $ftpInvoice;

            $connection = ftp_connect($ftp['ftp_ip']);
            ftp_login($connection, $ftp['ftp_user'], $ftp['ftp_pass']);

            if (ftp_get($connection, $destination, $ftpPath, FTP_BINARY)) {
                $this->_saveInvoiceUrl($filename, $id);
            }

            ftp_close($connection);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return all the orders that does not have invoice URL by ids
     *
     * @param $invoices
     * @param $config
     * @return |null
     */
    private function _ordersHaveInvoiceUrl($invoices, $config)
    {
        $orders = array();
        foreach ($invoices as $invoice) {
            $orders[] = $invoice->getId();
        }

        if ($config->getOrderType() == 'increment') {
            $where = '"' . implode('","', $orders) . '"';

            $sql = "SELECT entity_id, increment_id
                FROM sales_flat_order
                WHERE increment_id IN ($where)
                AND (invoice_download_url IS NULL 
                OR invoice_download_url = '')";
        } else {
            $sql = "SELECT entity_id, increment_id
                FROM sales_flat_order
                WHERE entity_id IN (" . implode(',', $orders) . ")
                AND (invoice_download_url IS NULL 
                OR invoice_download_url = '')";
        }

        try {
            $result = $this->_getReadConnection()->fetchAll($sql);

            if (count($result) > 0) {
                return $result;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }

    /**
     * Save invoice url in database
     *
     * @param string $filename
     * @param int $orderId
     */
    private function _saveInvoiceUrl($filename, $orderId)
    {
        $url = 'invoices' . DS . $filename;

        $sql = 'UPDATE sales_flat_order
                SET invoice_download_url = "' . $url . '"
                WHERE entity_id = ' . $orderId;

        try {
            $this->_getWriteConnection()->query($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @param array $dbOrder
     * @param array $erpOrder
     * @return bool
     */
    private function _compareOrders($dbOrder, $erpOrder)
    {
        $valid = false;

        if ($this->_compareStatus($dbOrder['status'], $erpOrder['status'])) {
            $valid = true;
        }

        if (isset($erpOrder['awb']) && $erpOrder['awb'] && $dbOrder['awb'] != $erpOrder['awb']) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * @param string $dbStatus
     * @param string $erpStatus
     * @return bool
     */
    private function _compareStatus($dbStatus, $erpStatus)
    {
        if ($dbStatus != $erpStatus) {
            return true;
        }

        return false;
    }

    /**
     * Return all items from an order.
     *
     * @param int $orderId
     * @param string $select
     * @return mixed
     */
    private function _getOrderItems($orderId, $select)
    {
        $sql = 'SELECT ' . $select . '
                FROM sales_flat_order_item
                WHERE order_id = ' . $orderId;

        return $this->_getReadConnection()->fetchAll($sql);
    }

    /**
     * Process order based on ERP status
     *
     * @param string $status
     * @param string $id
     * @return null
     */
    private function _processOrder($status, $id)
    {
        try {
            if ($status == 'processing') {
                $this->_createInvoice($id);
            } else if ($status = 'complete') {
                $this->_createInvoice($id);
                $this->_createShipment($id);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Generate order invoice
     *
     * @param string $id
     * @return bool
     */
    private function _createInvoice($id)
    {
        if (!$this->_order) {
            /* @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($id, 'increment_id');
            $this->_order = $order;
        }

        $order = $this->_order;

        if ($order->canInvoice()) {
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();

            if (Mage::getStoreConfig('blugento_erpintegration/general/send_pdf')) {
                $invoice->getOrder()->setCustomerNoteNotify(true);
                $invoice->getOrder()->setIsInProcess(true);
                $invoice->sendEmail();
            }

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
        }
    }

    /**
     * Generate order shipment
     *
     * @param int $id
     */
    private function _createShipment($id)
    {
        $order = Mage::getModel('sales/order')->load($id, 'increment_id');

        /*
         * Create Qty array
         */
        $shipmentItems = array();
        foreach ($order->getAllItems() as $item) {
            $shipmentItems [$item->getId()] = $item->getQtyToShip();
        }

        /**
         * Prepare shipment and save
         */
        if ($order->getId() && !empty($shipmentItems) && $order->canShip()) {
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($shipmentItems);
            $shipment->save();
        }

        $this->_order = null;
    }

    /**
     * Return existing products sku.
     *
     * @return array
     */
    private function _getAllSkus()
    {
        $skuArray = array();

        try {
            $tableName = $this->_getResourceConnection()->getTableName('catalog_product_entity');
            $sql = "SELECT sku FROM " . $tableName;

            $result = $this->_getReadConnection()->fetchAll($sql);

            foreach ($result as $sku) {
                $skuArray[] = $sku['sku'];
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $skuArray;
    }

    /**
     * Return attributes_id by attribute code.
     *
     * @return array
     */
    private function _getAttributesId()
    {
        $attributes = array('name', 'price', 'special_price', 'status', 'tax_class_id', 'visibility');
        $attributesStr = implode('", "', $attributes);

        $attributesId = array();

        $tableName = $this->_getResourceConnection()->getTableName('eav_attribute');
        $sql = 'SELECT attribute_id, attribute_code
                FROM ' . $tableName . '
                WHERE attribute_code IN ("' . $attributesStr . '")';

        $result = $this->_getReadConnection()->fetchAll($sql);

        foreach ($result as $data) {
            $attributesId[$data['attribute_code']] = $data['attribute_id'];
        }

        return $attributesId;
    }

    /**
     * Create base simple product in 'catalog_product_entity';
     *
     * @param string $sku
     * @return int|null
     */
    private function _createBaseProduct($sku)
    {
        $createdAt = date('Y-m-d H:m:s');
        $tableName = $this->_getResourceConnection()->getTableName('catalog_product_entity');

        $sql = 'INSERT INTO ' . $tableName . ' (entity_type_id, attribute_set_id, type_id, sku, created_at)
                VALUES (' . $this->_entityTypeId . ', ' . $this->_attributeSetId . ', "' . $this->_type . '", "' . $sku . '", "' . $createdAt . '")';

        $this->_getWriteConnection()->query($sql);

        return $this->_getReadConnection()->fetchOne('SELECT last_insert_id()');
    }

    /**
     * Add product attribute values type varchar, int, decimal...
     *
     * @param int $entityId
     * @param string $value
     * @param string $attributeId
     * @param string $tableType
     */
    private function _addProductAttributeValues($entityId, $value, $attributeId, $tableType)
    {
        $tableName = $this->_getResourceConnection()->getTableName('catalog_product_entity_' . $tableType);

        $value = addslashes($value);

        $sql = 'INSERT INTO ' . $tableName . ' (entity_type_id, attribute_id, store_id, entity_id, value)
                VALUES (' . $this->_entityTypeId . ', ' . $attributeId . ', 0, ' . $entityId . ', "' . $value . '")';

        $this->_getWriteConnection()->query($sql);
    }

    /**
     * Add product stock.
     *
     * @param int $entityId
     * @param int $qty
     */
    private function _setProductStock($entityId, $qty)
    {
        $isInStock = $qty ? 1 : 0;

        $tableName = $this->_getResourceConnection()->getTableName('cataloginventory_stock_item');
        $sql = 'INSERT INTO ' . $tableName . ' (product_id, stock_id, qty, is_in_stock, manage_stock)
                VALUES (' . $entityId . ', ' . $this->_stockId . ', ' . $qty . ', ' . $isInStock . ', 0)';

        $this->_getWriteConnection()->query($sql);

        $tableName = $this->_getResourceConnection()->getTableName('cataloginventory_stock_status');
        $sql = 'INSERT INTO ' . $tableName . ' (product_id, website_id, stock_id, qty, stock_status)
                VALUES (' . $entityId . ', ' . $this->_websiteId . ', ' . $this->_stockId . ', ' . $qty . ', ' . $isInStock . ')';

        $this->_getWriteConnection()->query($sql);
    }

    /**
     * Set product website.
     *
     * @param int $entityId
     */
    private function _setProductWebsite($entityId)
    {
        $tableName = $this->_getResourceConnection()->getTableName('catalog_product_website');
        $sql = 'INSERT INTO ' . $tableName . ' (product_id, website_id)
                VALUES (' . $entityId . ', ' . $this->_websiteId . ')';

        $this->_getWriteConnection()->query($sql);
    }

    /**
     * Check if product allows backorder and set is in stock
     *
     * @param array $skuQty
     * @param array $item
     * @return int
     */
    private function checkIsInStock($skuQty, $item)
    {
        $isInStock = $skuQty[trim($item['sku'])] > 0 ? 1 : 0;

        if ($isInStock == 0) {
            if ($item['backorders'] != 0 && $item['use_config_backorders'] == 0) {
                $isInStock = 1;
            }

            if ($item['backorders'] == 0 && $item['use_config_backorders'] == 1) {
                if (Mage::getStoreConfig('cataloginventory/item_options/backorders') != 0) {
                    $isInStock = 1;
                }
            }
        }

        return $isInStock;
    }

    /**
     * Retrieve the read connection
     *
     * @return mixed
     */
    private function _getReadConnection()
    {
        return $this->_getResourceConnection()->getConnection('core_read');
    }

    /**
     * Retrieve the write connection
     *
     * @return mixed
     */
    private function _getWriteConnection()
    {
        return $this->_getResourceConnection()->getConnection('core_write');
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}
