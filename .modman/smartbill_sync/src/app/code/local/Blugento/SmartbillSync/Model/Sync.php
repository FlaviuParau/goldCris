<?php

class Blugento_SmartbillSync_Model_Sync extends Mage_Core_Model_Abstract
{
    protected $_SbApiToken;
    protected $_SbApiUrl;
    protected $_Cif;
    protected $_Gestiune;
    protected $_hash;
    protected $_user;

    public function cronSyncStock($force=null)
    {
        $enableLog = Mage::getStoreConfig('blugento_smartbillsync/general/enable_log');
        $updates = -1;

        try {
            if ($this->_isValidToRun() || $force == 'force') {
                $currentDay = Mage::getSingleton('core/date')->date('w');
                $days = explode(',', Mage::getStoreConfig('blugento_smartbillsync/general/days'));
                if (in_array($currentDay, $days)) {
                    $updates = $this->syncStock();

                    if ($updates > 0) {
                        $this->reindexData('cataloginventory_stock');
                    }
                }
                $this->_setLastRunTime();
            }

            if ($enableLog) {
                Mage::log('CRON RUN: Update ' . $updates . ' products', null, 'smartbill_sync_cron.log', true);
            }
        } catch (Exception $e) {
            Mage::log('Sync ERROR:: ' . $e->getMessage(), null, 'smartbill_sync_error.log', true);
        }

        return $updates;
    }

    /**
     * Set the inventory sync last run time
     *
     * @param int $type
     */
    private function _setLastRunTime()
    {
        $runtime = date('Y-m-d H:i:s', time());
        $time = Mage::getStoreConfig('blugento_smartbillsync/general/time');
        $frequency = Mage::getStoreConfig('blugento_smartbillsync/general/sync_frequency');
        if ($time && $frequency == 24) {
            $runtime = substr($runtime, 0, -8) . str_replace(',', ':', $time);
        }
        Mage::getConfig()->saveConfig('blugento_smartbillsync/general/last_run_time', $runtime, 'default', 0)
            ->reinit();
    }

    /**
     * Check if sync is valid to run
     *
     * @param int|null $type
     * @return bool
     */
    private function _isValidToRun()
    {
        if (!Mage::getStoreConfig('blugento_smartbillsync/general/enabled')) {
            return null;
        }

        $lastRunTime = Mage::getStoreConfig('blugento_smartbillsync/general/last_run_time');
        $frequency   = Mage::getStoreConfig('blugento_smartbillsync/general/sync_frequency');

        $currentTime = strtotime(date('Y-m-d H:m:s'));

        if (!$lastRunTime) {
            return true;
        }

        $addReq = '+' . $frequency . ' hour +0 minutes';
        if ($frequency == 0.5) {
            $addReq = '+0 hour +30 minutes';
        }
        $validToRunAt = strtotime(date('Y-m-d H:i:s', strtotime($addReq, strtotime($lastRunTime))));

        if ($currentTime >= $validToRunAt) {
            return true;
        }

        return false;
    }

    public function syncStock()
    {
        $warehouse = Mage::getStoreConfig('blugento_smartbillsync/general/gestiune');
        $enableLog = Mage::getStoreConfig('blugento_smartbillsync/general/enable_log');

        if ($warehouse == 'Fara gestiune') {
            $warehouse = '';
        }

        $i = 0;
        $error = array();
        $missingSku = array();
        $updated = array();
        $skus = array();
        $skuQty = array();
        $existingSku = array();

        try {
            $stock = $this->getStocksAction($warehouse);

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($stock);

            $xml = simplexml_load_string($dom->saveXML());

            $list = $xml->list;
            $allProducts = 0;
            
            foreach($list as $l) {
	            $products = $l->products;
	            $allProducts += count($products);
	
	            $resource = Mage::getSingleton('core/resource');
	            $readConnection = $resource->getConnection('core_read');
	
	            $existingGestItems = array();
	            foreach ($products as $product) {
	                $qty = (int)$product->quantity;
	                $sku = $product->productCode;
	
	                if (!$sku) {
	                    $error[] = $sku ? $sku : $i;
	                    continue;
	                }
	                $sku = str_replace('.0', '', $sku);
	
	                array_push($skus, $sku);
	
	                if (isset($skuQty[$sku])) {
	                    $skuQty[$sku] += $qty;
	                } else {
	                    $skuQty[$sku] = $qty;
	                }
	
	                $existingGestItems[] = $sku;
	            }
	
	            if (count($skus)) {
	                $sql = 'SELECT e.entity_id AS id, e.sku AS sku, q.qty AS qty,
						q.backorders as backorder, q.use_config_backorders as configbackorder
	                    FROM catalog_product_entity e
	                    JOIN cataloginventory_stock_item q
	                    ON e.entity_id = q.product_id
	                    WHERE e.sku LIKE "' . $skus[0] . '"';
	
	                unset($skus[0]);
	
	                foreach ($skus as $sk) {
	                    $sql .= ' OR e.sku LIKE "' . $sk . '"';
	                }
	
	                $result = $readConnection->fetchAll($sql);
	
	                if (count($result)) {
	                    foreach ($result as $item) {
	                        $existingSku[] = $item['sku'];
	                        if (isset($skuQty[trim($item['sku'])]) && $item['qty'] != $skuQty[trim($item['sku'])]) {
	                            $tableName = $resource->getTableName('cataloginventory_stock_item');
	                            
	                            $isInStock = ($skuQty[trim($item['sku'])] > 0 || ($item['backorder'] != 0 && $item['configbackorder'] == 0) ||
                                    ($skuQty[trim($item['sku'])] <= 0 && $item['backorder'] == 0 && $item['configbackorder'] != 0 && Mage::getStoreConfig('cataloginventory/item_options/backorders') == 1)) ? 1 : 0;
	                            $qtyX = $skuQty[trim($item['sku'])] ? $skuQty[trim($item['sku'])] : 0;

	                            $sql = ' UPDATE  ' . $tableName . ' SET ';
	                            $sql .= ' qty  = ' . $qtyX;
	
	                            if (!is_null($isInStock)) {
	                                $sql .= ' , is_in_stock  = ' . $isInStock;
	                            }
	                            $sql .= '  WHERE product_id = ' . $item['id'];
	
	                            $result = $resource->getConnection('core_write')->query($sql);

	                            if ($result) {
	                                $updated[] = $item['sku'];
                                }
	                        }
	                    }
	                }
	                $missingSku = array_diff(array_map('trim', $skus), $existingSku);
	            } else {
	                $errors[] = 'There is no data in file.';
	            }
	
	            if (Mage::getStoreConfig('blugento_smartbillsync/general/disable_missing') && count($existingGestItems)) {
	                $this->_disabledMissingItems($existingGestItems, $resource);
	            }
            }
        } catch (Exception $e) {
            Mage::log('ERROR:: ' . $e->getMessage(), null, 'smartbill_sync_error.log', true);
        }
        if ($enableLog) {
            Mage::log('Warehouse: ' . $warehouse . ':: ' . count($updated) . ' products updated from ' . $allProducts . ' products | Missing SKU: ' . count($missingSku), Zend_Log::INFO, 'smartbill_sync_update_info.log', true);
        }

        return count($updated);
    }

    /**
     * Disable missing items.
     *
     * @param array $existingGestItems
     * @param $resource
     */
    private function _disabledMissingItems($existingGestItems, $resource)
    {
        $readConnection = $resource->getConnection('core_read');

        $table = $resource->getTableName('catalog_product_entity');
        $query = "SELECT entity_id as id, sku as sku FROM " . $table . " WHERE sku != '' AND type_id !='configurable'";
        $catalogItems = $readConnection->fetchAll($query);

        $missingIds = array();
        foreach ($catalogItems as $product) {
            $id = isset($product['id']) ? $product['id'] : null;
            $sku = isset($product['sku']) ? $product['sku'] : null;
            if (!in_array($sku, $existingGestItems)) {
                $missingIds[] = $id;
            }
        }

        $enableLog = Mage::getStoreConfig('blugento_smartbillsync/general/enable_log');

        if (count($missingIds)) {
            $missing = count($missingIds);
            $missingIds = implode(',', $missingIds);

            $writeConnection = $resource->getConnection('core_write');

            $table = $resource->getTableName('cataloginventory_stock_item');
            $query = 'UPDATE ' . $table . ' SET qty = 0 , is_in_stock = 0 WHERE product_id IN (' . $missingIds . ')';
            $writeConnection->query($query);

            if ($enableLog) {
                Mage::log('DISABLED ITEMS (' . $missing . ') | QUERY:: ' . $query, null, 'smartbill_sync_disable.log', true);
            }
        }
    }

    public function getStocksAction($warehouse)
    {
        $this->_Cif = Mage::getStoreConfig('blugento_smartbillsync/general/cif');
        $this->_SbApiToken = Mage::getStoreConfig('blugento_smartbillsync/general/token');
        $this->_SbApiUrl = Mage::getStoreConfig('blugento_smartbillsync/general/api_url');

        $this->_user = Mage::getStoreConfig('blugento_smartbillsync/general/user');
        $this->_hash = base64_encode($this->_user . ':' . $this->_SbApiToken);
        $params = array(
            'cif' => $this->_Cif,
            'date' => date('Y-m-d'),
            'warehouseName' => $warehouse,
            'productName' => '',
            'productCode' => ''
        );
        $query = http_build_query($params);
        $url = $this->_SbApiUrl . 'stocks?' . $query;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Content-Type:application/xml',
            'Accept:application/xml',
            'authorization:Basic ' . $this->_hash

        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;

    }

    /**
     * Reindex Stock data
     *
     * @param string $index
     */
    public function reindexData($index)
    {
        $process = Mage::getSingleton('index/indexer')->getProcessByCode($index);

        if ($process->getIndexer()->isVisible()) {
            try {
                Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
            } catch (Mage_Core_Exception $e) {
                Mage::log($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('admin/session')->addException($e,
                    Mage::helper('index')->__('There was a problem with reindexing process.')
                );
            }
        }
    }
}
