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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enhanced block for product price display of all products in spite of bundles (got own block!).
 * Contains the normal price.phtml rendering and additionally a configured static block.
 *
 */
class Blugento_Localizer_Block_Catalog_Product_Price
    extends Mage_Catalog_Block_Product_Price
{
    /**
     * Add content of template block below price html if defined in config
     *
     * @return string Price HTML
     */
    public function _toHtml()
    {
        $html = trim(parent::_toHtml());

        if (!Mage::getStoreConfigFlag('catalog/price/display_block_below_price')) {
            return $html;
        }

        $htmlObject = new Varien_Object();
        $htmlObject->setParentHtml($html);
        $htmlTemplate = $this->getLayout()->createBlock('core/template')
            ->setTemplate('blugento/localizer/price_info.phtml')
            ->setProduct($this->getProduct())
            ->setFormattedTaxRate($this->getFormattedTaxRate())
            ->setIsIncludingTax($this->isIncludingTax())
            ->setIsIncludingShippingCosts($this->isIncludingShippingCosts())
            ->setPriceDisplayType(Mage::helper('tax')->getPriceDisplayType())
            ->setIsShowShippingLink($this->isShowShippingLink())
            ->toHtml();
        $htmlObject->setHtml($htmlTemplate);

        Mage::dispatchEvent('blugento_localizer_after_product_price',
            array(
                'html_obj' => $htmlObject,
                'block'    => $this,
            )
        );

        $html  = $htmlObject->getPrefix();
        $html .= $htmlObject->getParentHtml();
        $html .= $htmlObject->getHtml();
        $html .= $htmlObject->getSuffix();

        return $html;
    }

    /**
     * Read tax rate from current product.
     *
     * @return string Tax Rate
     */
    public function getTaxRate()
    {
        $taxRateKey = 'tax_rate_' . $this->getProduct()->getId();
        if (!$this->getData($taxRateKey)) {
            $this->setData($taxRateKey, $this->_loadTaxCalculationRate($this->getProduct()));
        }

        return $this->getData($taxRateKey);
    }

    /**
     * Retrieves formatted string of tax rate for user output
     *
     * @return string Formatted Tax Rate for the given locale
     */
    public function getFormattedTaxRate()
    {
        if ($this->getTaxRate() === null
            || $this->getProduct()->getTypeId() == 'bundle'
        ) {
            return '';
        }

        $locale = Mage::app()->getLocale()->getLocaleCode();
        $taxRate = Zend_Locale_Format::toFloat($this->getTaxRate(), array('locale' => $locale));

        return $this->__('%s%%', $taxRate);
    }

    /**
     * Returns whether or not the price contains taxes
     *
     * @return bool Flag if prices are shown with including tax
     */
    public function isIncludingTax()
    {
        if (!$this->getData('is_including_tax')) {
            $includesTax = Mage::helper('tax')->priceIncludesTax();
            $this->setData('is_including_tax', $includesTax);
        }

        return $this->getData('is_including_tax');
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
     * Returns whether the shipping link needs to be shown
     * on the frontend or not.
     *
     * @return bool Flag if shipping link should be displayed
     */
    public function isShowShippingLink()
    {
        $productTypeId = $this->getProduct()->getTypeId();
        $ignoreTypeIds = array('virtual', 'downloadable');
        if (in_array($productTypeId, $ignoreTypeIds)) {
            return false;
        }

        return true;
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
     * Translate block sentence
     *
     * @return string Translated text
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), 'Mage_Catalog');
        array_unshift($args, $expr);

        return Mage::app()->getTranslator()->translate($args);
    }
}
