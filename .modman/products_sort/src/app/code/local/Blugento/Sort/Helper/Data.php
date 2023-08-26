<?php
/**
 * Blugento Products Sort
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sort
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sort_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSortingOptionValue($name){
        return strtolower(str_replace(' ', '_', $name));
    }

    public function isSortOptionEnabled($option){
        return (int) Mage::getStoreConfig('blugento_sort/global_config/'.$option);
    }
}
