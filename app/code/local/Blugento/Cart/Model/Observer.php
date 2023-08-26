<?php
/**
 * Blugento Cart Settings
 * Observer Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Cart_Model_Observer
{
    private $_country = 'RO';

    /**
     * Set all products to not saleable
     *
     * @param $observer
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function modifySaleable($observer)
    {
        if (!Mage::helper('blugento_cart')->isEnabled()) {
            return false;
        }

        $hideAll = Mage::getStoreConfig('blugento_cart/global_config/hide_all');
        $saleable = $observer->getSalable();
        if ($hideAll) {
            $saleable->setIsSalable(false);
        } else {
            $custom = Mage::getResourceModel('catalog/product')->getAttributeRawValue($saleable->getData('product')->getId(),
                'blugento_cart_custom', Mage::app()->getStore()->getStoreId());
            if ($custom) {
                $saleable->setIsSalable(false);
            }
        }
    }

    /**
     * Display shipping price on cart page when a quote is created or updated.
     *
     * @throws Mage_Core_Exception
     */
    public function addShippingPrice()
    {
	    $helper = Mage::helper('blugento_cart');
        $storeId = Mage::app()->getStore()->getStoreId();

	    if (!$helper->isShippingPriceCartEnabled($storeId)) {
	    	return;
	    }

        if (Mage::registry('checkout_addShipping')) {
            Mage::unregister('checkout_addShipping');
            return;
        }
        Mage::register('checkout_addShipping',true);
        
        $cart = Mage::getSingleton('checkout/cart');
        $quote = $cart->getQuote();

        $quote->getShippingAddress()->setShippingMethod('');

        if ($quote->getId()) {
            $shippingMethodCode = $this->getLowestShippingMethodCode($quote->getId(), $storeId);
        }

        if ($quote->getCouponCode() != '') {
		    $c = Mage::getResourceModel('salesrule/rule_collection');
		    $c->getSelect()->where("code=?", $quote->getCouponCode());
		
		    $coupon = '';
		
		    foreach ($c->getItems() as $item) {
			    $coupon = $item;
		    }
		
		    if ($coupon) {
			    if ($coupon->getSimpleFreeShipping() > 0) {
				    $quote->getShippingAddress()->setShippingMethod($shippingMethodCode)->save();
				    return true;
			    }
		    }
	    }
	
	    try {
            if ($quote->getShippingAddress()->getCountryId() == '') {
                $countryCode = substr(Mage::getStoreConfig('general/locale/code', $storeId), 3);
                $quote->getShippingAddress()->setCountryId($countryCode);
		    }
		
		    $quote->getShippingAddress()->setCollectShippingRates(true);
		    $quote->getShippingAddress()->collectShippingRates();
		
		    $rates = $quote->getShippingAddress()->getAllShippingRates();
		    $allowed_rates = array();
		    foreach ($rates as $rate) {
			    array_push($allowed_rates,$rate->getCode());
		    }

            if (!in_array($shippingMethodCode,$allowed_rates) && count($allowed_rates) > 0) {
			    $shippingCode = $allowed_rates[0];
		    }

            if (isset($shippingCode)) {
			    $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
			    if ($address->getCountryId() == '') $address->setCountryId($this->_country);
			    if ($address->getCity() == '') $address->setCity('');
			    if ($address->getPostcode() == '') $address->setPostcode('');
			    if ($address->getRegionId() == '') $address->setRegionId('');
			    if ($address->getRegion() == '') $address->setRegion('');
			    $address->setShippingMethod($shippingCode)->setCollectShippingRates(true);
			    Mage::getSingleton('checkout/session')->getQuote()->save();
		    } else {
			    $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
			    if ($address->getCountryId() == '') $address->setCountryId($this->_country);
			    if ($address->getCity() == '') $address->setCity('');
			    if ($address->getPostcode() == '') $address->setPostcode('');
			    if ($address->getRegionId() == '') $address->setRegionId('');
			    if ($address->getRegion() == '') $address->setRegion('');
			    $address->setShippingMethod($shippingMethodCode)->setCollectShippingRates(true);
			    Mage::getSingleton('checkout/session')->getQuote()->save();
		    }
		
		    Mage::getSingleton('checkout/session')->resetCheckout();
	    }
	    catch (Mage_Core_Exception $e) {
		    Mage::getSingleton('checkout/session')->addError($e->getMessage());
	    }
	    catch (Exception $e) {
		    Mage::getSingleton('checkout/session')->addException($e, Mage::helper('checkout')->__('Load customer quote error'));
	    }
    }

    /**
     * Return shipping method code with lowest price
     *
     * @param int $quoteId
     * @param int $storeId
     * @return string
     */
    protected function getLowestShippingMethodCode($quoteId, $storeId)
    {
        $helper = Mage::helper('blugento_cart');

        $configShippingMethods = $helper->getShippingMethodsCode($storeId);

        $quote = Mage::getModel('sales/quote')->load($quoteId);
        $address = $quote->getShippingAddress();
        $shippingRates = $address->getAllShippingRates();

        $shippingMethods = [];
        foreach($shippingRates as $rate){
            $shippingMethods[] = $rate->getData();
        }

        if ($configShippingMethods != 9999) {
            $codes = explode(',', $configShippingMethods);
            $methods = [];
            foreach ($shippingMethods as $shMethod) {
                foreach ($codes as $code) {
                    if ($shMethod['code'] == $code) {
                        $methods[] = $shMethod;
                    }
                }
            }
            $shippingMethods = $methods;
        }

        $methodCode = '';
        $minPrice = $shippingMethods[0]['price'];
        foreach ($shippingMethods as $key => $method) {
            if ($method['price'] <= $minPrice) {
                $methodCode = $method['code'];
            }
        }

        return $methodCode;
    }

    /**
     * Remove COD payment method if virtual products
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function filterPaymentMethod(Varien_Event_Observer $observer) {

        if (Mage::getStoreConfig('blugento_cart/cod_payment_virtual/enabled')) {
            /* call get payment method */
            $method = $observer->getEvent()->getMethodInstance();

            /*   get  Quote  */
            $quote = $observer->getEvent()->getQuote();

            $result = $observer->getEvent()->getResult();
            if (empty($quote) || (null === $quote)) {
                return $this;
            }

            /* Disable Your payment method */
            if ($method->getCode() == 'cashondelivery') {
                foreach ($quote->getAllItems() as $item) {
                    // get Cart item product Type //
                    if ($item->getProductType() == 'virtual'):
                        $result->isAvailable = false;
                    endif;
                }
            }
        }
    }
}
