<?php

class Blugento_Erpprocess_Block_Customer_Link extends Mage_Core_Block_Template
{
    protected $_orderId;

    protected function _construct()
    {
        $this->_orderId = $this->getRequest()->getParam('order_id');
    }

    /**
     * Return invoice download url.
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getURL($route = '', $params = array())
    {
        $orderUrl = Mage::getModel('sales/order')->load($this->_orderId)->getInvoiceDownloadUrl();

        if ($orderUrl != null && $orderUrl != '') {
            $orderUrl = Mage::getBaseUrl('media') . $orderUrl;
        }

        return $orderUrl;
    }

    /**
     * Return AWB number.
     *
     * @return string
     */
    protected function getAwb()
    {
        return Mage::getModel('sales/order')->load($this->_orderId)->getAwbNumber();
    }

    /**
     * Return AWB url.
     *
     * @param string $awb
     * @return string
     */
    protected function getAwbUrl($awb)
    {
        return $awb;
    }

    /**
     * Return AWB label text.
     *
     * @return string
     */
    protected function getAwbLabel()
    {
        return Mage::helper('blugento_erpprocess')->__('AWB Number:');
    }
}

