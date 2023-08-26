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
 * @package     Blugento_Multiproducts
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Multiproducts_Block_Catalog_Product_Widget_Multiproducts
    extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $mode = $this->getData('mode') ?: 1;
        $filter = $this->getData('filter_products') ?: 1;
        $collection = null;

        switch ($mode) {
            // Products
            case '1':
                $ids = $this->getData('ids');

                if ($ids) {
                    $cleanIds = array();
                    $ids = preg_split("/[\D]+/", $ids);

                    foreach ($ids as $id) {
                        $id = intval($id);

                        if ($id > 0) {
                            $cleanIds[] = $id;
                        }
                    }

                    $cleanIds = array_unique($cleanIds);

                    if (count($cleanIds)) {
                        $productsM = $this->_getProductsByIds($cleanIds);

                        $collection = new Varien_Data_Collection(); // TODO:: on refine change this
                        foreach ($cleanIds as $key=>$id) {
                            foreach ($productsM as $product) {
                                if ($product->getId() == $id) {
                                    $collection->addItem($product);
                                }
                            }
                        }
                    }
                }

                break;
            // Category
            case '2':
                $categoryId = $this->getData('categories');

                if ($categoryId > 0) {
                    $collection = $this->_getProductsByCategory($categoryId, $filter);
                } else {
                    $collection = $this->_getNewProducts();
                }

                break;
            default:
        }

        if (Mage::helper('core')->isModuleEnabled('Blugento_HomepageManager')) {
            $this->addHomepagemanagerLayoutOptions();
        }

        if (count($collection)) {
            $_imageHelper  = $this->helper('catalog/image');
            $_outputHelper = $this->helper('catalog/output');
            $helperTheme   = $this->helper('blugento_theme');

            $productImageBackgroundColorToHex = $this->getProductImageBackgroundColorToHex();

            $_priceDisplayType = $this->getPriceDisplayType();
            $shippingCostUrl = '#shipping-info';

            foreach ($collection as $_product) {
            	$_productImgHoverAttr = ($_product->getImageHover() != 'no_selection' && $_product->getImageHover() != NULL) ? 'image_hover' : 'thumbnail';
                $_product->setImage((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 300));
                $_product->setImageSquare((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(400, 400));
                $_product->setImageWidthResized((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 400));
                $_product->setImageHeightResized((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 200));
                $_product->setImageHover((string) $_imageHelper->init($_product, $_productImgHoverAttr)->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 300));
                $_product->setImageSquareHover((string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(400, 400));
                $_product->setImageWidthResizedHover((string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 400));
                $_product->setImageHeightResizedHover((string) $_imageHelper->init($_product, 'thumbnail')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(300, 200));
                $_product->setImage2x((string) $_imageHelper->init($_product, 'small_image')->keepFrame(true)->backgroundColor($productImageBackgroundColorToHex)->resize(600));
                $_product->setImageAlt($this->stripTags($this->getImageLabel($_product, 'small_image'), null, true));
                $_product->setName($_outputHelper->productAttribute($_product, $_product->getName() , 'name'));
                $_product->setNameStripped($this->stripTags($_product->getName(), null, true));
                $_product->setManufacturer($_product->getAttributeText('manufacturer'));
                $_product->setManufacturerId($_product->getManufacturer());
                $_product->setShortDescription($_product->getShortDescription());
                $_product->setPrice(trim($this->getPriceHtml($_product, true)));
                $_product->setPriceDiscount($helperTheme->getDiscountValue($_product));
                $_product->setPricePercentage($helperTheme->getDiscountPercentage($_product));
                $_product->setIsSaleable($_product->isSaleable());
                $_product->setAddToCart($this->getAddToCartUrl($_product));
                $_product->setWhishlistUrl((Mage::helper('wishlist')->isAllow()) ? Mage::helper('wishlist')->getAddUrl($_product) : '');
                $_product->setCompareUrl($this->getAddToCompareUrl($_product));
                $_product->setHasAddToLinks($this->getProductListAddToLinksStatus() == 1);
                $_product->setReviews(($_product->getRatingSummary()) ? $this->getReviewsSummaryHtml($_product, 'short') : '');

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
            }

            $this->setProductCollection($collection);
        }

        return parent::_beforeToHtml();
    }

    /**
     * Get products by ids
     *
     * @param array $ids
     * @return object
     */
    protected function _getProductsByIds($ids)
    {
        $products = Mage::getModel('catalog/product')->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', 4)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('entity_id', array('in' => $ids));

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($products);

        $products->load();

        return $products;
    }

    /**
     * Get new products
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function _getNewProducts()
    {
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', 4)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addAttributeToFilter('news_from_date', array('lt' => date('Y-m-d H:i:s')))
            ->addAttributeToFilter('news_to_date', array('gteq' => date('Y-m-d H:i:s')));

        return $products;
    }

    /**
     * Get products from a category
     *
     * @param $categoryId
     * @param $filter
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    private function _getProductsByCategory($categoryId, $filter = 1)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);

        if ( ! $category->getId()) {
            return false;
        }

        $products = $category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('visibility', array('gt' => 1))
            ->addStoreFilter(Mage::app()->getStore()->getId());

        // New products in front
        if ($filter == 2) {
            $products->addAttributeToSort('news_from_date', 'DESC');
        }

        return $products;
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

        $layoutOptions = array (
            'product_list_image_resize'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListImageResize() ?: 1),
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
            'product_list_discount_percentage_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountPercentageMode() ?: 2),
            'product_list_discount_mode'=> (int) (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountMode() ?: 2),
            'product_list_add_to_links_status' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListAddToLinksStatus() ?: 1),
            'product_list_image_width' => Mage::app()->getLayout()->getBlock('root')->getProductListImageWidth() ?: 180,
            'product_list_tax_mode' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 1),
            'product_list_tax_shipping_mode' => (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTaxShippingMode() ?: 2),
            'product_image_background_color_to_hex' => $helperTheme->hex2rgb($_productImageBackgroundColor),
        );
        foreach ($layoutOptions as $option=>$val) {
            $this->setData($option, $val);
        }
    }

    public function getTaxHtml($_product)
    {
        $taxHtml = '';

        if (method_exists($this, 'getFormattedTaxRate')) {
            $store = Mage::app()->getStore();
            $_priceDisplayType = Mage::getStoreConfig('tax/display/type', $store);

            if ($this->getProductListTaxMode() == 1) {
                $_hasSeparator = true;
                if ($_priceDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX) {
                    $taxHtml = '<span class="tax-details">' . $this->__('Excl. %s Tax', $this->getFormattedTaxRate($_product));
                } elseif ($_priceDisplayType == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX)  {
                    $taxHtml = '<span class="tax-details">' . $this->__('Incl. %s Tax', $this->getFormattedTaxRate($_product));
                } else {
                    $taxHtml = '<span class="tax-details">';
                    $_hasSeparator = false;
                }
                if ($this->getProductListTaxShippingMode() == 1) {
                    if ($_hasSeparator) {
                        $taxHtml .= '<span class="tax-separator">, </span>';
                    }
                    $shippingCostUrl = '#shipping-info';
                    if (Mage::getStoreConfig('catalog/price/including_shipping_costs')) {
                        $taxHtml .= '<span class="shipping-cost-details">' . $this->__('incl. <a href="%s">Shipping Cost</a>', $shippingCostUrl) . '</span>';
                    } else {
                        $taxHtml .= '<span class="shipping-cost-details">' . $this->__('excl. <a href="%s">Shipping Cost</a>', $shippingCostUrl) . '</span>';
                    }
                }
                $taxHtml .= '</span>';
            } else {
                $taxHtml = '';
            }
        } else {
            /* @var $_taxHelper Mage_Tax_Helper_Data */
            $_taxHelper = $this->helper('tax');
            if ($_taxHelper->displayPriceExcludingTax()) {
                $taxHtml = '<span class="tax-details">' . $this->__('Excluding tax') . '</span>';
            } else {
                $taxHtml = '<span class="tax-details">' . $this->__('Including tax') . '</span>';
            }
            $_productListTaxMode = (int) (Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 1);

            $taxHtml =  ($_productListTaxMode == 1) ? $taxHtml : '';
        }

        return $taxHtml;
    }

    public function getFormattedTaxRate($_product)
    {
        if ($this->getTaxRate($_product) === null
            || $_product->getTypeId() == 'bundle'
        ) {
            return '';
        }

        $locale = Mage::app()->getLocale()->getLocaleCode();
        $taxRate = Zend_Locale_Format::toFloat($this->getTaxRate($_product), array('locale' => $locale));

        return $this->__('%s%%', $taxRate);
    }

    /**
     * Read tax rate from current product.
     *
     * @return string Tax Rate
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
     * Gets tax percents for current product
     *
     * @param  Mage_Catalog_Model_Product $product Product Model
     * @return string Tax Rate
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
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/display/type', $store);
    }
}
