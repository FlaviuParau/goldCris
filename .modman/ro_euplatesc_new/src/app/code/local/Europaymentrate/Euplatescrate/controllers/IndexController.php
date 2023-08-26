<?php

class europaymentrate_euplatescrate_IndexController extends Mage_Core_Controller_Front_Action
{

    protected $_order;

    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function getStandard()
    {
        return Mage::getSingleton('euplatescrate/standard');
    }


    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->seteuplatescrateStandardQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('euplatescrate/redirect')->toHtml());
        $session->unsQuoteId();
    }

    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->geteuplatescrateStandardQuoteId(true));

        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }

        $this->_redirect('checkout/cart');
    }

    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->geteuplatescrateStandardQuoteId(true));

        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }


    public function ipnAction()
    {
		if(isset($_GET['success'])){
			$this->_redirect('checkout/onepage/success');
			return;
		}
		
        if (!$this->getRequest()->isPost() and !isset($_GET['epdev'])) {
        //if (!$this->getRequest()->isPost()) {
           $this->_redirect('');
           return;
        }
	    Mage::getSingleton('euplatescrate/standard')->setIpnFormData($this->getRequest()->getPost());
	    $this->getResponse()->setBody($this->getStandard()->ipnPostSubmit());
    }
}
