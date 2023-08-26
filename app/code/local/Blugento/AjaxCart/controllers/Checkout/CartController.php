<?php
/**
 * Blugento AjaxCart
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_AjaxCart
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

require_once 'Mage/Checkout/controllers/CartController.php';

class Blugento_AjaxCart_Checkout_CartController extends Mage_Checkout_CartController
{
    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
    	if (!Mage::helper('blugento_ajaxcart')->isEnabled()) {
            parent::addAction();
            return false;
        }

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(array(
                    'locale' => Mage::app()->getLocale()->getLocaleCode()
                ));
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('blugento_ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_add_product_complete', array(
                'product'   => $product,
                'request'   => $this->getRequest(),
                'response'  => $this->getResponse()
            ));

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('blugento_ajaxcart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);

            $_response = Mage::getModel('blugento_ajaxcart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot add the item to shopping cart.'));
            $_response->send();
        }
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        if (!Mage::helper('blugento_ajaxcart')->isEnabled() || !$this->getRequest()->isAjax()) {
            parent::updateItemOptionsAction();
            return false;
        }

        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(array(
                    'locale' => Mage::app()->getLocale()->getLocaleCode()
                ));
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('blugento_ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_update_item_complete', array(
                'item'      => $item,
                'request'   => $this->getRequest(),
                'response'  => $this->getResponse()
            ));

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('blugento_ajaxcart/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);

            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);

            $_response = Mage::getModel('blugento_ajaxcart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot update the item.'));
            $_response->send();
        }
    }

    /**
     * Delete shopping cart item action
     */
    public function deleteAction()
    {
        if (!Mage::helper('blugento_ajaxcart')->isEnabled() || !$this->getRequest()->isAjax()) {
            parent::deleteAction();
            return false;
        }

        if ($this->_validateFormKey()) {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
	                if (Mage::helper('core')->isModuleEnabled('Blugento_TextOptions')) {
		                $resource        = Mage::getSingleton('core/resource');
		                $readConnection  = $resource->getConnection('core_read');
		                $tableName       = $resource->getTableName('sales/quote_item');
		                $relatedId       = 0;
		
		                $item = $this->_getCart()->getQuote()->getItemById($id);
		                $quoteId = $this->_getCart()->getQuote()->getItemById($id)->getQuoteId();
		
		                foreach ($item->getOptions() as $option) {
			                $_option = unserialize($option->getValue());
			
			                if (isset($_option['related_product']) && $_option['related_product'] != '') {
				                $relatedId = $_option['related_product'];
			                }
		                }
		
		                if ($relatedId != 0) {
			                $selectQuery = $readConnection->select()->from($tableName, 'item_id')
				                ->where('product_id = ?', $relatedId)
				                ->where('quote_id = ?', $quoteId);
			
			                $itemId = $readConnection->fetchOne($selectQuery);
			
			                $this->_getCart()
				                ->removeItem((int) $itemId)
				                ->removeItem($id)
				                ->save();
		                } else {
			                $this->_getCart()
				                ->removeItem($id)
				                ->save();
		                }
	                } else {
		                $this->_getCart()
			                ->removeItem($id)
			                ->save();
	                }
                } catch (Exception $e) {
                    $_response = Mage::getModel('blugento_ajaxcart/response');
                    $_response->setError(true);
                    $_response->setMessage($this->__('Cannot remove the item.'));
                    $_response->send();

                    Mage::logException($e);
                }
            }
        } else {
            $_response = Mage::getModel('blugento_ajaxcart/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot remove the item.'));
            $_response->send();
        }

        $_response = Mage::getModel('blugento_ajaxcart/response');
        $_response->setMessage($this->__('Item was removed successfully.'));

        // check minimum order amount and add in response if condition is false
        if (!$this->_getQuote()->validateMinimumAmount()) {
            $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

            $warning = Mage::getStoreConfig('sales/minimum_order/description')
                ? Mage::getStoreConfig('sales/minimum_order/description') . Mage::helper('checkout')->__('<div>Click <a href="%s">here</a> to adjust cart items or quantity!</div>', Mage::getUrl('checkout/cart'))
                : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount) . Mage::helper('checkout')->__('<div>Click <a href="%s">here</a> to adjust cart items or quantity!</div>', Mage::getUrl('checkout/cart'));

            $_response->setMinOrderMessage($warning);
        }
	
	    if (Mage::helper('core')->isModuleEnabled('Blugento_GoogleTagManager') && Mage::helper('blugento_googletagmanager')->isEnabled()) {
		    /** @var Mage_Catalog_Model_Product $product */
		    $product = $this->_getQuote()->getItemById($id)->getProduct();
		    /** @var $model Blugento_GoogleTagManager_Model_Request */
		    $model   = Mage::getModel('blugento_googletagmanager/request');
		
		    $product             = $model->getProductInfo($product);
		    $product['quantity'] = $this->_getQuote()->getItemById($id)->getQty();
		    
		    $_response->setProductQuoteRemoveItem($product);
		    
		    unset($product);
	    }

        if (Mage::helper('core')->isModuleEnabled('Blugento_GoogleTag') && Mage::helper('blugento_googletag')->isEnabled()) {
             /** @var Mage_Catalog_Model_Product $product */
		    $product = $this->_getQuote()->getItemById($id)->getProduct();
            /** @var $model Blugento_GoogleTag_Model_Request */
            $model = Mage::getModel('blugento_googletag/request');

            $gtagProduct             = $model->getProductInfo($product);
            $gtagProduct['quantity'] = $this->_getQuote()->getItemById($id)->getQty();

            $_response->setGtagProductQuoteRemoveItem($gtagProduct);

            unset($gtagProduct);
        }

        // append updated blocks
        $this->getLayout()->getUpdate()->addHandle('blugento_ajaxcart');
        $this->loadLayout();
	
	    if (Mage::helper('core')->isModuleEnabled('Me_Lff') && Mage::helper('me_lff')->isFreeShippingNotificationAvailable()) {
		    $layout = $this->getLayout();
		    $block = $layout->getBlock('checkout.cart.form.before');
		    if ($block) {
			    $infoBlock = $layout->createBlock('me_lff/checkout_cart_leftamountinfo')
				    ->setName('me.lff.amount.info')
				    ->setTemplate('me/lff/checkout/cart/left-amount-info.phtml');
			    $block->append($infoBlock);
		    }
	    }

        $_response->addUpdatedBlocks($_response);
        $_response->send();
    }

    /**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        if (!Mage::helper('blugento_ajaxcart')->isEnabled() || !$this->getRequest()->isAjax()) {
            parent::updatePostAction();
            return false;
        }

        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return false;
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        $_response = Mage::getModel('blugento_ajaxcart/response');

        //check minimum order amount and add in response if condition is false
        if (!$this->_getQuote()->validateMinimumAmount()) {
            $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

            $warning = Mage::getStoreConfig('sales/minimum_order/description')
                ? Mage::getStoreConfig('sales/minimum_order/description')
                : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

            $_response->setMinOrderMessage($warning);
        }
	
	    $shippingMethodCode = $this->_getLowestShippingMethodCode($this->_getQuote()->getId());
     
	    if (
	    	Mage::helper('blugento_cart')->isShippingPriceCartEnabled(Mage::app()->getStore()->getStoreId()) &&
		    $this->_getQuote()->getId() &&
		    $shippingMethodCode
	    ) {
		    $this->_getQuote()->getShippingAddress()->setShippingMethod($shippingMethodCode)->save();
		    $this->_getQuote()->setTotalsCollectedFlag(false)->collectTotals();
	    }

        // append updated blocks
        $this->getLayout()->getUpdate()->addHandle('blugento_ajaxcart');
        $this->loadLayout();
	
	    if (Mage::helper('core')->isModuleEnabled('Me_Lff') && Mage::helper('me_lff')->isFreeShippingNotificationAvailable()) {
		    $layout = $this->getLayout();
		    $block = $layout->getBlock('checkout.cart.form.before');
		    if ($block) {
			    $infoBlock = $layout->createBlock('me_lff/checkout_cart_leftamountinfo')
				    ->setName('me.lff.amount.info')
				    ->setTemplate('me/lff/checkout/cart/left-amount-info.phtml');
			    $block->append($infoBlock);
		    }
	    }

        $_response->addUpdatedBlocks($_response);
        $_response->send();
    }
	
	/**
	 * Initialize coupon
	 */
	function couponPostAction()
	{
		$_response = Mage::getModel('blugento_ajaxcart/response');
		
		$this->handleCoupon($_response);
		
		// append updated blocks
		$this->getLayout()->getUpdate()->addHandle('blugento_ajaxcart');
		$this->loadLayout();
		
		$_response->addUpdatedBlocks($_response);
		$_response['checkout_coupon'] = false;
		
		$_response->send();
	}
	
	private function handleCoupon($response) {
		if (!Mage::helper('blugento_ajaxcart')->isEnabled() || !$this->getRequest()->isAjax()) {
			parent::couponPostAction();
			return false;
		}
		
		if (!$this->_getCart()->getQuote()->getItemsCount()) {
			$this->_goBack();
			return false;
		}
		
		$couponCode = (string) $this->getRequest()->getParam('coupon_code');
		
		if ($this->getRequest()->getParam('remove') == 1) {
			$couponCode = '';
		}
		
		$oldCouponCode = $this->_getQuote()->getCouponCode();
		
		if (!strlen($couponCode) && !strlen($oldCouponCode)) {
			$msg = $this->__('This is a required field.');
			$response->setMessage($msg);
			$response->send();
		}
		
		try {
			$codeLength = strlen($couponCode);
			$isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
			
			$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
				->collectTotals()
				->save();
			
			$shippingMethodCode = $this->_getLowestShippingMethodCode($this->_getQuote()->getId());
			
			if (
				Mage::helper('blugento_cart')->isShippingPriceCartEnabled(Mage::app()->getStore()->getStoreId()) &&
				$this->_getQuote()->getId() &&
				$shippingMethodCode
			) {
				$this->_getQuote()->getShippingAddress()->setShippingMethod($shippingMethodCode)->setCollectShippingRates(true);
				$this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')->save();
				$this->_getQuote()->setTotalsCollectedFlag(false)->collectTotals();
			}
			
			if ($couponCode) {
				if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
					$msg = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode));
					$response->setMessage($msg);
				} else {
					$msg = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
					$response->setMessage($msg);
				}
			} else {
				$msg = $this->__('Coupon code was canceled.');
				$response->setMessage($msg);
			}
		} catch (Mage_Core_Exception $e) {
			$response->setMessage($this->__('Cannot apply the coupon code.'));
			$response->send();
		} catch (Exception $e) {
			$response->setMessage($this->__('Cannot apply the coupon code.'));
			$response->send();
			Mage::logException($e);
		}
		
		return true;
	}
	
	/**
	 * Return shipping method code with lowest price
	 *
	 * @param int $quoteId
	 * @return string
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _getLowestShippingMethodCode($quoteId)
	{
		$storeId = Mage::app()->getStore()->getStoreId();
		$helper = Mage::helper('blugento_cart');
		$configShippingMethods = $helper->getShippingMethodsCode($storeId);
		$shippingMethods = Mage::getModel('checkout/cart_shipping_api')->getShippingMethodsList($quoteId, $storeId);
		
		if ($configShippingMethods != 9999) {
			$codes = explode(',', $configShippingMethods);
			$methods = [];
			
			foreach ($shippingMethods as $shMethod) {
				foreach ($codes as $code) {
					if ($shMethod['code'] == $code) {
						$methods[] = $shMethod;
					}
                }
            }
			
            $shippingMethods = $methods;
        }
		
		$methodCode = '';
		$minPrice = $shippingMethods[0]['price'];
		
		foreach ($shippingMethods as $key => $method) {
			if ($method['price'] <= $minPrice) {
				$methodCode = $method['code'];
			}
		}
		
		return $methodCode;
	}
}
