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
class Blugento_ProductsWidget_Model_System_Config_Source_Slider_Animation
{
    public function toOptionArray()
    {
        return array(
	        array(
		        'label' => Mage::helper('blugento_productswidget')->__('6000 ms'),
		        'value' => 6000
	        ),
	        array(
		        'label' => Mage::helper('blugento_productswidget')->__('4000 ms'),
		        'value' => 4000
	        ),
	        array(
		        'label' => Mage::helper('blugento_productswidget')->__('3000 ms'),
		        'value' => 3000
	        ),
	        array(
		        'label' => Mage::helper('blugento_productswidget')->__('2000 ms'),
		        'value' => 2000
	        ),
	        array(
		        'label' => Mage::helper('blugento_productswidget')->__('1000 ms'),
		        'value' => 1000
	        ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('500 ms'),
                'value' => 500
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('400 ms'),
                'value' => 400
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('300 ms'),
                'value' => 300
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('200 ms'),
                'value' => 200
            ),
            array(
                'label' => Mage::helper('blugento_productswidget')->__('100 ms'),
                'value' => 100
            )
        );
    }
}
