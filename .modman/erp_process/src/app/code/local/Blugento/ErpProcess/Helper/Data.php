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

class Blugento_ErpProcess_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *  Update product price
     *
     * @param Varien_Data_Collection $details
     * @return Varien_Object
     */
    public function updateProductPrice(Varien_Data_Collection $details)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->updateProductPrice($details);
    }

    /**
     *  Update product stock
     *
     * @param Varien_Data_Collection $details
     * @return Varien_Object
     */
    public function updateProductStock(Varien_Data_Collection $details)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->updateProductStock($details);
    }

    /**
     *  Update orders
     *
     * @param Varien_Data_Collection $details
     * @return Varien_Object
     */
    public function updateOrder(Varien_Data_Collection $details)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->updateOrder($details);
    }

    /**
     * Store invoice
     *
     * @param Varien_Data_Collection $invoices
     * @param Varien_Object $config
     */
    public function storeInvoice(Varien_Data_Collection $invoices, Varien_Object $config)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->storeInvoice($invoices, $config);
    }

    /**
     * Get new placed orders that wasn't integrated yet.
     *
     * @param string $table
     * @param string $key
     * @param string $status
     * @return mixed
     */
    public function getNewOrders($table, $key, $status = 0)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        $orderFields = $this->_getOrderFields();
        $addressFields = $this->_getOrderAddressFields();
        $paymentFields = $this->_getOrderPaymentFields();

        $select = '';
        foreach ($orderFields as $as => $field) {
            $select .= 'so.' . $field . ' AS ' . $as . ', ';
        }

        foreach ($addressFields as $as => $field) {
            $select .= 'soa.' . $field . ' AS ' . $as . ', ';
        }

        foreach ($paymentFields as $as => $field) {
            $select .= 'sop.' . $field . ' AS ' . $as . ', ';
        }

        $select = substr($select, 0, strlen($select) - 2);

        return $model->getNewOrders($table, $key, $select, $status);
    }

    /**
     * Return orders by order status
     *
     * @param array $statuses
     * @return Varien_Object|null
     */
    public function getOrdersByStatus($statuses)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->getOrdersByStatus($statuses);
    }

    /**
     * Create product
     *
     * @param Varien_Data_Collection $products
     * @return Varien_Object
     */
    public function createProduct(Varien_Data_Collection $products)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->createProduct($products);
    }

    /**
     * @param array $orders
     * @return mixed
     */
    public function updateOrdersFromErp($orders)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        return $model->updateOrdersFromErp($orders);
    }

    /**
     * Reindex Stock data
     *
     * @param string $index
     */
    public function _reindexData($index)
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

    /**
     * Return items select.
     *
     * @return string
     */
    public function getItemSelect()
    {
        $fields = $this->_getItemFields();

        $select = '';
        foreach ($fields as $as => $field) {
            $select .= $field . ' AS ' . $as . ', ';
        }

        $select = substr($select, 0, strlen($select) - 2);

        return $select;
    }

    /**
     * Create invoice number
     *
     * @param string $orderIncrement
     * @param string $invoiceNo
     */
    public function createInvoiceForOrder($orderIncrement, $invoiceNo = '')
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        $model->createInvoiceForOrder($orderIncrement, $invoiceNo);
    }

    public function updateOrderStatus($orderIncrement)
    {
        /* @var Blugento_ErpProcess_Model_Process $model */
        $model = Mage::getModel('blugento_erpprocess/process');

        $model->updateOrderStatus($orderIncrement);
    }

    /**
     * Return an array with all the 'sales_flat_order' table fields.
     * The key is the SQL AS and the value is table column.
     *
     * @return array
     */
    private function _getOrderFields()
    {
        return array(
            'order_id'        => 'entity_id',
            'status'          => 'status',
            'shipping_method' => 'shipping_method',
            'shipping_description' => 'shipping_description',
            'increment_id'    => 'increment_id',
            'email'           => 'customer_email',
            'comment'         => 'customer_note',
            'total_price'     => 'base_grand_total',
            'subtotal_price'  => 'subtotal_incl_tax',
            'shipping_price'  => 'shipping_incl_tax',
            'shipping_tax'    => 'shipping_tax_amount',
            'currency'        => 'base_currency_code',
            'created_date'    => 'created_at',
            'discount_amount' => 'discount_amount',
            'left_to_pay'     => 'base_total_due',
            'store_id'        => 'store_id',
        );
    }

    /**
     * Return an array with all the 'sales_flat_order_address' table fields.
     * The key is the SQL AS and the value is table column.
     *
     * @return array
     */
    private function _getOrderAddressFields()
    {
        return array(
            'region'        => 'region',
            'postcode'      => 'postcode',
            'lastname'      => 'lastname',
            'firstname'     => 'firstname',
            'address'       => 'street',
            'city'          => 'city',
            'phone'         => 'telephone',
            'country'       => 'country_id',
            'company'       => 'company',
            'cui'           => 'vat_id',
            'purchase_type' => 'blugento_purchase_type',
            'reg_no'        => 'blugento_customer_reg_no',
            'iban'          => 'blugento_customer_iban',
            'bank'          => 'blugento_customer_bank',
            'headquarter'   => 'blugento_customer_headquarter',
            'cnp'           => 'blugento_customer_cnp'
        );
    }

    /**
     * Return an array with all the 'sales_flat_order_payment' table fields.
     * The key is the SQL AS and the value is table column.
     *
     * @return array
     */
    private function _getOrderPaymentFields()
    {
        return array(
            'payment_method' => 'method',
            'amount_paid' => 'amount_paid',
        );
    }

    /**
     * Return an array with all the 'sales_flat_order_item' table fields.
     * The key is the SQL AS and the value is table column.
     *
     * @return array
     */
    private function _getItemFields()
    {
        return array(
            'product_id' => 'product_id',
            'type'       => 'product_type',
            'weight'     => 'weight',
            'sku'        => 'sku',
            'name'       => 'name',
            'qty'        => 'qty_ordered',
            'price'      => 'price_incl_tax',
            'tax_percent'=> 'tax_percent',
            'base_price_excl_tax'=> 'base_price',
            'price_excl_tax'=> 'price',
            'parent_item_id'=> 'parent_item_id',
        );
    }
}
