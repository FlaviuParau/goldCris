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


class AW_Giftwrap_Model_Sales_Total_Quote_Giftwrap extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     *
     */
    public function __construct()
    {
        $this->setCode('aw_giftwrap');
    }

    /**
     * Collect totals process.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return AW_Giftwrap_Model_Sales_Total_Quote_Giftwrap
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (!Mage::helper('aw_giftwrap/config')->isEnabled()) {
            return $this;
        }
        parent::collect($address);
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        $giftWrapData = Mage::getSingleton('checkout/session')->getData('aw_giftwrap');
        if (
            is_null($giftWrapData)
            || !is_array($giftWrapData)
            || !array_key_exists('add_gift_wrap', $giftWrapData)
            || !$giftWrapData['add_gift_wrap']
            || !array_key_exists('wrap_type_id', $giftWrapData)
        ) {
            return $this;
        }
        $typeId = $giftWrapData['wrap_type_id'];
        $type = Mage::getModel('aw_giftwrap/type')->load($typeId);
        if (is_null($type->getId())) {
            return $this;
        }
        $basePrice = floatval($type->getPrice());
        $price = Mage::app()->getStore()->convertPrice($basePrice);

        $address->setTotalAmount($this->getCode(), $price);
        $address->setBaseTotalAmount($this->getCode(), $basePrice);
        return $this;
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        parent::fetch($address);
        $price = $address->getTotalAmount($this->getCode());
        if (is_null($price)) {
            return $this;
        }

        $address->addTotal(
            array(
                'code' => $this->getCode(),
                'title' => Mage::helper('aw_giftwrap')->__('Gift Wrap'),
                'value' => $price
           )
        );
        return $this;
    }
}