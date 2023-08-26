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

class Blugento_Theme_Block_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{
    public function getItems()
    {
        if (Mage::helper('core')->isModuleEnabled('Blugento_HomepageManager')) {
            $this->addHomepagemanagerLayoutOptions();
        }

        return $this->_itemCollection;
    }

    public function setProductExtraData($_product)
    {
        $_imageHelper  = Mage::helper('catalog/image');
        $_outputHelper = Mage::helper('catalog/output');
        $helperTheme   = Mage::helper('blugento_theme');

        $productImageBackgroundColorToHex = $this->getProductImageBackgroundColorToHex();

        $_priceDisplayType = $this->getPriceDisplayType();
        $shippingCostUrl = '#shipping-info';
        
        $_productImgHoverAttr = ($_product->getImageHover() != 'no_selection' && $_product->getImageHover() != NULL) ? 'image_hover' : 'thumbnail';

        $_product->setImage((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize($this->getProductListImageResizeWidth(),$this->getProductListImageResizeHeight()));
        if ($this->getProductListHover() == 1) {
	        $_product->setImageHover((string) $_imageHelper->init($_product, $_productImgHoverAttr)->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize($this->getProductListImageResizeWidth(),$this->getProductListImageResizeHeight()));
        }
        
        $_product->setImageAlt($this->stripTags($this->getImageLabel($_product, 'small_image'), null, true));
        $_product->setName($_outputHelper->productAttribute($_product, $_product->getName() , 'name'));
        $_product->setNameStripped($this->stripTags($_product->getName(), null, true));
        $_product->setManufacturer($_product->getAttributeText('manufacturer'));
        $_product->setManufacturerId($_product->getManufacturer());
        $_product->setShortDescription(strip_tags($_outputHelper->productAttribute($_product, $_product->getShortDescription(), 'short_description')));
        $_product->setPriceDiscount($helperTheme->getDiscountValue($_product));
        $_product->setPricePercentage($helperTheme->getDiscountPercentage($_product));
        $_product->setPrice(trim($this->getPriceHtml($_product, true)));
        $_product->setIsSaleable($_product->isSaleable());
        $_product->setAddToCart($this->getAddToCartUrl($_product));
        $_product->setWhishlistUrl((Mage::helper('wishlist')->isAllow()) ? Mage::helper('wishlist')->getAddUrl($_product) : '');
        $_product->setCompareUrl($this->getAddToCompareUrl($_product));
        $_product->setHasAddToLinks($this->getProductListAddToLinksStatus() == 1);
        $_product->setReviews(($_product->getRatingSummary()) ? $this->getReviewsSummaryHtml($_product, 'short') : '');

        //TODO:: refine this!
        $productUrlC = str_replace('?options=cart', '', $_product->getProductUrl());
        $productUrlC = str_replace('&options=cart', '', $productUrlC);
        $productUrlC = str_replace('&amp;options=cart', '', $productUrlC);
        $_product->setProductUrlC($productUrlC); // TODO:: refine this!

        if ($this->getProductListTaxMode() == 1) {
            $_hasSeparator = true;
            if ($_priceDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX) {
                $tax_text = '<span class="tax-details">' . $this->__('Excl. %s Tax', $this->getFormattedTaxRate($_product));
            } elseif ($_priceDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX)  {
                $tax_text = '<span class="tax-details">' . $this->__('Incl. %s Tax', $this->getFormattedTaxRate($_product));
            } else {
                $tax_text = '<span class="tax-details">';
                $_hasSeparator = false;
            }
            if ($this->getProductListTaxShippingMode() == 1) {
                if ($_hasSeparator) {
                    $tax_text .= '<span class="tax-separator">, </span>';
                }
                if ($this->isIncludingShippingCosts()) {
                    $tax_text .= '<span class="shipping-cost-details">' . $this->__('incl. <a href="%s">Shipping Cost</a>', $shippingCostUrl) . '</span>';
                } else {
                    $tax_text .= '<span class="shipping-cost-details">' . $this->__('excl. <a href="%s">Shipping Cost</a>', $shippingCostUrl) . '</span>';
                }
            }
            $tax_text .= '</span>';
        } else {
            $tax_text = '';
        }

        $_product->setTaxHtml($tax_text);

        return $_product;
    }

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $this->_itemCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter()
        ;

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        return $this;
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
        $this->_prepareData();
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
            'product_view_related_products_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsStatus() ?: 1),
            'product_view_related_products_slider_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderStatus() ?: 1),
	        'product_view_related_products_slider_slides_to_show' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderSlidesToShow() ?: 4),
	        'product_view_related_products_slider_slides_to_scroll' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderSlidesToScroll() ?: 1),
	        'product_view_related_products_slider_animation' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderAnimation() ?: 300),
            'product_view_related_products_slider_loop' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderLoop() ?: 2),
            'product_view_related_products_slider_center' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderCenter() ?: 2),
            'product_view_related_products_slider_dots' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderDots() ?: 2),
            'product_view_related_products_slider_mobile' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductViewRelatedProductsSliderMobile() ?: 2),
            'product_list_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListStatus() ?: 2),
	        'show_empty_reviews' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListEmptyReviews() ?: 2),
        );

        foreach ($layoutOptions as $option=>$val) {
            $this->setData($option, $val);
        }
    }
}
