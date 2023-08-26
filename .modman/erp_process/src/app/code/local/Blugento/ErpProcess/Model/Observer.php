<?php

class Blugento_Erpprocess_Model_Observer
{

    public function addDownloadInvoiceAction($observer)
    {
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        if (!$block) {
            return $this;
        }
        $order = Mage::registry('current_order');

        $invoiceURL = $order->getInvoiceDownloadUrl();
        $awbNumber = $order->getAwbNumber();

        if ($invoiceURL != null && $invoiceURL != '') {
            //$block->removeButton('order_invoice');
            $invoiceURL = Mage::getBaseUrl('media') . $invoiceURL;
            $block->addButton('cygtest_resubmit', array(
                'label'     => Mage::helper('sales')->__('List Invoice'),
                'onclick'   => 'window.open(\'' . $invoiceURL . '\')',
                'class'     => 'go'
            ));
        }
        return $this;
    }
}
