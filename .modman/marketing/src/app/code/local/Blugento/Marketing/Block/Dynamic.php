<?php
/**
 * Blugento Marketing
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Customfilters
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Marketing_Block_Dynamic extends Mage_Catalog_Block_Product_View
{
    public function getDynamicCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $product = $this->getProduct();
        $price = $product->getFinalPrice();
        $productType = $product->getTypeId();

        if ($this->checkBundleProduct($product)) {
            $price = Mage::getModel('bundle/product_price')->getTotalPrices($product, 'min', 1);
        }

        if ($this->checkGroupedProduct($product)) {
            $prices = array();
            $assocProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($assocProducts as $assocProduct) {
                if ($assocProduct->getSpecialPrice()) {
                    array_push($prices, $assocProduct->getSpecialPrice());
                } else {
                    array_push($prices, $assocProduct->getPrice());
                }
            }
            sort($prices);
            $price = $prices[0];
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/dynamic', $storeId);
        $code = str_replace(
            array('{{product_id}}', '{{product_type}}', '{{price}}'),
            array($product->getId(), $productType, $price),
            $code
        );
        return $code;
    }

    public function getDynamicAnalyticsCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $product = $this->getProduct();
        $price = $product->getFinalPrice();
        $productType = $product->getTypeId();

        if ($this->checkBundleProduct($product)) {
            $price = Mage::getModel('bundle/product_price')->getTotalPrices($product, 'min', 1);
        }

        if ($this->checkGroupedProduct($product)) {
            $prices = array();
            $assocProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($assocProducts as $assocProduct) {
                if ($assocProduct->getSpecialPrice()) {
                    array_push($prices, $assocProduct->getSpecialPrice());
                } else {
                    array_push($prices, $assocProduct->getPrice());
                }
            }
            sort($prices);
            $price = $prices[0];
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/dynamic_analytics', $storeId);
        $code = str_replace(
            array('{{product_id}}', '{{product_type}}', '{{price}}'),
            array($product->getId(), $productType, $price),
            $code
        );
        return $code;
    }

    public function getDynamicNecessaryCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $product = $this->getProduct();
        $price = $product->getFinalPrice();
        $productType = $product->getTypeId();

        if ($this->checkBundleProduct($product)) {
            $price = Mage::getModel('bundle/product_price')->getTotalPrices($product, 'min', 1);
        }

        if ($this->checkGroupedProduct($product)) {
            $prices = array();
            $assocProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($assocProducts as $assocProduct) {
                if ($assocProduct->getSpecialPrice()) {
                    array_push($prices, $assocProduct->getSpecialPrice());
                } else {
                    array_push($prices, $assocProduct->getPrice());
                }
            }
            sort($prices);
            $price = $prices[0];
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/dynamic_necessary', $storeId);
        $code = str_replace(
            array('{{product_id}}', '{{product_type}}', '{{price}}'),
            array($product->getId(), $productType, $price),
            $code
        );
        return $code;
    }

    /**
     * Check if is bundle product
     *
     * @param $product
     * @return bool
     */
    private function checkBundleProduct($product)
    {
        $type = $product->getTypeId();
        if ($type == 'bundle') {
            return true;
        }
        return false;
    }

    /**
     * Check if is grouped product
     *
     * @param $product
     * @return bool
     */
    private function checkGroupedProduct($product)
    {
        $type = $product->getTypeId();
        if ($type == 'grouped') {
            return true;
        }
        return false;
    }
}
