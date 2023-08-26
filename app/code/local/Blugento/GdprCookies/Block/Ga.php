<?php

class Blugento_GdprCookies_Block_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    protected function _getPageTrackingCodeUniversal($accountId)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return "
ga('create', '{$this->jsQuoteEscape($accountId)}', 'auto');
" . $this->_getAnonymizationCode() . "
ga('send', 'pageview');

ga('set', 'userId', '{$this->getUserId()}');
";
        }

        return "
ga('create', '{$this->jsQuoteEscape($accountId)}', 'auto');
" . $this->_getAnonymizationCode() . "
ga('send', 'pageview');
";
    }

    protected function _getPageTrackingCodeAnalytics($accountId)
    {
        return "gtag('event', 'page_view', {
            send_to: '{$this->jsQuoteEscape($accountId)}',
          });\n";
    }

    protected function _getOrdersTrackingCodeAnalytics()
    {
        $orderIds = $this->getOrderIds();

        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = array();
        $variables = array();
        $items = array();

        foreach ($collection as $order) {
            if ($order->getIsVirtual()) {
                $address = $order->getBillingAddress();
            } else {
                $address = $order->getShippingAddress();
            }

            $variables = array(
                'transaction_id' => $order->getIncrementId(),
                'affiliation' => $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                'value' => $order->getBaseGrandTotal(),
                'tax' => $order->getBaseTaxAmount(),
                'shipping' => $order->getBaseShippingAmount(),
            );

            foreach ($order->getAllVisibleItems() as $item) {
                $items[] = array(
                    'id' => $this->jsQuoteEscape($item->getSku()),
                    'name' => $this->jsQuoteEscape($item->getName()),
                    'price' => $item->getBasePrice(),
                    'quantity' => $item->getQtyOrdered(),
                    'category' =>  null, // there is no "category" defined for the order item
                );
            }
        }

        $variables['items'] = $items;
        $result[] = sprintf("gtag('event', 'purchase', %s);\n", json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        Mage::log(implode("\n", $result), null, 'd123.log');
        return implode("\n", $result);
    }

    private function getUserId()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        return $customer->getId();
    }
}
