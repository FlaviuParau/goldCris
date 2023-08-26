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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_Swatches_Helper_Configurable extends Mage_ConfigurableSwatches_Helper_Data
{
    /**
     * Get swatches product javascript
     *
     * @return string
     */
    public function getSwatchesProductJs()
    {
        /**
         * @var $product Mage_Catalog_Model_Product
         */
        $product = Mage::registry('current_product');
        if ($this->isEnabled() && $product) {
            $configAttrs = $this->getSwatchAttributeIds();
            $configurableAttributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
            foreach ($configurableAttributes as $configurableAttribute) {
                if (in_array($configurableAttribute['attribute_id'], $configAttrs)) {
                    if (Mage::helper('blugento_swatches')->isExtensionEnabled()) {
                        $path = 'js/swatches/swatches-product.js';
                    } else {
                        $path = 'js/configurableswatches/swatches-product.js';
                    }
                    return $path;
                }
            }
        }
        return '';
    }
}
