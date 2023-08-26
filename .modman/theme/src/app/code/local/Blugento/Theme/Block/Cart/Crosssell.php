<?php
/**
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
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Theme_Block_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    public function setLimit($limit)
    {
        $this->_maxItemCount = $limit;
    }

    /**
     * Returns whether or not the price contains taxes
     *
     * @return bool Flag if shipping costs are including taxes
     */
    public function isIncludingShippingCosts()
    {
        if (!$this->getData('is_including_shipping_costs')) {
            $this->setData(
                'is_including_shipping_costs',
                Mage::getStoreConfig('catalog/price/including_shipping_costs')
            );
        }

        return $this->getData('is_including_shipping_costs');
    }

    protected function _beforeToHtml()
    {
        if (Mage::helper('core')->isModuleEnabled('Blugento_HomepageManager')) {
            $this->addHomepagemanagerLayoutOptions();
        }

        return parent::_beforeToHtml();
    }

    /**
     * Add Homepagemanager layout options.
     */
    public function addHomepagemanagerLayoutOptions()
    {
        $helperTheme = $this->helper('blugento_theme');
        $productImageBackgroundColor = $helperTheme->getVariable('products-list__item__product-image_background-color', 'scss', '#fff');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $productImageBackgroundColor)) {
            $productImageBackgroundColor = '#fff';
        }

        $helperCart       = false;
        $displayCustomBtn = false;
        $hideAllAddToCart = false;
        $customBtnText    = '';
	    $displayCustomPrice = false;
        if (Mage::helper('core')->isModuleEnabled('Blugento_Cart')) {
            $helperCart = Mage::helper('blugento_cart');
            $hideAllAddToCart = $helperCart->getHideAll();
            $displayCustomBtn = $helperCart->getDisplayCustomButton();
	        $displayCustomPrice = $helperCart->getDisplayPrice();
            if ($displayCustomBtn) {
                $customBtnText = $helperCart->getCustomBtnText();
            }
        }

        $layoutOptions = array (
            'product_list_image_resize_width'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeWidth() ?: 300),
            'product_list_image_resize_height'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeHeight() ?: 300),
            'product_list_hover'=>(int) (Mage::app()->getLayout()->getBlock('root')->getProductListHover() ?: 2),
            'catalog_product_list_qty'=> (int) (Mage::app()->getLayout()->getBlock('root')->getCatalogProductListQty() ?: 2),
            'product_list_add_to_links_status'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListAddToLinksStatus() ?: 1),
            'product_list_image_width'=> Mage::app()->getLayout()->getBlock('root')->getProductListImageWidth() ?: 180,
            'product_list_tax_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 1),
            'product_add_to_cart_status'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductAddToCartStatus() ?: 2),
            'product_list_tax_shipping_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTaxShippingMode() ?: 2),
            'product_list_discount_percentage_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountPercentageMode() ?: 2),
            'product_list_short_description_status'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionStatus() ?: 1),
            'product_list_title_bottom_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTitleBottomMode() ?: 1),
            'product_list_short_description_bottom_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionBottomMode() ?: 1),
            'product_list_discount_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountMode() ?: 2),
            'product_image_background_color_to_hex' => $helperTheme->hex2rgb($productImageBackgroundColor),
            'product_list_description_html' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListDescriptionHtml() ?: 2),
            'display_custom_btn' => $displayCustomBtn,
            'display_custom_btn_text' => $customBtnText,
            'hide_all_add_to_cart' => $hideAllAddToCart,
            'helper_cart' => $helperCart,
	        'display_cart_price_custom' => $displayCustomPrice,
            'checkout_cart_crosssell_slider_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getCheckoutCartCrosssellSliderStatus() ?: 1),
            'product_list_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListStatus() ?: 2),
	        'show_empty_reviews' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListEmptyReviews() ?: 2),
        );

        foreach ($layoutOptions as $option=>$val) {
            $this->setData($option, $val);
        }
    }
}
