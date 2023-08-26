<?php
/**
 * Blugento AjaxCart
 * Observer Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_AjaxCart
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_AjaxCart_Model_Observer
{
    public function addToCartEvent($observer)
    {
        if (!Mage::helper('blugento_ajaxcart')->isEnabled()) {
            return false;
        }

        $request = Mage::app()->getFrontController()->getRequest();
        $product = $observer->getProduct();
        $price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);

        if (!$request->getParam('in_cart') && !$request->getParam('is_checkout')) {
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

            $_response = Mage::getModel('blugento_ajaxcart/response')
                ->setImage($product->getImageUrl())
                ->setPrice(Mage::helper('core')->currency($price, true, false))
                ->setName($product->getName())
                ->setMessage(Mage::helper('checkout')->__('%s was added to your shopping cart.', $product->getName()));

            if (Mage::helper('blugento_ajaxcart')->isSimpleProductImageEnabled() && $product->getTypeId() == 'configurable') {
	            $childProductImage = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->getSku())->getImageUrl();
	            $_response->setImage($childProductImage);
            }
            
            // append updated blocks
            $_response->addUpdatedBlocks($_response);
	
	        if (Mage::helper('core')->isModuleEnabled('Blugento_GoogleTagManager') && Mage::helper('blugento_googletagmanager')->isEnabled()) {
		        /** @var $model Blugento_GoogleTagManager_Model_Request */
		        $model                  = Mage::getModel('blugento_googletagmanager/request');
		        $gtmProduct             = $model->getProductInfo($product);
		        $gtmProduct['quantity'] = Mage::app()->getRequest()->getParam('qty') ?: '1';
	        	
		        $_response->setProductQuoteAddItem($gtmProduct);
		        $_response->setCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode());

		        unset($gtmProduct);
	        }

            if (Mage::helper('core')->isModuleEnabled('Blugento_TikTok') && Mage::helper('blugento_tiktok')->isEnabled()) {
                /** @var $model Blugento_TikTok_Model_Request */
                $model = Mage::getModel('blugento_tiktok/request');

                $ttqProduct             = $model->getProductInfo($product);
                $ttqProduct['quantity'] = Mage::app()->getRequest()->getParam('qty') ?: '1';

                $_response->setTtqProductQuoteAddItem($ttqProduct);
                $_response->setCurrencyCode(Mage::app()->getStore()->getCurrentCurrencyCode());

                unset($ttqProduct);
            }

            if (Mage::helper('core')->isModuleEnabled('Blugento_GoogleTag') && Mage::helper('blugento_googletag')->isEnabled()) {
                /** @var $model Blugento_GoogleTag_Model_Request */
                $model = Mage::getModel('blugento_googletag/request');

                $gtagProduct             = $model->getProductInfo($product);
                $gtagProduct['quantity'] = (float) Mage::app()->getRequest()->getParam('qty') ?: 1;

                $_response->setGtagProductQuoteAddItem($gtagProduct);

                unset($gtagProduct);
            }

            if (Mage::helper('core')->isModuleEnabled('Blugento_Remarketing') && Mage::helper('blugento_remarketing')->isEnabled()) {
                /** @var Blugento_Remarketing_Helper_Data _drHelper */
                $_drHelper = Mage::helper('blugento_remarketing');
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                $_product['id']    = $product->getId();
                $_product['price'] = $product->getFinalPrice() * $product->getQty();
                $_product['qty']   = Mage::app()->getRequest()->getParam('qty') ?: '1';

                $_response->setRemarketingProductQuoteItem($_product);
                $_response->setDynamicMarketingAccountId($_drHelper->getAwAccountId());
                $_response->setCustomerId($customer->getId());

                unset($_product);
            }

            if (Mage::helper('core')->isModuleEnabled('Cadence_Fbpixel') && Mage::helper('cadence_fbpixel')->isVisitorPixelEnabled() && Mage::helper('cadence_fbpixel')->getVisitorPixelId() && Mage::helper('cadence_fbpixel')->isAddToCartPixelEnabled()) {
                $session = Mage::getSingleton('cadence_fbpixel/session');

                $fbqProduct['content_ids'] = array($product->getId());
                $fbqProduct['value'] = $product->getFinalPrice() * $product->getQty();
                $fbqProduct['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
                $fbqProduct['external_id'] = Mage::getSingleton('core/session')->getFbExternalId();

                $_response->setFbqProductQuoteItem($fbqProduct);

                unset($fbqProduct);
            }

            $_response->send();
        }

        if ($request->getParam('is_checkout')) {
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

            $_response = Mage::getModel('blugento_ajaxcart/response')
                ->setImage($product->getImageUrl())
                ->setPrice(Mage::helper('core')->currency($price, true, false))
                ->setName($product->getName())
                ->setMessage(Mage::helper('checkout')->__('%s was added to your shopping cart.', $product->getName()));

            $_response->send();
        }
    }

    public function updateItemEvent($observer)
    {
	    if (!Mage::helper('blugento_ajaxcart')->isEnabled()) {
		    return false;
	    }
	    
        $request = Mage::app()->getFrontController()->getRequest();

        if (!$request->getParam('in_cart') && !$request->getParam('is_checkout')) {
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

            $_response = Mage::getModel('blugento_ajaxcart/response')
                ->setMessage(Mage::helper('checkout')->__('Item was updated successfully.'));

            // append updated blocks
            $_response->addUpdatedBlocks($_response);

            $_response->send();
        }

        if ($request->getParam('is_checkout')) {
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
	
	        $product = $observer->getProduct();
	        $price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
            
            $_response = Mage::getModel('blugento_ajaxcart/response')
                ->setImage($product->getImageUrl())
                ->setPrice(Mage::helper('core')->currency($price, true, false))
                ->setName($product->getName())
                ->setMessage(Mage::helper('checkout')->__('Item was updated successfully.'));

            $_response->send();
        }
    }

    public function getConfigurableOptions($observer)
    {
        $is_ajax = Mage::app()->getFrontController()->getRequest()->getParam('ajax');

        if ($is_ajax) {
            $_response = Mage::getModel('blugento_ajaxcart/response');

            $product = Mage::registry('current_product');
            if (!$product->isConfigurable() && !($product->getTypeId() == 'bundle')) {
                return false;
            }

            // append configurable options block
            $_response->addConfigurableOptionsBlock($_response);

            $_response->send();
        }

        return false;
    }

    public function getGroupProductOptions($observer)
    {
        $id = Mage::app()->getFrontController()->getRequest()->getParam('product');
        $options = Mage::app()->getFrontController()->getRequest()->getParam('super_group');

        if ($id) {
            $product = Mage::getModel('catalog/product')->load($id);

            if ($product->getData()) {
                if ($product->getTypeId() == 'grouped' && !$options) {
                    $_response = Mage::getModel('blugento_ajaxcart/response');

                    Mage::register('product', $product);
                    Mage::register('current_product', $product);

                    // add group product's items block
                    $_response->addGroupProductItemsBlock($_response);

                    $_response->send();
                }
            }
        }
    }
}
