<?php

class SMSLink_SMSGateway_Adminhtml_SMSGatewayController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('customer/customer')
			->_addBreadcrumb(Mage::helper('smsgateway')->__('Items Manager'), Mage::helper('smsgateway')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
	
		$this->_initAction();
		$this->loadLayout()
			->_setActiveMenu('customer/customer')
			->renderLayout();
	}
	
}