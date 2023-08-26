<?php
/**
 * Blugento Products Sort
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sort
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sort_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    public function setCollection($collection)
    {
        parent::setCollection($collection);

        $id = Mage::app()->getRequest()->getParam('id');
        $value = Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, "default_sort_by", Mage::app()->getStore()->getId());

        $order = $this->getCurrentOrder();
        $direction = $this->getCurrentDirection();
        if ($order) {
            if ($order == 'popularity') {

//                $this->getCollection()->getSelect()
//                    ->joinLeft(
//                        array('sfoi' => $collection->getResource()->getTable('sales/order_item')),
//                        'e.entity_id = sfoi.product_id',
//                        array('qty_ordered' => 'SUM(sfoi.qty_ordered)')
//                    )
//                    ->group('e.entity_id')
//                    ->order('qty_ordered ' . $direction);

                $this->getCollection()->setOrder($this->getCurrentOrder(), $direction);

            } else if ($order == 'new_products') {
	            $this->setData('_current_grid_direction', $direction);
	            $this->getCollection()->getSelect()->order('entity_id ' . $direction);
            } else if ($order == 'discount') {
                $this->setData('_current_grid_direction', $direction);
                $this->getCollection()->getSelect()->order('(1 - price_index.final_price / price_index.price) ' . $direction);
            } else {
                $this->getCollection()->setOrder($this->getCurrentOrder(), $direction);
            }
        }

        return $this;
    }

    public function getCurrentDirection()
    {
        $dir = $this->_getData('_current_grid_direction');
        if ($dir) {
            return $dir;
        }

        $directions = array('asc', 'desc');
        $dir = strtolower($this->getRequest()->getParam($this->getDirectionVarName()));
        if ($dir && in_array($dir, $directions)) {
            if ($dir == $this->_direction) {
                Mage::getSingleton('catalog/session')->unsSortDirection();
            } else {
                $this->_memorizeParam('sort_direction', $dir);
            }
        } else {
            $dir = Mage::getSingleton('catalog/session')->getSortDirection();
        }
        // validate direction
        if (!$dir || !in_array($dir, $directions)) {
            if (!Mage::getStoreConfig('blugento_sort/global_config/price_descending')) {
                $dir = $this->_direction;
            } else {
                $dir = 'desc';
            }
        }
        $this->setData('_current_grid_direction', $dir);
        return $dir;
    }
}
