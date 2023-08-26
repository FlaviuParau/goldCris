<?php
/**
 * Blugento Products Sort
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sort
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sort_Model_Config extends Mage_Catalog_Model_Config
{
    public function getAttributeUsedForSortByArray()
    {
        $attributes = array();
        if (Mage::getStoreConfig('blugento_sort/global_config/popularity')) {
            $attributes['popularity'] = Mage::helper('blugento_sort')->__('Popularity');
        }
        if (Mage::getStoreConfig('blugento_sort/global_config/new_products')) {
            $attributes['new_products'] = Mage::helper('blugento_sort')->__('New Products');
        }
        if (Mage::getStoreConfig('blugento_sort/global_config/discount')) {
            $attributes['discount'] = Mage::helper('blugento_sort')->__('Discount');
        }

        return array_merge(
            parent::getAttributeUsedForSortByArray(),
            $attributes
        );
    }
}
