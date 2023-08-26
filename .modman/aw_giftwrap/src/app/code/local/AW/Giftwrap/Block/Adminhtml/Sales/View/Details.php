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

class AW_Giftwrap_Block_Adminhtml_Sales_View_Details extends Mage_Adminhtml_Block_Template
{
    /**
     * @var AW_Giftwrap_Model_Order_Wrap|null
     */
    protected $_linkedGiftwrap = null;

    /**
     * Retrieve Yes/No label for Is Wrapping Products Separately value
     * @return string
     */
    public function getIsWrappingProductsSeparatelyLabel()
    {
        return $this->getLinkedGiftWrap()->getIsWrappingProductsSeparatelyLabel();
    }

    /**
     * Show gift message if specified
     * @return bool
     */
    public function canShowGiftMessage()
    {
        return !is_null($this->getLinkedGiftWrap()->getGiftMessage());
    }

    /**
     * Retrieve formatted Gift Message string
     * @return string
     */
    public function getGiftMessage()
    {
        $giftMessage = $this->getLinkedGiftWrap()->getData('gift_message');
        return nl2br($this->escapeHtml($giftMessage));
    }

    /**
     * Retrieve Gift Wrap Type html
     *
     * @return string
     */
    public function getGiftWrapHtml()
    {
        return $this
            ->getChild('aw_giftwrap_sales_view_details_giftwrap')
            ->setGiftwrapType($this->getLinkedGiftWrap()->getTypeModel())
            ->toHtml()
        ;
    }

    /**
     * Retrieve linked gift wrap for current viewed order
     * @return AW_Giftwrap_Model_Order_Wrap|null
     */
    public function getLinkedGiftWrap()
    {
        if (is_null($this->_linkedGiftwrap) && !is_null($this->getOrder())) {
            $linkedGiftWrap = Mage::getModel('aw_giftwrap/order_wrap')->loadByOrder($this->getOrder());
            if ($linkedGiftWrap->getId()) {
                $this->_linkedGiftwrap = $linkedGiftWrap;
            }
        }
        return $this->_linkedGiftwrap;
    }

    /**
     * Get current viewed product
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        } elseif (Mage::registry('current_invoice')) {
            return Mage::registry('current_invoice')->getOrder();
        } elseif (Mage::registry('current_shipment')) {
            return Mage::registry('current_shipment')->getOrder();
        } elseif (Mage::registry('current_creditmemo')) {
            return Mage::registry('current_creditmemo')->getOrder();
        }
        return null;
    }
}