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
 * @package     Blugento_Theme
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Theme_Helper_Theme extends Mage_Core_Helper_Abstract
{
    /**
     * Attach theme data after product collection load
     *
     * Dispatch by:: catalog_product_collection_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function attachThemeData($products, $storeId)
    {
        $helperTheme   = Mage::helper('blugento_theme');
        $_outputHelper = Mage::helper('catalog/output');
        $_imageHelper  = Mage::helper('catalog/image');

        $_productImageBackgroundColor = $helperTheme->getVariable('products-list__item__product-image_background-color', 'scss', '#fff');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $_productImageBackgroundColor)) {
            $_productImageBackgroundColor = '#fff';
        }
        $_productImageBackgroundColorToHex = $helperTheme->hex2rgb($_productImageBackgroundColor);

        /* @var $parentProduct Mage_Catalog_Model_Product */
        foreach ($products as $product) {

            $product->setData('image_square' , (string) $_imageHelper->init($product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(400, 400));
            $product->setData('image_width_resized' , (string) $_imageHelper->init($product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(400, 400));

//            'id'                        => $_product->getId(),
//            'sku'                       => $_product->getSku(),
//            'url'                       => $_product->getProductUrl(),
//            'image'                     => (string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 300),
//            'imageSquare'               => (string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(400, 400),
//            'imageWidthResized'         => (string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 400),
//            'imageHeightResized'        => (string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 200),
//            'imageHover'                => (string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 300),
//            'imageSquareHover'          => (string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(400, 400),
//            'imageWidthResizedHover'    => (string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 400),
//            'imageHeightResizedHover'   => (string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(300, 200),
//            'image@2x'                  => (string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($_productImageBackgroundColorToHex)->resize(600),
//            'image_alt'                 => $this->stripTags($this->getImageLabel($_product, 'small_image'), null, true),
//            'name'                      => $_outputHelper->productAttribute($_product, $_product->getName() , 'name'),
//            'name_after'                => '',
//            'name_stripped'             => $this->stripTags($_product->getName(), null, true),
//            'manufacturer'              => $_product->getAttributeText('manufacturer'),
//            'manufacturer_id'           => $_product->getManufacturer(),
//            'short_description'         => strip_tags($_outputHelper->productAttribute($_product, $_product->getShortDescription(), 'short_description')),
//            'price'                     => trim($this->getPriceHtml($_product, true)),
//            'price-discount'            => $helperTheme->getDiscountValue($_product),
//            'price-discount-percentage' => $helperTheme->getDiscountPercentage($_product),
//            'is_saleable'               => $_product->isSaleable(),
//            'add_to_cart'               => $this->getAddToCartUrl($_product),
//            'whishlist_url'             => ($this->helper('wishlist')->isAllow()) ? $this->helper('wishlist')->getAddUrl($_product) : '',
//            'compare_url'               => $this->getAddToCompareUrl($_product),
//            'has_add_to_links'          => ($_productListAddToLinksStatus == 1),
//            'reviews'                   => ($_product->getRatingSummary()) ? $this->getReviewsSummaryHtml($_product, 'short') : '',
//            'is_new'                    => ($_product->getNewsFromDate() || $_product->getNewsToDate()) &&
//                (
//                    $rightNow >= strtotime($_product->getNewsFromDate()) && $rightNow <= strtotime($_product->getNewsToDate()) ||
//                    $rightNow >= strtotime($_product->getNewsFromDate()) && is_null($_product->getNewsToDate()) ||
//                    $rightNow <= strtotime($_product->getNewsToDate()) && is_null($_product->getNewsFromDate())
//                ),
//            'is_sale'                   => $_product->getSpecialPrice() &&
//                (
//                    $rightNow >= strtotime($_product->getSpecialFromDate()) && $rightNow <= strtotime($_product->getSpecialToDate()) ||
//                    $rightNow >= strtotime($_product->getSpecialFromDate()) && is_null($_product->getSpecialToDate()) ||
//                    $rightNow <= strtotime($_product->getSpecialToDate()) && is_null($_product->getSpecialFromDate())
//                ),
            }
    }
}
