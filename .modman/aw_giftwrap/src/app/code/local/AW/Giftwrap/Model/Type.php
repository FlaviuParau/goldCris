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


class AW_Giftwrap_Model_Type extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_giftwrap/type');
    }

    /**
     * @return AW_Giftwrap_Model_Type
     */
    protected function _beforeSave()
    {
        if (is_array($this->getStoreIds())) {
            $this->setStoreIds(implode(',', $this->getStoreIds()));
        }
        return parent::_beforeSave();
    }

    /**
     * @return AW_Giftwrap_Model_Type
     */
    protected function _afterSave()
    {
        if (strlen($this->getStoreIds()) > 0) {
            $this->setStoreIds(array_map('intval', explode(',', $this->getStoreIds())));
        } else {
            $this->setStoreIds(array());
        }
        return parent::_afterSave();
    }

    /**
     * @return AW_Giftwrap_Model_Type
     */
    protected function _afterLoad()
    {
        if (strlen($this->getStoreIds()) > 0) {
            $this->setStoreIds(array_map('intval', explode(',', $this->getStoreIds())));
        } else {
            $this->setStoreIds(array());
        }
        return parent::_afterLoad();
    }


    /**
     * Return resized image url
     *
     * @param int $width
     * @param int $height = null
     *
     * @return string
     */
    public function getImageUrl($width = 100, $height = null)
    {
        return Mage::helper('aw_giftwrap/image')->resizeImage(
            $this->getId(),
            $this->getImage(),
            $width,
            $height
        );
    }

    /**
     * Return original image url
     *
     * @return string
     */
    public function getOriginalImageUrl()
    {
        return Mage::helper('aw_giftwrap/image')->getImageUrl($this->getId(), $this->getImage());
    }

    /**
     * Retrieve formatted price
     *
     * @return string
     */
    public function getFormattedPrice()
    {
        $store = Mage::getSingleton('checkout/session')->getQuote()->getStore();
        return $store->convertPrice($this->getPrice(), true, false);
    }
}