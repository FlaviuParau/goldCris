<?php

class TM_FacebookLB_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected $_config = array();

    protected $_mapping = array(
            'send'      => 'data-share',
            'layout'    => 'data-layout',
            'showfaces' => 'data-show-faces',
            'width'     => 'data-width',
            'verb'      => 'data-action',
            'color'     => 'data-colorscheme'
        );

    public function getCategoryLikeButton($product)
    {
        $oldUrl = $product->getData('url');
        $oldRequestPath = $product->getData('request_path');
        $product->setData('url', '');
        $product->setData('request_path', '');

        $params = array('_ignore_category' => true);
        $url = $product->getUrlModel()->getUrl($product, $params);

        $product->setData('url', $oldUrl);
        $product->setData('request_path', $oldRequestPath);

        return $this->renderLikeButton($url, 'category_products');
    }

    public function renderLikeButton($url, $configKey)
    {
        if (!Mage::getStoreConfig('blugento_socialmedia/like_general/enabled')) {
            return '';
        }
        $config = $this->getConfig($configKey);
        $html = '';
        if (isset($config['enabled']) && $config['enabled'] == 'true') {
            $dataAttr = '';
            foreach ($this->_mapping as $key => $value) {
                if (!isset($config[$key])) {
                    continue;
                }
                $dataAttr .= sprintf(' %s="%s"', $value, $config[$key]);
            }
            $html = '<div class="fb-like" data-href="' . $url . '"'. $dataAttr .'></div>';
        }
        return $html;
    }

    public function getConfig($parentConfigKey)
    {
        if (!isset($this->_config[$parentConfigKey])) {
            $config = Mage::getStoreConfig('blugento_socialmedia/' . $parentConfigKey);
            if ($config['layout'] != 'standard') {
                unset($config['showfaces'], $config['width']);
            }
            // transporm config to facebook values
            foreach ($config as $key => $value) {
                switch ($value) {
                    case '1':
                        $config[$key] = 'true';
                        break;

                    case '0':
                        $config[$key] = 'false';
                        break;
                }
            }
            $this->_config[$parentConfigKey] = $config;
        }
        return $this->_config[$parentConfigKey];
    }
}
