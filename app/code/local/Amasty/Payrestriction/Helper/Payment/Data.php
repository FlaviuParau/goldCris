<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */
class Amasty_Payrestriction_Helper_Payment_Data extends Mage_Payment_Helper_Data
{
    /**
     * @var null|Amasty_Payrestriction_Model_Mysql4_Rule_Collection
     */
    protected $_allRules = null;

    public function getStoreMethods($store = null, $quote = null)
    {
        $methods = parent::getStoreMethods($store, $quote);
        if (!$quote) {
            return $methods;
        }

        $address = $quote->getShippingAddress();
        if (!$address->getEmail()) {
            $address = $quote->getBillingAddress();
        }
        $items = $quote->getAllItems();

        // remember subtotal                
        $subtotal = $address->getSubtotal();
        $baseSubtotal = $address->getBaseSubtotal();
        // set new
        $includeTax = Mage::getStoreConfig('ampayrestriction/general/tax');
        $includeDiscount = Mage::getStoreConfig('ampayrestriction/general/discount');
        $hlp = Mage::helper('ampayrestriction');
        $hlp->modifySubtotal($address, $includeTax, $includeDiscount);

        foreach ($methods as $k => $method) {
            /** @var Amasty_Payrestriction_Model_Rule $rule */
            foreach ($this->getRules($address, $items) as $rule) {
                if ($rule->restrict($method)) {
                    $codes = $this->getCouponCodes($quote);
                    if ($rule->validate($address)
                        && $hlp->isCouponValid($rule, $codes)
                        && !$hlp->isCouponValid($rule, $codes, true)
                    ) {
                        unset($methods[$k]);
                    }//if validate
                }//if restrict
            }//rules        
        }//methods

        // restore subtotal
        $address->setSubtotal($subtotal);
        $address->setBaseSubtotal($baseSubtotal);

        return $methods;
    }

    public function getCouponCodes($quote)
    {
        $codes = $quote->getCouponCode();

        if (!$codes) {
            return array();
        }

        $providedCouponCodes = explode(",", $codes);

        foreach ($providedCouponCodes as $key => $code) {
            $providedCouponCodes[$key] = trim($code);
        }

        return $providedCouponCodes;

    }

    /**
     * @param $address
     * @param $items - products in cart
     *
     * @return Amasty_Payrestriction_Model_Mysql4_Rule_Collection
     */
    public function getRules($address, $items)
    {
        if (is_null($this->_allRules)) {
            $this->_allRules = Mage::getResourceModel('ampayrestriction/rule_collection')
                ->addAddressFilter($address)
                ->addIsAdminFilter()
                ->addBackorderFilter($items);

            $this->_allRules->load();

            foreach ($this->_allRules as $rule) {
                $rule->afterLoad();
            }
        }

        return $this->_allRules;
    }
}