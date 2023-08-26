<?php
/**
 * Class Me_Lff_Block_Checkout_Cart_Leftamountinfo
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Block_Checkout_Cart_Leftamountinfo
 */
class Me_Lff_Block_Checkout_Cart_Leftamountinfo extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_freeShippingCode = 'freeshipping_freeshipping';

    /**
     * @var Mage_Checkout_Model_Session|null
     */
    protected $_checkout = null;

    /**
     * @var Mage_Sales_Model_Quote|null
     */
    protected $_quote = null;

    /**
     * Get left amount price
     *
     * @return float
     */
    public function getAmountPrice()
    {
        $amountPrice = 0;

        $this->_getQuote();

        if (!is_null($this->_quote) && $this->_quote->getId()) {

            if (!$this->_getLffHelper()->showIfCartIsEmpty() && !(float)$this->_quote->getBaseSubtotal()) {
                $amountPrice = 0;
            } else {
                if (!$this->_quote->getShippingAddress()->getFreeShipping()) {
                    $amountPrice = $this->_getCurrentFreeShippingAmount() - $this->_getSubtotalInclTax($this->_quote);
                } else {
                    $amountPrice = 0;
                }
            }

        }

        return (float)$amountPrice;
    }

    /**
     * Get including or excluding tax string
     *
     * @return string
     */
    public function getTaxSuffixInfo()
    {
        $taxSuffixInfo = '';
        $_helper = $this->_getLffHelper();

        $taxSuffixConfig = $_helper->getTaxSuffixInfo();
        if (Me_Lff_Model_System_Config_Backend_Taxsuffix::EXCL_TAX == $taxSuffixConfig) {

            $taxSuffixInfo = $_helper->__('(excl. tax)');

        } elseif (Me_Lff_Model_System_Config_Backend_Taxsuffix::INCL_TAX == $taxSuffixConfig) {

            $taxSuffixInfo = $_helper->__('(incl. tax)');

        } elseif (Me_Lff_Model_System_Config_Backend_Taxsuffix::AUTO_TAX == $taxSuffixConfig) {

            $cartTax = $this->_quote->getShippingAddress()->getBaseTaxAmount();
            if (!is_null($cartTax) && (float)$cartTax) {
                $taxSuffixInfo = $_helper->__('(incl. tax)');
            } else {
                $taxSuffixInfo = $_helper->__('(excl. tax)');
            }

        }

        return $taxSuffixInfo;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        if (null === $this->_checkout) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_getCheckout()->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Get subtotal including tax
     *
     * @param Mage_Sales_Model_Quote $quote quote
     * @return float
     */
    protected function _getSubtotalInclTax(Mage_Sales_Model_Quote $quote)
    {
        return $quote->getShippingAddress()->getSubtotalInclTax();
    }

    /**
     * Get free shipping amount
     *
     * @return float|mixed
     * @throws Mage_Core_Exception
     */
    protected function _getCurrentFreeShippingAmount()
    {
        $freeShippingPrice = Mage::getStoreConfig('carriers/freeshipping/free_shipping_subtotal', Mage::app()->getStore());

        if (Mage::app()->getStore()->getId() != Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId()) {
            $baseCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
            $currentCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();
            if ($baseCurrency != $currentCurrency) {
                $freeShippingPrice = Mage::helper('directory')->currencyConvert($freeShippingPrice, $baseCurrency, $currentCurrency);
            }
        }

        return $freeShippingPrice;
    }

    /**
     * Get extension helper
     *
     * @return Me_Lff_Helper_Data
     */
    protected function _getLffHelper()
    {
        return Mage::helper('me_lff');
    }
}
