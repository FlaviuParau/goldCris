<?php
/**
 * Blugento Marketing
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Customfilters
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Marketing_Block_Marketing extends Mage_Core_Block_Template
{
    public function getConversionCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/conversion', $storeId);

        $order = Mage::getSingleton('checkout/session')->getLastRealOrder();
        $currency = $order->getOrderCurrencyCode();
        $total = $order->getBaseGrandTotal();
        $orderId = $order->getIncrementId();
        $orderAmount = number_format($order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount() ,2);

        $firstname = $order->getCustomerFirstname();
        $lastname = $order->getCustomerLastname();
        $email = $order->getCustomerEmail();

        $billingAddress = $order->getBillingAddress();
        $phoneNumber = $billingAddress->getTelephone();
        $street = implode(', ', $billingAddress->getStreet());
        $city = $billingAddress->getCity();
        $region = $billingAddress->getRegion();
        $postalCode = $billingAddress->getPostcode();
        $countryCode = $billingAddress->getCountryId();
        $country = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();


        $items = $order->getAllVisibleItems();
        $description = array();
        $orderGrossTotal = 0;
        $orderItemsNew = array();
        foreach ($items as $item) {
            $description[] = $item->getName() . 'x' . round($item->getData('qty_ordered'));
            //$orderGrossTotal += $item->getRowTotalInclTax() - $item->getTaxAmount() - $item->getHiddenTaxAmount();

            $orderItemsNew[] = array(
                'id' => $item->getProductId(),
                'name' => $item->getName(),
                'price' => $item->getPriceInclTax()
            );
        }

        $orderGrossTotal = $order->getSubtotalInclTax()
            - $order->getTaxAmount()
            + $order->getShippingTaxAmount() // shipping tax amount must be added because was deducted from tax_amount on above line
            + $order->getDiscountAmount(); // discount amount is set with "-" in sales_flat_order

        $description = urlencode(implode(',', $description));

        $shippingCost = $order->getShippingInclTax();
        $orderItemsNew = json_encode($orderItemsNew);

        $code = str_replace(
            array(
                '{{value}}',
                '{{currency}}',
                '{{order_id}}',
                '{{order_items}}',
                '{{value_excl_tax}}',
                '{{firstname}}',
                '{{lastname}}',
                '{{email}}',
                '{{phone_number}}',
                '{{street}}',
                '{{city}}',
                '{{region}}',
                '{{postal_code}}',
                '{{country}}',
                '{{amount}}',
                '{{shipping_cost}}',
                '{{order_items_new}}',
            ),
            array(
                $total,
                $currency,
                $orderId,
                $description,
                $orderGrossTotal,
                $firstname,
                $lastname,
                $email,
                $phoneNumber,
                $street,
                $city,
                $region,
                $postalCode,
                $country,
                $orderAmount,
                $shippingCost,
                $orderItemsNew
            ),
            $code
        );

        $code .= $this->getGACode($order, $items, $orderGrossTotal, $storeId);

        return $code;
    }

    public function getConversionNecessaryCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/conversion_necessary', $storeId);

        $order = Mage::getSingleton('checkout/session')->getLastRealOrder();
        $currency = $order->getOrderCurrencyCode();
        $total = $order->getBaseGrandTotal();
        $orderId = $order->getIncrementId();
        $orderAmount = number_format($order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount() ,2);

        $firstname = $order->getCustomerFirstname();
        $lastname = $order->getCustomerLastname();
        $email = $order->getCustomerEmail();

        $billingAddress = $order->getBillingAddress();
        $phoneNumber = $billingAddress->getTelephone();
        $street = implode(', ', $billingAddress->getStreet());
        $city = $billingAddress->getCity();
        $region = $billingAddress->getRegion();
        $postalCode = $billingAddress->getPostcode();
        $countryCode = $billingAddress->getCountryId();
        $country = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();

        $items = $order->getAllVisibleItems();
        $description = array();
        $orderGrossTotal = 0;
        foreach ($items as $item) {
            $description[] = $item->getName() . 'x' . round($item->getData('qty_ordered'));
            //$orderGrossTotal += $item->getRowTotalInclTax() - $item->getTaxAmount() - $item->getHiddenTaxAmount();

            $orderItemsNew[] = array(
                'id' => $item->getProductId(),
                'name' => $item->getName(),
                'price' => $item->getPriceInclTax()
            );
        }

        $orderGrossTotal = $order->getSubtotalInclTax()
            - $order->getTaxAmount()
            + $order->getShippingTaxAmount() // shipping tax amount must be added because was deducted from tax_amount on above line
            + $order->getDiscountAmount(); // discount amount is set with "-" in sales_flat_order

        $description = urlencode(implode(',', $description));

        $shippingCost = $order->getShippingInclTax();
        $orderItemsNew = json_encode($orderItemsNew);

        $code = str_replace(
            array(
                '{{value}}',
                '{{currency}}',
                '{{order_id}}',
                '{{order_items}}',
                '{{value_excl_tax}}',
                '{{firstname}}',
                '{{lastname}}',
                '{{email}}',
                '{{phone_number}}',
                '{{street}}',
                '{{city}}',
                '{{region}}',
                '{{postal_code}}',
                '{{country}}',
                '{{amount}}',
                '{{shipping_cost}}',
                '{{order_items_new}}',
            ),
            array(
                $total,
                $currency,
                $orderId,
                $description,
                $orderGrossTotal,
                $firstname,
                $lastname,
                $email,
                $phoneNumber,
                $street,
                $city,
                $region,
                $postalCode,
                $country,
                $orderAmount,
                $shippingCost,
                $orderItemsNew
            ),
            $code
        );

        $code .= $this->getGACode($order, $items, $orderGrossTotal, $storeId);

        return $code;
    }

    public function getConversionAnalyticsCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/conversion_analytics', $storeId);

        $order = Mage::getSingleton('checkout/session')->getLastRealOrder();
        $currency = $order->getOrderCurrencyCode();
        $total = $order->getBaseGrandTotal();
        $orderId = $order->getIncrementId();
        $orderAmount = number_format($order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount() ,2);

        $firstname = $order->getCustomerFirstname();
        $lastname = $order->getCustomerLastname();
        $email = $order->getCustomerEmail();

        $billingAddress = $order->getBillingAddress();
        $phoneNumber = $billingAddress->getTelephone();
        $street = implode(', ', $billingAddress->getStreet());
        $city = $billingAddress->getCity();
        $region = $billingAddress->getRegion();
        $postalCode = $billingAddress->getPostcode();
        $countryCode = $billingAddress->getCountryId();
        $country = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();

        $items = $order->getAllVisibleItems();
        $description = array();
        $orderGrossTotal = 0;
        foreach ($items as $item) {
            $description[] = $item->getName() . 'x' . round($item->getData('qty_ordered'));
            //$orderGrossTotal += $item->getRowTotalInclTax() - $item->getTaxAmount() - $item->getHiddenTaxAmount();

            $orderItemsNew[] = array(
                'id' => $item->getProductId(),
                'name' => $item->getName(),
                'price' => $item->getPriceInclTax()
            );
        }

        $orderGrossTotal = $order->getSubtotalInclTax()
            - $order->getTaxAmount()
            + $order->getShippingTaxAmount() // shipping tax amount must be added because was deducted from tax_amount on above line
            + $order->getDiscountAmount(); // discount amount is set with "-" in sales_flat_order

        $description = urlencode(implode(',', $description));

        $shippingCost = $order->getShippingInclTax();
        $orderItemsNew = json_encode($orderItemsNew);

        $code = str_replace(
            array(
                '{{value}}',
                '{{currency}}',
                '{{order_id}}',
                '{{order_items}}',
                '{{value_excl_tax}}',
                '{{firstname}}',
                '{{lastname}}',
                '{{email}}',
                '{{phone_number}}',
                '{{street}}',
                '{{city}}',
                '{{region}}',
                '{{postal_code}}',
                '{{country}}',
                '{{amount}}',
                '{{shipping_cost}}',
                '{{order_items_new}}',
            ),
            array(
                $total,
                $currency,
                $orderId,
                $description,
                $orderGrossTotal,
                $firstname,
                $lastname,
                $email,
                $phoneNumber,
                $street,
                $city,
                $region,
                $postalCode,
                $country,
                $orderAmount,
                $shippingCost,
                $orderItemsNew
            ),
            $code
        );

        $code .= $this->getGACode($order, $items, $orderGrossTotal, $storeId);

        return $code;
    }

    public function getBeforeRemarketingCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/remarketing_before', $storeId);
        return $code;
    }

    public function getAfterRemarketingCode($storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/enabled', $storeId)) {
            return '';
        }

        $code = Mage::getStoreConfig('blugento_marketing/settings/remarketing_after', $storeId);
        return $code;
    }

    public function getGACode($order, $items, $orderGrossTotal, $storeId)
    {
        if (!Mage::getStoreConfig('blugento_marketing/settings/ga_enabled', $storeId)) {
            return '';
        }
        $affiliation = Mage::getStoreConfig('blugento_marketing/settings/ga_affiliation', $storeId);

        $orderId = $order->getIncrementId();
        $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
        $code = '<script>ga(\'ecommerce:addTransaction\', {
                  \'id\': \'' . $orderId . '\',
                  \'affiliation\': \'' . $affiliation . '\',
                  \'revenue\': \'' . $orderGrossTotal . '\',
                  \'shipping\': \'' . $shipping . '\',
                  \'tax\': \'' . $order->getTaxAmount() . '\'
                });';

        foreach ($items as $item) {
            $description[] = $item->getName() . 'x' . round($item->getData('qty_ordered'));
            $orderGrossTotal += $item->getRowTotalInclTax() - $item->getTaxAmount() - $item->getHiddenTaxAmount();

            $code .= 'ga(\'ecommerce:addItem\', {
                      \'id\': \'' . $orderId . '\',
                      \'name\': \'' . $item->getName() . '\',
                      \'sku\': \'' . $item->getSku() . '\',
                      \'category\': \'' . $this->_getProductCategoryName($item) . '\',
                      \'price\': \'' . $item->getRowTotalInclTax() . '\',
                      \'quantity\': \'' . round($item->getData('qty_ordered')) . '\'
                    });';
        }

        return $code . '</script>';

    }

    protected function _getProductCategoryName($item)
    {
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $categoryIds = $product->getCategoryIds();

        if (count($categoryIds)) {
            $firstCategoryId = $categoryIds[0];
            $_category = Mage::getModel('catalog/category')->load($firstCategoryId);

            return $_category->getName();
        }

        return '';
    }
}
