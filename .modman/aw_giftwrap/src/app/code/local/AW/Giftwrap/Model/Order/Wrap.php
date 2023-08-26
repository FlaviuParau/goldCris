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


class AW_Giftwrap_Model_Order_Wrap extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('aw_giftwrap/order_wrap');
    }

    /**
     * @return AW_Giftwrap_Model_Type
     */
    public function getTypeModel()
    {
        $typeModel = Mage::getModel('aw_giftwrap/type');
        $typeModel->setData($this->getData('giftwrap_type_info'));
        $typeModel->setBaseGiftwrapAmount($this->getBaseGiftwrapAmount());
        $typeModel->setGiftwrapAmount($this->getGiftwrapAmount());
        return $typeModel;
    }

    /**
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrderModel()
    {
        $order = Mage::getModel('sales/order')->load($this->getOrderId());
        if (is_null($order->getId())) {
            return null;
        }
        return $order;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return AW_Giftwrap_Model_Order_Wrap
     */
    public function loadByOrder(Mage_Sales_Model_Order $order)
    {
        return $this->load($order->getId(), 'order_id');
    }

    /**
     * Retrieve Yes/No label for Is Wrapping Products Separately value
     * @return string
     */
    public function getIsWrappingProductsSeparatelyLabel()
    {
        $optionArray = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        foreach ($optionArray as $option) {
            if ($option['value'] == $this->getData('is_wrapping_products_separately')) {
                return $option['label'];
            }
        }
        return null;
    }
}