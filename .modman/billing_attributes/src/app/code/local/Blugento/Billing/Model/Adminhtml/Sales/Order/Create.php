<?php
/**
 * Blugento Billing Attributes
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Billing
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Billing_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    protected function _initBillingAddressFromOrder(Mage_Sales_Model_Order $order)
    {
        $quoteBillingAddress = $this->getQuote()->getBillingAddress();

        $orderBillingAddress = $order->getBillingAddress();

        if ($orderBillingAddress->getBlugentoPurchaseType()) {
            $quoteBillingAddress->setBlugentoPurchaseType($orderBillingAddress->getBlugentoPurchaseType());
        }
        if ($orderBillingAddress->getBlugentoCustomerCnp()) {
            $quoteBillingAddress->setBlugentoCustomerCnp($orderBillingAddress->getBlugentoCustomerCnp());
        }
        if ($orderBillingAddress->getBlugentoCustomerRegNo()) {
            $quoteBillingAddress->setBlugentoCustomerRegNo($orderBillingAddress->getBlugentoCustomerRegNo());
        }
        if ($orderBillingAddress->getBlugentoCustomerIban()) {
            $quoteBillingAddress->setBlugentoCustomerIban($orderBillingAddress->getBlugentoCustomerIban());
        }
        if ($orderBillingAddress->getBlugentoCustomerBank()) {
            $quoteBillingAddress->setBlugentoCustomerBank($orderBillingAddress->getBlugentoCustomerBank());
        }

        $this->getQuote()->getBillingAddress()->setCustomerAddressId('');
        Mage::helper('core')->copyFieldset(
            'sales_copy_order_billing_address',
            'to_order',
            $orderBillingAddress,
            $quoteBillingAddress
        );
    }

    protected function _initShippingAddressFromOrder(Mage_Sales_Model_Order $order)
    {
        $orderShippingAddress = $order->getShippingAddress();
        $quoteShippingAddress = $this->getQuote()->getShippingAddress()
            ->setCustomerAddressId('')
            ->setSameAsBilling($orderShippingAddress && $orderShippingAddress->getSameAsBilling());


        if ($orderShippingAddress->getBlugentoPurchaseType()) {
            $quoteShippingAddress->setBlugentoPurchaseType($orderShippingAddress->getBlugentoPurchaseType());
        }
        if ($orderShippingAddress->getBlugentoCustomerCnp()) {
            $quoteShippingAddress->setBlugentoCustomerCnp($orderShippingAddress->getBlugentoCustomerCnp());
        }
        if ($orderShippingAddress->getBlugentoCustomerRegNo()) {
            $quoteShippingAddress->setBlugentoCustomerRegNo($orderShippingAddress->getBlugentoCustomerRegNo());
        }
        if ($orderShippingAddress->getBlugentoCustomerIban()) {
            $quoteShippingAddress->setBlugentoCustomerIban($orderShippingAddress->getBlugentoCustomerIban());
        }
        if ($orderShippingAddress->getBlugentoCustomerBank()) {
            $quoteShippingAddress->setBlugentoCustomerBank($orderShippingAddress->getBlugentoCustomerBank());
        }
        Mage::helper('core')->copyFieldset(
            'sales_copy_order_shipping_address',
            'to_order',
            $orderShippingAddress,
            $quoteShippingAddress
        );
    }
}
