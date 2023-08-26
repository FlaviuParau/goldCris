<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Giftwrap_Block_Checkout_Onepage_Shippingmethod_Form extends Mage_Core_Block_Template
{
    protected $_wrapTypeCollection = null;

    public function isCanShow()
    {
        $configHelper = Mage::helper('aw_giftwrap/config');
        if (!$configHelper->isEnabled()) {
            return false;
        }
        if (!$this->getWrapTypeCollection()->getSize()) {
            return false;
        }
        $items = $this->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            if (Mage::helper('aw_giftwrap')->isValidProduct($item->getProduct())) {
                return true;
            }
        }
        return false;
    }

    public function isCanSpecifyMessageTextarea()
    {
        return Mage::helper('aw_giftwrap/config')->isGiftMessageEnabled();
    }

    public function isCanSpecifySeparatelyOption()
    {
        if (!Mage::helper('aw_giftwrap/config')->isWrapProductsSeparately()) {
            return false;
        }
        $items = $this->getQuote()->getAllVisibleItems();
        $validItemsQty = 0;
        foreach ($items as $item) {
            if (Mage::helper('aw_giftwrap')->isValidProduct($item->getProduct())) {
                $validItemsQty += $item->getQty();
                if ($validItemsQty > 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return AW_Giftwrap_Model_Resource_Type_Collection
     */
    public function getWrapTypeCollection()
    {
        if (is_null($this->_wrapTypeCollection)) {
            $this->_wrapTypeCollection = Mage::getModel('aw_giftwrap/type')->getCollection()
                ->addStoreFilter()
                ->sortBySortOrder()
            ;
        }
        return $this->_wrapTypeCollection;
    }

    public function formatPrice($price)
    {
        $store = Mage::getSingleton('checkout/session')->getQuote()
            ->getStore()
        ;
        return $store->convertPrice($price, true);
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * @return bool
     */
    public function isGiftWrapWasSelected()
    {
        $storedData = Mage::getSingleton('checkout/session')->getData('aw_giftwrap');
        if (is_null($storedData) || !array_key_exists('add_gift_wrap', $storedData)) {
            return false;
        }
        return $storedData['add_gift_wrap'];
    }

    /**
     * @return null|int
     */
    public function getSelectedGiftWrapType()
    {
        $storedData = Mage::getSingleton('checkout/session')->getData('aw_giftwrap');
        if (is_null($storedData) || !array_key_exists('wrap_type_id', $storedData)) {
            $firstWrapType = $this->getWrapTypeCollection()->getFirstItem();
            return $firstWrapType->getId();
        }
        return $storedData['wrap_type_id'];
    }

    /**
     * @return string
     */
    public function getSpecifiedGiftWrapMessage()
    {
        $storedData = Mage::getSingleton('checkout/session')->getData('aw_giftwrap');
        if (is_null($storedData) || !array_key_exists('wrap_message', $storedData)) {
            return '';
        }
        return $storedData['wrap_message'];
    }

    /**
     * @return bool
     */
    public function isGiftWrapSeparatelyWasSelected()
    {
        $storedData = Mage::getSingleton('checkout/session')->getData('aw_giftwrap');
        if (is_null($storedData) || !array_key_exists('wrap_separately', $storedData)) {
            return false;
        }
        return $storedData['wrap_separately'];
    }
}