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
class Blugento_Swatches_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if extension is enabled
     *
     * @return int
     */
    public function isExtensionEnabled()
    {
        return (int) Mage::getStoreConfig('configswatches/blugento_swatches/enabled');
    }

    /**
     * Return swatch attributes
     *
     * @return string
     */
    public function getSwatchAttributes()
    {
        $attributes = Mage::getStoreConfig('configswatches/general/swatch_attributes');

        return $attributes;
    }

    /**
     * Check if display children gallery
     *
     * @return int
     */
    public function displayChildrenGallery()
    {
        return (int) Mage::getStoreConfig('configswatches/blugento_swatches/children_gallery');
    }

    /**
     * Return swatches template path for product view page
     *
     * @return string
     */
    public function getSwatchesViewTemplatePath()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'swatches/catalog/product/view/type/options/configurable/swatches.phtml';
        } else {
            $path = 'configurableswatches/catalog/product/view/type/options/configurable/swatches.phtml';
        }

        return $path;
    }

    /**
     * Return swatches template path for product listing
     *
     * @return string
     */
    public function getSwatchesListTemplatePath()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'swatches/catalog/product/list/swatches.phtml';
        } else {
            $path = 'configurableswatches/catalog/product/list/swatches.phtml';
        }

        return $path;
    }

    /**
     * Return swatches template path
     *
     * @return string
     */
    public function getMediaTemplatePath()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'swatches/catalog/product/view/media.phtml';
        } else {
            $path = 'configurableswatches/catalog/product/view/media.phtml';
        }

        return $path;
    }

    /**
     * Get product media javascript
     *
     * @return string
     */
    public function getProductMediaJs()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'js/swatches/product-media.js';
        } else {
            $path = 'js/configurableswatches/product-media.js';
        }
        return $path;
    }

    /**
     * Get swatches list javascript
     *
     * @return string
     */
    public function getSwatchesListJs()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'js/swatches/swatches-list.js';
        } else {
            $path = 'js/configurableswatches/swatches-list.js';
        }
        return $path;
    }

    /**
     * Get swatches prices javascript
     *
     * @return string
     */
    public function getProductSwatchPrices()
    {
        if ($this->isExtensionEnabled()) {
            $path = 'js/swatches/configurable-swatch-prices.js';
        } else {
            $path = 'js/configurableswatches/configurable-swatch-prices.js';
        }
        return $path;
    }
	
	/**
	 * Check if hover swatch option is enabled
	 *
	 * @return int
	 */
	public function isHoverSwatchImageEnabled()
	{
		return (int) Mage::getStoreConfig('configswatches/product_detail_dimensions/hover_swatch_image');
	}
	
	/**
	 * Return swatches image width
	 *
	 * @return int
	 */
	public function getHoverSwatchImageWidth()
	{
		if ($this->isHoverSwatchImageEnabled()) {
			$width = $this->fallbackImageWidth();
		} else {
			$width = Mage::getStoreConfig('configswatches/product_detail_dimensions/width');
		}
		
		return (int) $width;
	}
	
	/**
	 * Return swatches image height
	 *
	 * @return int
	 */
	public function getHoverSwatchImageHeight()
	{
		if ($this->isHoverSwatchImageEnabled()) {
			$height = $this->fallbackImageHeight();
		} else {
			$height = Mage::getStoreConfig('configswatches/product_detail_dimensions/height');
		}
		
		return (int) $height;
	}
	
	/**
	 * Set a fallback image width
	 *
	 * @return int
	 */
	public function fallbackImageWidth()
	{
		return (int) Mage::getStoreConfig('configswatches/product_detail_dimensions/hover_swatch_image_width') ?: 500;
	}
	
	/**
	 * Set a fallback image height
	 *
	 * @return int
	 */
	public function fallbackImageHeight()
	{
		return (int) Mage::getStoreConfig('configswatches/product_detail_dimensions/hover_swatch_image_height') ?: 500;
	}
	
	/**
	 * Check if option to hide product carousel is enabled
	 *
	 * @return int
	 */
	public function isHideProductCarouselOptionEnabled()
	{
		return (int) Mage::getStoreConfig('configswatches/product_detail_dimensions/hide_product_media_carousel');
	}
}
