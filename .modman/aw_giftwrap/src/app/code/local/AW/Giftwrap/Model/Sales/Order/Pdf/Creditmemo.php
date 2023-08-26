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

class AW_Giftwrap_Model_Sales_Order_Pdf_Creditmemo
    extends Varien_Object
    implements AW_Lib_Model_AdminhtmlPdf_Sales_Order_Pdf_CreditmemoInterface
{
    protected $_order = null;

    /**
     * Check is can draw section
     *
     * @return bool
     */
    public function canDraw()
    {
        if (!Mage::helper('aw_giftwrap/config')->isEnabled()) {
            return false;
        }
        return true;
    }

    /**
     * Set order model
     *
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     */
    public function setOrderModel(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
    }

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrderModel()
    {
        return $this->_order;
    }

    /**
     * Check if needed rendering after address
     *
     * @return bool
     */
    public function hasAfterAddressSection()
    {
        return false;
    }

    /**
     * Get data for render section after addresses
     *
     * @return array
     */
    public function getAddressSectionData()
    {
        return null;
    }

    /**
     * Check if needed rendering after Payment and shipment
     *
     * @return bool
     */
    public function hasAfterPaymentShipmentSection()
    {
        return false;
    }

    /**
     * Get data for render section after Payment and shipment
     *
     * @return array
     */
    public function getPaymentShipmentSectionData()
    {
        return null;
    }

    /**
     * Check if needed rendering custom sections
     *
     * @return bool
     */
    public function hasCustomSection()
    {
        $linkedGiftwrap = $this->getLinkedGiftWrap();
        if ($linkedGiftwrap !== null) {
            return true;
        }
        return false;
    }

    /**
     * Get data for render custom sections
     *
     * @return array
     */
    public function getCustomSectionData()
    {
        $sectionData = array(
            'layout_type' => AW_Lib_Model_AdminhtmlPdf_Sales_Order_Pdf_Creditmemo::ONE_COLUMN_LAYOUT,
            'column'      => array(
                'left'  => array(
                    'title'  => Mage::helper('aw_giftwrap')->__('Order Gift Wrap Options'),
                    'values' => $this->prepareGiftwrapLines(),
                ),
            ),
        );
        return $sectionData;
    }

    /**
     * Prepare values for rendering in PDF
     * @return array
     */
    public function prepareGiftwrapLines()
    {
        $values = array();
        $linkedGiftwrap = $this->getLinkedGiftWrap();

        $values[] = array(
            'value' => Mage::helper('aw_giftwrap')->__('Wrapping products separately?'),
            'type'  => 'label',
        );
        $values[] = array(
            'value' => $linkedGiftwrap->getIsWrappingProductsSeparatelyLabel(),
            'type'  => 'value',
        );

        if (!is_null($this->getLinkedGiftWrap()->getGiftMessage())) {
            $values[] = array(
                'value' => Mage::helper('aw_giftwrap')->__('Gift Message'),
                'type'  => 'label',
            );
            $values[] = array(
                'value' => $linkedGiftwrap->getGiftMessage(),
                'type'  => 'value',
            );
        }

        $values[] = array(
            'value' => Mage::helper('aw_giftwrap')->__('Gift Wrap'),
            'type'  => 'label',
        );
        $giftwrapType = $linkedGiftwrap->getTypeModel();
        $values[] = array(
            'value' => $giftwrapType->getName(),
            'type'  => 'value',
        );
        $values[] = array(
            'value' => $giftwrapType->getFormattedPrice(),
            'type'  => 'value',
        );
        return $values;
    }

    /**
     * Retrieve linked gift wrap for current order
     * @return AW_Giftwrap_Model_Order_Wrap|null
     */
    public function getLinkedGiftWrap()
    {
        if (!is_null($this->getOrderModel())) {
            $linkedGiftWrap = Mage::getModel('aw_giftwrap/order_wrap')->loadByOrder($this->getOrderModel());
            if ($linkedGiftWrap->getId()) {
                return $linkedGiftWrap;
            }
        }
        return null;
    }
}