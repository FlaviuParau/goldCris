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


class AW_Giftwrap_Model_Observer_Checkout
{
    /**
     * Observer for save gift wrap selected data to session
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveGiftWrapDataToSession($observer)
    {
        /** @var AW_Giftwrap_Helper_Config $configHelper */
        $configHelper = Mage::helper('aw_giftwrap/config');
        if (!$configHelper->isEnabled()) {
            return $this;
        }
        Mage::getSingleton('checkout/session')->unsetData('aw_giftwrap');
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost('aw_giftwrap', null);
        $dataToStoring = array();
        if (is_null($data)) {
            return $this;
        }
        if (!array_key_exists('add_gift_wrap', $data)) {
            return $this;
        }
        $dataToStoring['add_gift_wrap'] = true;
        if (!array_key_exists('wrap_type_id', $data)) {
            return $this;
        }
        $dataToStoring['wrap_type_id'] = intval($data['wrap_type_id']);
        $dataToStoring['wrap_message'] = null;
        if (array_key_exists('wrap_message', $data) && $configHelper->isGiftMessageEnabled()) {
            $dataToStoring['wrap_message'] = $data['wrap_message'];
        }
        $dataToStoring['wrap_separately'] = false;
        if (array_key_exists('wrap_separately', $data) && $configHelper->isWrapProductsSeparately()) {
            $dataToStoring['wrap_separately'] = true;
        }
        Mage::getSingleton('checkout/session')->setData('aw_giftwrap', $dataToStoring);
        return $this;
    }

    /**
     * Observer for save gift wrap total amount and checkout GW data
     * to our table
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveOrderWrap($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $data = Mage::getSingleton('checkout/session')->getData('aw_giftwrap', null);
//        Mage::getSingleton('checkout/session')->unsetData('aw_giftwrap');
        if (is_null($data)) {
            return $this;
        }
        $giftWrapType = Mage::getModel('aw_giftwrap/type')->load($data['wrap_type_id']);
        if (is_null($giftWrapType->getId())) {
            return $this;
        }
        $orderWrapModel = Mage::getModel('aw_giftwrap/order_wrap')
            ->loadByOrder($order)
        ;
        if (!is_null($orderWrapModel->getId())) {
            return $this;
        }
        $orderWrapModel->setData(
            array(
                'order_id'                          => $order->getId(),
                'base_giftwrap_amount'              => $quote->getShippingAddress()->getBaseTotalAmount('aw_giftwrap'),
                'giftwrap_amount'                   => $quote->getShippingAddress()->getTotalAmount('aw_giftwrap'),
                'gift_message'                      => $data['wrap_message'],
                'is_wrapping_products_separately'   => $data['wrap_separately'],
                'giftwrap_type_info'                => $giftWrapType->getData()
            )
        );
        try {
            $orderWrapModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Observer for save order model to registry
     * after place order and before new order email sending
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveOrderToRegistry($observer)
    {
        if (!is_null(Mage::registry('current_order'))) {
            return $this;
        }
        Mage::register('current_order', $observer->getEvent()->getOrder());
        return $this;
    }
}