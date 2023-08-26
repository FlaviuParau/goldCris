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

class Blugento_ProductsWidget_Model_System_Config_Source_Slider_Item_Row
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('blugento_productswidget')->__('6 Items'),
                'value' => 6
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('5 Items'),
                'value' => 5
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('4 Items'),
                'value' => 4
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('3 Items'),
                'value' => 3
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('2 Items'),
                'value' => 2
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('1 Item'),
                'value' => 1
            )
        );
    }
}
