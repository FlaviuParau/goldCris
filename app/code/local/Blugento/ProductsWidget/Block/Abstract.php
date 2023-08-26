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
abstract class Blugento_ProductsWidget_Block_Abstract extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface
{
	protected $_cacheKey;
	protected $_type;
	
	/**
	 * Internal constructor, that is called from real constructor
	 */
	protected function _construct()
	{
		if (!Mage::getStoreConfig('blugento_productswidget/cache/enabled')) {
			return parent::_construct();
		}

        $storeId = Mage::app()->getStore()->getId() ? Mage::app()->getStore()->getId() : 1;
		
		$params = array();
		foreach ($this->getData() as $attr=>$val) {
			if ($attr == 'type') {
				continue;
			}
			$params[] = $val;
		}
		$params = implode('', $params);
		
		$cacheKey = $storeId . $this->_type . strtoupper(md5(trim(str_replace(' ', '', $params))));
		
		$cacheLifetime = Mage::getStoreConfig('blugento_productswidget/cache/custom_cache_liftime');
		
		$this->addData(array(
			'cache_lifetime' => $cacheLifetime,
			'cache_tags'     => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG),
			'cache_key'      => $cacheKey,
		));
		
		$this->_cacheKey = $cacheKey;
	}
	
	/**
	 * Render block HTML | set template
	 *
	 * @return string
	 */
	public function _toHtml()
	{
		if (!Mage::getStoreConfig('blugento_productswidget/general/enabled')) {
			return '';
		}
		
		require_once 'MobileDetect/Mobile_Detect.php';
		$detect = new Mobile_Detect;
		if ($detect->isMobile() &&
			!$detect->isTablet() &&
			$this->getMobileShowItems() == Blugento_ProductsWidget_Helper_Data::DO_NOT_DISPLAY_PRODUCTS_ON_MOBILE
		) {
			return '';
		}
		
		if ($this->getDisplayType() == Blugento_ProductsWidget_Helper_Data::DISPLAY_TYPE_STANDARD) {
			$style = $this->getStandardStyle();
			
			if ($style == Blugento_ProductsWidget_Helper_Data::DISPLAY_STYLE_GRID) {
				$this->setTemplate('blugento/productswidget/grid.phtml');
			} else {
				$this->setTemplate('blugento/productswidget/list.phtml');
			}
		} else if ($this->getDisplayType() == Blugento_ProductsWidget_Helper_Data::DISPLAY_TYPE_SLIDER) {
			$this->setTemplate('blugento/productswidget/slider.phtml');
		}
		
		return parent::_toHtml();
	}
	
	/**
	 * Prepare collection with products.
	 *
	 * @return Mage_Core_Block_Abstract
	 * @throws Exception
	 */
	protected function _beforeToHtml()
	{
		if (Mage::helper('core')->isModuleEnabled('Blugento_HomepageManager')) {
			$this->addHomepagemanagerLayoutOptions();
		}
		
		$this->setProductCollection($this->_getProductCollection());
	}
	
	/**
	 * Return product collection.
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection|null
	 */
	abstract protected function _getProductCollection();
	
    /**
     * @param array $arr
     */
    public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    /**
     * @param array|string $key
     * @param null $value
     */
    public function setData($key, $value = null)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Add Homepagemanager layout options.
     */
    public function addHomepagemanagerLayoutOptions()
    {
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
			    $customBtnText = ($helperCart->getCategoryCustomBtnText() != '' && $helperCart->getDisplayCustomBtnRedirect()) ? $helperCart->getCategoryCustomBtnText() : $helperCart->getCustomBtnText();
		    }
	    }
	    
        $layoutOptions = array (
            'product_list_image_resize_width'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeWidth() ?: 300) : 300),
            'product_list_image_resize_height'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListImageResizeHeight() ?: 300) : 300),
            'product_list_hover'=>(int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListHover() ?: 2) : 2),
            'catalog_product_list_qty'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getCatalogProductListQty() ?: 2) : 2),
            'product_list_add_to_links_status'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListAddToLinksStatus() ?: 1) : 1),
            'product_list_image_width'=> Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 180) : 180,
            'product_list_tax_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListTaxMode() ?: 1) : 1),
            'product_add_to_cart_status'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductAddToCartStatus() ?: 2) : 2),
            'product_list_tax_shipping_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListTaxShippingMode() ?: 2) : 2),
            'product_list_discount_percentage_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountPercentageMode() ?: 2) : 2),
            'product_list_short_description_status'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionStatus() ?: 1) : 1),
            'product_list_title_bottom_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListTitleBottomMode() ?: 1) : 1),
            'product_list_short_description_bottom_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListShortDescriptionBottomMode() ?: 1) : 1),
            'product_list_discount_mode'=> (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListDiscountMode() ?: 2) : 2),
            'product_list_description_html' => (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListDescriptionHtml() ?: 2) : 2),
            'product_list_status' => (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListStatus() ?: 2) : 2),
	        'display_custom_btn' => $displayCustomBtn,
	        'display_custom_btn_text' => $customBtnText,
	        'hide_all_add_to_cart' => $hideAllAddToCart,
	        'helper_cart' => $helperCart,
	        'display_cart_price_custom' => $displayCustomPrice,
	        'show_empty_reviews' => (int) (Mage::app()->getLayout()->getBlock('root') ? (Mage::app()->getLayout()->getBlock('root')->getProductListEmptyReviews() ?: 2) : 2),
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
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    public function _afterToHtml($html)
    {
        $formkey = Mage::getSingleton('core/session')->getFormKey();
        $formkey = "/form_key/".$formkey."/";
        $html = preg_replace("/\/form_key\/[a-zA-Z0-9,.-]+\//", $formkey, $html);

        return parent::_afterToHtml($html);
    }

    /**
     * Retrieve active wishlist product
     *
     * @param int $productId
     * @return string
     */
    public function getActiveWishlist($productId)
    {
        $className = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);

            $collection = Mage::getModel('wishlist/item')->getCollection()
                ->addFieldToFilter('wishlist_id', $wishlist->getId())
                ->addFieldToFilter('product_id', $productId);
            $item = $collection->getFirstItem();

            $className = '';
            if ($item->getId()) {
                $className = 'active-wishlist';
            }
        }

        return $className;
    }

    /**
     * Retrieve active compare product
     *
     * @param int $productId
     * @return string
     */
    public function getActiveCompare($productId)
    {
        $collection = $this->helper('catalog/product_compare')->getItemCollection();

        $className = '';
        foreach($collection as $comparing_product) {
            if ($comparing_product->getId() === $productId) {
                $className = 'active-compare';
                break;
            }
        }

        return $className;
    }
}
