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


class AW_Giftwrap_Block_Sales_Order_Info_Details_Giftwrap extends Mage_Core_Block_Template
{
    /* @var AW_Giftwrap_Model_Type */
    protected $_giftwrapType = null;

    /**
     * Set Type model
     *
     * @param AW_Giftwrap_Model_Type
     * @return AW_Giftwrap_Block_Adminhtml_Sales_View_Details_Giftwrap
     */
    public function setGiftwrapType(AW_Giftwrap_Model_Type $giftwrapType)
    {
        $this->_giftwrapType = $giftwrapType;
        return $this;
    }

    /**
     * Retrieve Type model
     *
     * @return AW_Giftwrap_Model_Type
     */
    public function getGiftwrapType()
    {
        return $this->_giftwrapType;
    }

    /**
     * Retrieve Type name
     *
     * @return string|null
     */
    public function getName()
    {
        if ($this->getGiftwrapType()->getId()) {
            return $this->getGiftwrapType()->getName();
        }
        return null;
    }

    /**
     * Retrieve Type formatted price
     *
     * @return null|string
     */
    public function getPrice()
    {
        if ($this->getGiftwrapType()->getId()) {
            return $this->getGiftwrapType()->getFormattedPrice();
        }
        return null;
    }

    /**
     * Retrieve Type description
     *
     * @return null|string
     */
    public function getDescription()
    {
        if ($this->getGiftwrapType()->getId()) {
            return nl2br($this->escapeHtml($this->getGiftwrapType()->getDescription()));
        }
        return null;
    }

    /**
     * Retrieve Type image url
     *
     * @param int $width
     * @param int $height
     * @return null|string
     */
    public function getImageUrl($width, $height = null)
    {
        if ($this->getGiftwrapType()->getId()) {
            return $this->getGiftwrapType()->getImageUrl($width, $height);
        }
        return null;
    }
}