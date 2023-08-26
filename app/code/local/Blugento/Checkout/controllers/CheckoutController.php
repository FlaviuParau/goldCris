<?php

class Blugento_Checkout_CheckoutController extends Mage_Checkout_Controller_Action
{
    /**
     * Save checkout discount coupon
     */
    public function saveCouponAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $couponCode = (string) $this->getRequest()->getParam('coupon_code');
            
            if ($this->getRequest()->getParam('remove') == 1) {
                $couponCode = '';
            }
            
            $oldCouponCode = $this->getOnepage()->getQuote()->getCouponCode();

            if (!strlen($couponCode) && $this->getRequest()->getParam('remove') == 0 && !strlen($oldCouponCode)) {
                $result = array(
                	'error' => 1,
	                'error_field' => 'coupon_code',
	                'message' => $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                );
                
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }

            try {
                $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getOnepage()->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                    ->collectTotals()
                    ->save();

                if ($couponCode) {
                    if ($couponCode == $this->getOnepage()->getQuote()->getCouponCode()) {
	                    $result = array(
		                    'success' => 1,
		                    'message' => $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
	                    );
                    } else {
                        $result = array(
                        	'warning' => 1,
	                        'message' => $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                        );
                    }
                } else {
	                $result = array(
		                'warning' => 1,
		                'message' => $this->__('Coupon code was canceled.')
	                );
                }
            } catch (Mage_Core_Exception $e) {
                $result = array('error' => 1, 'message' => $e->getMessage());
            } catch (Exception $e) {
                $result = array('error' => 1, 'message' => $this->__('Cannot apply the coupon code.'));
            }
            
            if (!isset($result['error'])) {
                $result['active_coupon_code'] = $this->getOnepage()->getQuote()->getCouponCode();
                
                $this->loadLayout('checkout_onepage_review');

                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
	
	/**
	 * Validate ajax request and redirect on failure
	 *
	 * @return bool
	 */
	private function _expireAjax()
	{
		if (
			!$this->getOnepage()->getQuote()->hasItems()
			|| $this->getOnepage()->getQuote()->getHasError()
			|| $this->getOnepage()->getQuote()->getIsMultiShipping()
		) {
			$this->_ajaxRedirectResponse();
			
			return true;
		}
		
		$action = $this->getRequest()->getActionName();
		
		if (
			Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
			&&  !in_array($action, array('index', 'progress'))
		) {
			$this->_ajaxRedirectResponse();
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get one page checkout model
	 */
	private function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}
	
	/**
	 * Send Ajax redirect response
	 */
	private function _ajaxRedirectResponse()
	{
		$this->getResponse()
			->setHeader('HTTP/1.1', '403 Session Expired')
			->setHeader('Login-Required', 'true')
			->sendResponse();
		
		return $this;
	}
	
	/**
	 * Get order review step html
	 *
	 * @return string
	 */
	private function _getReviewHtml()
	{
		$reviewHtml = $this->getLayout()->getBlock('root')->toHtml();
		
		if (in_array('checkout_onepage_review', $this->getLayout()->getUpdate()->getHandles())) {
			$reviewBlock = $this->getLayout()->getBlock('root');
			$reviewBlock->setTemplate('checkout/onepage/review/info.phtml');
			$agreementsBlock = $this->getLayout()->createBlock('checkout/agreements', 'agreements', array('template' => 'checkout/onepage/agreements.phtml'));
			$reviewHtml = $reviewBlock->append($agreementsBlock)->toHtml();
		}
		
		return $reviewHtml;
	}
}
