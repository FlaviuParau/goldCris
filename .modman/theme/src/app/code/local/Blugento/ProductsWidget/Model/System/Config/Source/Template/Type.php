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
class Blugento_ProductsWidget_Model_System_Config_Source_Template_Type
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Big Boxes'),
                'value' => 'grid-4 slider-enabled'
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Big Boxes - Only Images'),
                'value' => 'grid-4 box-image slider-enabled'
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Small Boxes'),
                'value' => 'grid-6 slider-disabled'
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Small Boxes - Only Images'),
                'value' => 'grid-6 box-image slider-disabled'
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Small Boxes - Left Image'),
                'value' => 'grid-4 box-left slider-disabled'
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('Small Boxes - Right Image'),
                'value' => 'grid-4 box-right slider-disabled'
            )
        );
    }
}
