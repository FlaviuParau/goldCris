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
class Blugento_ProductsWidget_Helper_Data extends Mage_Core_Helper_Abstract
{

    const PRODUCTS_TYPE_CUSTOM_SELECT      = 1;
    const PRODUCTS_TYPE_CUSTOM_CATEGORY    = 2;
    const PRODUCTS_TYPE_CUSTOM_NEW         = 3;
    const PRODUCTS_TYPE_CUSTOM_DISCOUNTED  = 4;

    const DISPLAY_STYLE_GRID  = 1;
    const DISPLAY_STYLE_LIST  = 2;

    const DISPLAY_TYPE_STANDARD  = 1;
    const DISPLAY_TYPE_SLIDER    = 2;
    
    const DO_NOT_DISPLAY_PRODUCTS_ON_MOBILE = 2;
}
