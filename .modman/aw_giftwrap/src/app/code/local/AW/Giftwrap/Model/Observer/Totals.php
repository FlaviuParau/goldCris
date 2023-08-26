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


class AW_Giftwrap_Model_Observer_Totals
{

    /**
     * Observer for add gift wrap totals to order model before place and save
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function addGiftWrapTotalsToOrder($observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        $baseGiftWrapAmount = $quote->getShippingAddress()->getBaseTotalAmount('aw_giftwrap');
        $giftWrapAmount = $quote->getShippingAddress()->getTotalAmount('aw_giftwrap');
        $order->setBaseAwGiftwrapAmount(floatval($baseGiftWrapAmount));
        $order->setAwGiftwrapAmount(floatval($giftWrapAmount));
        return $this;
    }

    /**
     * Observer for save invoice gift wrap total amount to our table
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveInvoiceTotal($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getAwGiftwrapAmount() <= 0) {
            return $this;
        }
        $invoiceWrap = Mage::getModel('aw_giftwrap/order_invoice_wrap')
            ->setData(
                array(
                     'invoice_id'           => $invoice->getId(),
                     'base_giftwrap_amount' => $invoice->getBaseAwGiftwrapAmount(),
                     'giftwrap_amount'      => $invoice->getAwGiftwrapAmount()
                )
            )
        ;
        try {
            $invoiceWrap->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Observer for save creditmemo gift wrap total amount to our table
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function saveCreditmemoTotal($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if ($creditmemo->getAwGiftwrapAmount() <= 0) {
            return $this;
        }
        $creditmemoWrap = Mage::getModel('aw_giftwrap/order_creditmemo_wrap')
            ->setData(
                array(
                     'creditmemo_id'        => $creditmemo->getId(),
                     'base_giftwrap_amount' => $creditmemo->getBaseAwGiftwrapAmount(),
                     'giftwrap_amount'      => $creditmemo->getAwGiftwrapAmount()
                )
            )
        ;
        try {
            $creditmemoWrap->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Observer for add gift wrap total amount to order model
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function loadWrapToOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderWrap = Mage::getModel('aw_giftwrap/order_wrap')
            ->loadByOrder($order)
        ;
        if (is_null($orderWrap->getId())) {
            return $this;
        }
        $order->setAwGiftwrapAmount(floatval($orderWrap->getGiftwrapAmount()));
        $order->setBaseAwGiftwrapAmount(floatval($orderWrap->getBaseGiftwrapAmount()));
        return $this;
    }

    /**
     * Observer for add saved gift wrap total amount to invoice model
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function loadWrapToInvoice($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $invoiceWrap = Mage::getModel('aw_giftwrap/order_invoice_wrap')
            ->loadByInvoice($invoice)
        ;
        if (is_null($invoiceWrap->getId())) {
            return $this;
        }
        $invoice->setAwGiftwrapAmount(floatval($invoiceWrap->getGiftwrapAmount()));
        $invoice->setBaseAwGiftwrapAmount(floatval($invoiceWrap->getBaseGiftwrapAmount()));
        return $this;
    }

    /**
     * Observer for add saved gift wrap total amount to creditmemo model
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function loadWrapToCreditmemo($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $creditmemoWrap = Mage::getModel('aw_giftwrap/order_creditmemo_wrap')
            ->loadByCreditmemo($creditmemo)
        ;
        if (is_null($creditmemoWrap->getId())) {
            return $this;
        }
        $creditmemo->setAwGiftwrapAmount(floatval($creditmemoWrap->getGiftwrapAmount()));
        $creditmemo->setBaseAwGiftwrapAmount(floatval($creditmemoWrap->getBaseGiftwrapAmount()));
        return $this;
    }

    /**
     * Observer for add GW amount to Paypal ITEMAMT
     *
     * @param Varien_Object $observer
     * @return $this
     */
    public function addTotalToPaypal($observer)
    {
        $paypalCart = $observer->getEvent()->getPaypalCart();
        $salesEntity = $observer->getEvent()->getSalesEntity();
        if (is_null($salesEntity)) {
            //then magento version >= 1.5.x
            $salesEntity = $paypalCart->getSalesEntity();
        }
        $awGiftWrapItemNameFromSession = $this->_getGiftWrapTypeNameFromSession();
        if (null === $awGiftWrapItemNameFromSession) {
            return $this;
        }
        $baseGiftWrapAmount = 0.00;
        $awGiftWrapItemName = Mage::helper('aw_giftwrap')->__('Gift Wrap');
        if ($salesEntity instanceof Mage_Sales_Model_Quote) {
            $baseGiftWrapAmount = (float)$salesEntity->getShippingAddress()->getBaseTotalAmount('aw_giftwrap');
            $awGiftWrapItemName = $awGiftWrapItemNameFromSession;
        }
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $baseGiftWrapAmount = (float)$salesEntity->getBaseAwGiftwrapAmount();
            $orderWrap = Mage::getModel('aw_giftwrap/order_wrap')
                ->loadByOrder($salesEntity)
            ;
            if (null !== $orderWrap->getId()) {
                $awGiftWrapItemName = $orderWrap->getTypeModel()->getName();
            } else {
                $awGiftWrapItemName = $awGiftWrapItemNameFromSession;
            }
        }
        if (is_null($paypalCart)) {
            //then magento version <= 1.4.x
            $additionalItems = $observer->getEvent()->getAdditional();
            $itemList = $additionalItems->getItems();
            $itemList[] = new Varien_Object(
                array(
                    'name'   => Mage::helper('aw_giftwrap')->__('Gift Wrap'),
                    'qty'    => 1,
                    'amount' => $baseGiftWrapAmount,
                )
            );
            $additionalItems->setItems($itemList);
            $salesEntity->setBaseSubtotal($salesEntity->getBaseSubtotal() + $baseGiftWrapAmount);
            return $this;
        }
        $paypalCart->addItem(Mage::helper('aw_giftwrap')->__('Gift Wrap'), 1, $baseGiftWrapAmount, $awGiftWrapItemName);
        return $this;
    }

    private function _getGiftWrapTypeNameFromSession()
    {
        $typeId = (int)Mage::getSingleton('checkout/session')->getData('aw_giftwrap/wrap_type_id');
        $type = Mage::getModel('aw_giftwrap/type')->load($typeId);
        if (null === $type->getId()) {
            return null;
        }
        return $type->getName();
    }
}