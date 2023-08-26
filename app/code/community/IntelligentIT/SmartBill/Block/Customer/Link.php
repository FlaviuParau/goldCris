<?php

class IntelligentIT_Smartbill_Block_Customer_Link extends Mage_Core_Block_Template
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
        $invoiceUrl = Mage::getModel('sales/order')->load($this->_orderId)->getSmartbillExternalDocumentUrl();

        return $invoiceUrl = isset($invoiceUrl) && $invoiceUrl != null ? $invoiceUrl : null;
    }
}

