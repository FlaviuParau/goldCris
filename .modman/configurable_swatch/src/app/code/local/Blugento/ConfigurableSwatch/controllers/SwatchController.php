<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_ConfigurableSwatch
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ConfigurableSwatch_SwatchController extends Mage_Core_Controller_Front_Action
{
    public function getSwatchProductDataAction()
    {
        $productId      = $this->getRequest()->getParam('id');
        $selectedOption = $this->getRequest()->getParam('label');
        $valueIndex     = $this->getRequest()->getParam('valindex');
        $childId        = $this->getRequest()->getParam('childid');
        $defImage       = $this->getRequest()->getParam('defimage');

        $product = Mage::getModel('catalog/product')->load($productId);

        $configurablePrice = $selectedOptionPrice = $product->getPrice();
	    $configurableSpecialPrice = $selectedOptionSpecialPrice = 0;
        
        if ($product->getSpecialPrice()) {
	        $configurableSpecialPrice = $selectedOptionSpecialPrice = $product->getSpecialPrice();
        }

        /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $allowedAttributes = $typeInstance->getConfigurableAttributeCollection($product);

        $attributeId = '';
        
        foreach ($allowedAttributes as $attribute) {
            $attributeId = $attribute->getId();
            
            foreach ($attribute->getPrices() as $attributePrice) {
                $index = isset($attributePrice['value_index']) ? $attributePrice['value_index'] : null;
                if ($index == $valueIndex) {
                    $isPercent  = isset($attributePrice['is_percent']) ? $attributePrice['is_percent'] : null;
                    $priceValue = isset($attributePrice['pricing_value']) ? $attributePrice['pricing_value'] : null;

                    if (!$priceValue || $priceValue <=0) {
                        $selectedOptionPrice = $configurablePrice;
	                    $selectedOptionSpecialPrice = $configurableSpecialPrice;
                    }
                    
                    if ($isPercent) {
                        $optionPrice = $configurablePrice + ($configurablePrice * $priceValue);
                        $optionSpecialPrice = $configurableSpecialPrice + ($configurableSpecialPrice * $priceValue);
                    } else {
                        $optionPrice = $configurablePrice + $priceValue;
	                    $optionSpecialPrice = $configurableSpecialPrice + $priceValue;
                    }
                    
                    if ($optionPrice && $optionPrice >= 0) {
                        $selectedOptionPrice = Mage::helper('core')->currency($optionPrice, true, false);
                    } else {
                        $selectedOptionPrice = Mage::helper('core')->currency($configurablePrice, true, false);
                    }
	
	                if ($optionSpecialPrice && $optionSpecialPrice >= 0) {
		                $selectedOptionSpecialPrice = Mage::helper('core')->currency($optionSpecialPrice, true, false);
	                } else {
		                $selectedOptionSpecialPrice = Mage::helper('core')->currency($configurableSpecialPrice, true, false);
	                }
                }
            }
        }

        try {
            $_product      = Mage::getModel('catalog/product')->load($childId);
            $widthListImg  = Mage::getStoreConfig('blugento_configurableswatch/general/list_img_width');
            $heightListImg = Mage::getStoreConfig('blugento_configurableswatch/general/list_img_height');
	
            $imageUrl = (string)Mage::helper('catalog/image')->init($_product,'small_image')->resize($widthListImg,$heightListImg);

            if (!strpos($imageUrl, 'placeholder')) {
                $image = $imageUrl;
            } else {
                $image =  $imageUrl = (string)Mage::helper('catalog/image')->init($product,'small_image')->resize($widthListImg,$heightListImg);
            }

            $result = array(
                'error'         => 0,
                'price'         => $selectedOptionPrice,
                'special_price' => $selectedOptionSpecialPrice,
                'id'            => $productId,
                'image'         => $image
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function addToCartAction()
    {
        $result = array();
        try {
            $confProdId   = $this->getRequest()->getParam('productid');
            $attributeId  = $this->getRequest()->getParam('attributeid');
            $attributeVal = $this->getRequest()->getParam('selectedvalue');
            $qtyVal = $this->getRequest()->getParam('qtyvalue');

            $configurableProduct = Mage::getModel('catalog/product')->load($confProdId);
            $cart = Mage::getSingleton('checkout/cart');
            $cart->init();

            $params = array(
                'product' => $confProdId,
                'super_attribute' => array (
                    $attributeId => $attributeVal
                ),
                'qty' => $qtyVal,
            );
            $request = new Varien_Object();
            $request->setData($params);

            $cart->addProduct($configurableProduct, $request);
            $session = Mage::getSingleton('customer/session');
            $session->setCartWasUpdated(true);

            $product = Mage::getModel('catalog/product')->load($confProdId);
            $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
            Mage::getSingleton('catalog/session')->addSuccess($message);

            $cart->save();

        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $e->getMessage();
        }

        try {
            $result = array();
            $minicart = $this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/sidebar.phtml')->toHtml();
            $result['minicart'] = $minicart;

            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);

            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
