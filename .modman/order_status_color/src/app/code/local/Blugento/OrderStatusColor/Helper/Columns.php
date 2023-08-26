<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */
class Blugento_OrderStatusColor_Helper_Columns extends Amasty_Ogrid_Helper_Columns
{
//    function getDefaultFields()
//    {
//        if (!$this->_defaultField) {
//            $this->_defaultField = array(
//                'am_real_order_id' => array(
//                    'header'=> Mage::helper('sales')->__('Order #'),
//                    'width' => '80px',
//                    'type'  => 'text',
//                    'index' => 'increment_id',
//                    'filter_index' => 'main_table.increment_id'
//                ),
//                'am_created_at' => array(
//                    'header' => Mage::helper('sales')->__('Purchased On'),
//                    'index' => 'created_at',
//                    'type' => 'datetime',
//                    'width' => '100px',
//                    'filter_index' => 'main_table.created_at'
//                ),
//                'am_billing_name' => array(
//                    'header' => Mage::helper('sales')->__('Bill to Name'),
//                    'index' => 'billing_name',
//                    'filter_index' => 'main_table.billing_name'
//                ),
//                'am_shipping_name' => array(
//                    'header' => Mage::helper('sales')->__('Ship to Name'),
//                    'index' => 'shipping_name',
//                    'filter_index' => 'main_table.shipping_name'
//                ),
//                'am_base_grand_total' => array(
//                    'header' => Mage::helper('sales')->__('G.T. (Base)'),
//                    'index' => 'base_grand_total',
//                    'type'  => 'currency',
//                    'currency' => 'base_currency_code',
//                    'filter_index' => 'main_table.base_grand_total'
//                ),
//                'am_grand_total' => array(
//                    'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
//                    'index' => 'grand_total',
//                    'type'  => 'currency',
//                    'currency' => 'order_currency_code',
//                    'filter_index' => 'main_table.grand_total'
//                ),
//            );
//
//            if (!Mage::app()->isSingleStoreMode()) {
//                $this->_defaultField['am_store_id'] = array(
//                    'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
//                    'index'     => 'store_id',
//                    'type'      => 'store',
//                    'store_view'=> true,
//                    'display_deleted' => true,
//                    'filter_index' => 'main_table.store_id'
//                );
//            }
//
//            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
//                $this->_defaultField['am_action'] = array(
//                        'header'    => Mage::helper('sales')->__('Action'),
//                        'width'     => '50px',
//                        'type'      => 'action',
//                        'getter'     => 'getId',
//                        'actions'   => array(
//                            array(
//                                'caption' => Mage::helper('sales')->__('View'),
//                                'url'     => array('base'=>'*/sales_order/view'),
//                                'field'   => 'order_id'
//                            )
//                        ),
//                        'filter'    => false,
//                        'sortable'  => false,
//                        'index'     => 'stores',
//                        'is_system' => true,
//                );
//            }
//        }
//        return $this->_defaultField;
//    }
//
//    protected function _removeDefaultColumns($grid)
//    {
//        $mainTableColumns = array(
//            'real_order_id', 'store_id',
//            'created_at', 'billing_name', 'shipping_name', 'base_grand_total',
//            'grand_total', 'action'
//        );
//
//        $columns = $grid->getColumns();
//
//        foreach ($columns as $column) {
//
//            $columnId = $column->getId();
//            if (in_array($columnId, $mainTableColumns)) {
//                $this->_removeColumn($grid, $columnId);
//            }
//        }
//    }
}
