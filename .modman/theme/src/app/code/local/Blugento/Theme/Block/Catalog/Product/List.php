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

/**
 * Enhanced block for product price display of all products in spite of bundles (got own block!).
 * Contains the normal price.phtml rendering and additionally a configured static block.
 *
 */
class Blugento_Theme_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     *
     * @param $product
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getFormattedTaxRate($product)
    {
        if ($this->getTaxRate($product) === null
            || $product->getTypeId() == 'bundle'
        ) {
            return '';
        }

        $locale = Mage::app()->getLocale()->getLocaleCode();
        $taxRate = Zend_Locale_Format::toFloat($this->getTaxRate($product), array('locale' => $locale));

        return $this->__('%s%%', $taxRate);
    }

    /**
     * Read tax rate from current product.
     *
     * @param $product
     * @return mixed
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTaxRate($product)
    {
        $taxRateKey = 'tax_rate_' . $product->getId();
        if (!$this->getData($taxRateKey)) {
            $this->setData($taxRateKey, $this->_loadTaxCalculationRate($product));
        }

        return $this->getData($taxRateKey);
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

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/display/type', $store);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return float|int
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _loadTaxCalculationRate(Mage_Catalog_Model_Product $product)
    {
        $taxPercent = $product->getTaxPercent();
        if (!$taxPercent) {
            $taxClassId = $product->getTaxClassId();
            if ($taxClassId) {
                $store = Mage::app()->getStore();
                $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                $group = Mage::getModel('customer/group')->load($groupId);
                $customerTaxClassId = $group->getData('tax_class_id');

                /* @var $calculation Mage_Tax_Model_Calculation */
                $calculation = Mage::getSingleton('tax/calculation');
                $request = $calculation->getRateRequest(null, null, $customerTaxClassId, $store);
                $taxPercent = $calculation->getRate($request->setProductClassId($taxClassId));
            }
        }

        if ($taxPercent) {
            return $taxPercent;
        }

        return 0;
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
        $helperTheme = Mage::helper('blugento_theme');
        $_productImageBackgroundColor = $helperTheme->getVariable('products-list__item__product-image_background-color', 'scss', '#fff');
        if (!preg_match('/^#[a-f0-9]{6}$/i', $_productImageBackgroundColor)) {
            $_productImageBackgroundColor = '#fff';
        }

        $helperCart = false;
        $displayCustomBtn = false;
        $displayCustomBtnOutofstock = false;
        $hideAllAddToCart = false;
        $customBtnText = '';
        $displayCustomPrice = false;
        if (Mage::helper('core')->isModuleEnabled('Blugento_Cart')) {
            $helperCart = Mage::helper('blugento_cart');
            $hideAllAddToCart = $helperCart->getHideAll();
            $displayCustomBtn = $helperCart->getDisplayCustomButton();
            $displayCustomPrice = $helperCart->getDisplayPrice();
            $displayCustomBtnOutofstock = $helperCart->getDisplayCustomButtonOutofstock();
            if ($displayCustomBtn || $displayCustomBtnOutofstock) {
                $customBtnText = $helperCart->getCustomBtnText();
            }
        }

        $layoutOptions = array(
            'product_list_image_resize_width' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeWidth() ?: 300),
            'product_list_image_resize_height' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeHeight() ?: 300),
            'product_list_hover' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListHover() ?: 2),
            'catalog_product_list_qty' => (int)(Mage::app()->getLayout()->getBlock('root')->getCatalogProductListQty() ?: 2),
            'product_list_add_to_links_status' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListAddToLinksStatus() ?: 1),
            'product_list_image_width' => Mage::app()->getLayout()->getBlock('root')->getProductListImageWidth() ?: 180,
            'product_list_tax_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 1),
            'product_add_to_cart_status' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductAddToCartStatus() ?: 2),
            'product_list_tax_shipping_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListTaxShippingMode() ?: 2),
            'product_list_discount_percentage_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListDiscountPercentageMode() ?: 2),
            'product_list_short_description_status' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionStatus() ?: 1),
            'product_list_title_bottom_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListTitleBottomMode() ?: 1),
            'product_list_short_description_bottom_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionBottomMode() ?: 1),
            'product_list_discount_mode' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListDiscountMode() ?: 2),
            'product_image_background_color_to_hex' => $helperTheme->hex2rgb($_productImageBackgroundColor),
            'product_list_description_html' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListDescriptionHtml() ?: 2),
            'product_list_status' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListStatus() ?: 2),
            'display_custom_btn' => $displayCustomBtn,
            'display_custom_btn_outofstock' => $displayCustomBtnOutofstock,
            'display_custom_btn_text' => $customBtnText,
            'display_cart_price_custom' => $displayCustomPrice,
            'hide_all_add_to_cart' => $hideAllAddToCart,
            'helper_cart' => $helperCart,
            'show_empty_reviews' => (int)(Mage::app()->getLayout()->getBlock('root')->getProductListEmptyReviews() ?: 2),
        );

        foreach ($layoutOptions as $option => $val) {
            $this->setData($option, $val);
        }
    }
}
