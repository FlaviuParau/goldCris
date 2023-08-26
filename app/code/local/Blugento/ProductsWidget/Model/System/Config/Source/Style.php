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
 * @package     Blugento_ProductsWidget
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_ProductsWidget_Model_System_Config_Source_Style
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array(
            array(
                'value' => Blugento_ProductsWidget_Helper_Data::DISPLAY_STYLE_GRID,
                'label' => 'Grid'
            ),
            array(
                'value' => Blugento_ProductsWidget_Helper_Data::DISPLAY_STYLE_LIST,
                'label' => 'List'
            ),
        );
        return $array;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array(
            Blugento_ProductsWidget_Helper_Data::DISPLAY_STYLE_GRID => 'Grid',
            Blugento_ProductsWidget_Helper_Data::DISPLAY_STYLE_LIST => 'List'
        );
        return $options;
    }
}
