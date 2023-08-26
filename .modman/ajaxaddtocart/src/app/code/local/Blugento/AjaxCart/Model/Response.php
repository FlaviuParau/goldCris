<?php
/**
 * Blugento AjaxCart
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_AjaxCart
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_AjaxCart_Model_Response extends Mage_Catalog_Block_Product_Abstract
{
    public function send()
    {
        Zend_Json::$useBuiltinEncoderDecoder = true;

        if ($this->getError()) {
            $this->setR('error');
        } else {
            $this->setR('success');
        }

        Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($this->getData()));
        Mage::app()->getFrontController()->getResponse()->sendResponse();

        die;
    }

    public function addUpdatedBlocks(&$_response)
    {
        $layout = Mage::getSingleton('core/layout');

        $updates = array(
            '.block.block-cart' => 'cart_sidebar',
            '.header .links' => 'top.links',
            '.cart' => 'checkout.cart',
            '.header-minicart' => 'minicart_head'
        );

        $result = array();

        foreach ($updates as $k => $v) {
            if ($block = $layout->getBlock($v)) {
                $result[] = array(
                    'key'   => $k,
                    'value' => $block->toHtml()
                );
            }
        }

        if (!empty($result)) {
            $_response->setUpdateBlocks($result);
        }
    }

    public function addConfigurableOptionsBlock(&$_response)
    {
        $layout = Mage::getSingleton('core/layout');
        $result = '';
        $_product = Mage::registry('current_product');

        $layout->getUpdate()->addHandle('blugento_ajaxcart_configurable_options');

        if ($_product->getTypeId() == 'bundle') {
            $layout->getUpdate()->addHandle('blugento_ajaxcart_bundle_options');
        }

        // set unique cache ID to bypass caching
        $cacheId = 'LAYOUT_' . Mage::app()->getStore()->getId() . md5(join('__', $layout->getUpdate()->getHandles()));

        $layout->getUpdate()->setCacheId($cacheId);
        $layout->getUpdate()->load();
        $layout->generateXml();
        $layout->generateBlocks();

        $value = $layout->getBlock('blugento_ajaxcart.configurable.options');

        if ($value) {
            $result .= $value->toHtml();
        }
        
        if ($_product->getTypeId() == 'bundle') {
            $value = $layout->getBlock('product.info.bundle');

            if ($value) {
                $result .= $value->toHtml();
            }
        }

        if (!empty($result)) {
            $_response->setConfigurableOptionsBlock($result);
        }
    }

    public function addGroupProductItemsBlock(&$_response)
    {
        $layout = Mage::getSingleton('core/layout');
        $result = '';

        $layout->getUpdate()->addHandle('blugento_ajaxcart_grouped_options');

        // set unique cache ID to bypass caching
        $cacheId = 'LAYOUT_' . Mage::app()->getStore()->getId() . md5(join('__', $layout->getUpdate()->getHandles()));
        $layout->getUpdate()->setCacheId($cacheId);

        $layout->getUpdate()->load();
        $layout->generateXml();
        $layout->generateBlocks();

        $value = $layout->getBlock('blugento_ajaxcart.grouped.options');

        if ($value) {
            $result .= $value->toHtml();
        }

        if (!empty($result)) {
            $_response->setConfigurableOptionsBlock($result);
        }
    }
}
