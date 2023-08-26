<?php

class IntelligentIT_SmartBill_Model_ObserverCore
{
	public function handle_adminHTMLBlockBefore($observer)
	{
	    $block = $observer->getBlock();

	    // action button
	    if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
	        $orderId 	= $block->getRequest()->getParam('order_id');
	        $order 		= Mage::getModel('sales/order')->load($orderId);
	        $mainLabel 	= Mage::helper('sales')->__('Emite in Smart Bill');

	        $storeID 	= Mage::app()->getStore()->getStoreId();
	        if ( empty($storeID) ) {
	        	// get store ID from order
	        	$storeID = $order->getStoreId();

	        	// set store ID as default
	        	// $allStores = Mage::app()->getStores();
	        	// $storeID = array_shift(array_keys($allStores));
	        }
	        $storeID 	= empty($storeID) ? 1 : $storeID;
	        $url 		= $order->getSmartbillDocumentUrl();
	        $urlAction 	= $block->getUrl('smartbill/documents/invoice/', array('_store' => $storeID));

	        if (!empty($url)) {
	            $mainLabel = Mage::helper('sales')->__('Vizualizeaza in Smart Bill');
	        }

	        $block->addButton('smc_invoice', array(
	            'label'     => $mainLabel,
	            // 'onclick'   => 'alert('.$this->getOrder()->getRealOrderId().');',
	            // 'onclick'   => "setLocation('".$this->getUrl('smartbill/documents/invoice/')."')",
	            'onclick'   => "if(window.navigator.userAgent.indexOf('Firefox')>0) { smcdocwin = window.open('".$block->getUrl('smartbill/documents/')."', 'smcdoc', ''); smcdocwin.close(); popWin('".$urlAction."', 'smcdoc', window.navigator.userAgent.indexOf('MSIE ')>0 ? 'location=yes,resizable=yes,scrollbars=yes,titlebar=yes,toolbar=yes' : ''); } else { popWin('".$urlAction."', 'smcdoc', window.navigator.userAgent.indexOf('MSIE ')>0 ? 'location=yes,resizable=yes,scrollbars=yes,titlebar=yes,toolbar=yes' : ''); }",
	            'class'     => 'smcdoc'
	        ), 0, 800, 'header', 'header');
	    }

	    // mass acton record
        if ( $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
          && $block->getRequest()->getControllerName() == 'sales_order' )
        {
        	$token = Mage::getStoreConfig(IntelligentIT_SmartBill_Helper_Public::XML_PATH_SMARTBILL_TOKEN);
	        if ( !empty($token) )
	        {
	            $block->addItem('smartbill', array(
	                'label' => 'Trimite erori Smart Bill',
	                'url'   => Mage::app()->getStore()->getUrl('smartbill/documents/debug'),
	            ));
	        }
        }

        // extra column
	    if ( $block instanceof Mage_Adminhtml_Block_Widget_Grid
	      && $block->getRequest()->getControllerName() == 'sales_order' )
	    {
	        $block->addColumn('smartbill_document', array(
	            'header'    => 'SmartBill #',
	            'type'      => 'text',
	            'width'     => '75px',
	            // 'index'     => 'entity_id',
	            'renderer'  => 'IntelligentIT_SmartBill_Block_Adminhtml_Sales_Order_Renderer_Smartbill',
	            'filter'    => false,
	            'sortable'  => false,
	        ));
	        $block->addColumnsOrder('entity_id','smartbill_document');
	    }
	}
}