<?php

class IntelligentIT_SmartBill_Block_Adminhtml_Sales_Order_Renderer_Smartbill extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row)
	{
	    if (Mage::app()->getLayout()->getBlock('sales_order_edit')) {
	        $orderId = $this->getRequest()->getParam('order_id');
        } else {
	        $orderId = $row->getId();
        }

        $orderDetails = Mage::getModel('sales/order')->load($orderId);
        $url = $orderDetails->getSmartbillDocumentUrl();
        $url = Mage::helper("adminhtml")->getUrl("smartbill/documents/invoice", array('order_id'=>$orderId));
        $name = trim($orderDetails->getSmartbillDocumentSeries().' '.$orderDetails->getSmartbillDocumentNumber());
        // if (empty(trim($name))) $name = 'deschide';
        // if (trim($name)==false) $name = 'deschide';
    	$outputHTML = !empty($name) ? '<a href="'.$url.'" rel="noopener" target="_blank" title="'.$name.'">'.$name.'</a>' : '';

		return $outputHTML;
	}
}