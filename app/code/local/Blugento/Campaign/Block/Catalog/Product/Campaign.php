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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Block_Catalog_Product_Campaign extends Mage_Core_Block_Template
{
    private $_campaign;
    private $_products;
    private $_class;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        if (!Mage::getStoreConfig('blugento_campaign/general/enabled')) {
            return parent::_construct();
        }

        if ($cachedProducts = Mage::app()->getCache()->load('bg_cmp_products')) {
            $this->_products = unserialize($cachedProducts);
        } else {
            if (!$this->_campaign) {
                $this->_setCampaign();
            }
        }

        if ($cachedClass = Mage::app()->getCache()->load('bg_cmp_class')) {
            $this->_class = unserialize($cachedClass);
        } else {
            if (!$this->_campaign) {
                $this->_setCampaign();
            }
        }
    }

    /**
     * Return campaign code as a html class
     *
     * @return string
     */
    public function getClassName()
    {
        if (!$this->_class) {
            $this->_class = $this->_campaign->getCode();

            $this->_cacheCampaignData($this->_campaign->getCode(), 'bg_cmp_class');
        }

        return $this->_class;
    }

    /**
     * Return all the products that are in a campaign
     *
     * @return array
     */
    public function getCampaignProducts()
    {
        if (!$this->_products) {
            /** @var Blugento_Campaign_Model_Category $categoryModel */
            $categoryModel = Mage::getModel('blugento_campaign/category');

            if ($this->_campaign->getId()) {
                $products = $categoryModel->getCampaignProducts($this->_campaign->getAssociatedCategory());

                if (count($products) > 0) {
                    $this->_cacheCampaignData($products, 'bg_cmp_products');
                }

                $this->_products = $products;
            } else {
                $this->_products = array();
            }
        }

        return $this->_products;
    }

    /**
     * Set campaign
     */
    private function _setCampaign()
    {
        /** @var Blugento_Campaign_Model_Campaign $campaign */
        $campaign = Mage::getModel('blugento_campaign/campaign');

        $campaign->getActiveCampaign();

        $this->_campaign = $campaign;
    }

    /**
     * Cache campaign data
     *
     * @param array $products
     * @param string $cacheId
     */
    private function _cacheCampaignData($products, $cacheId)
    {
        try {
            $cacheLifetime = Mage::getStoreConfig('blugento_campaign/general/cache_lifetime');
            $cacheLifetime = $cacheLifetime ? $cacheLifetime : 3600;

            Mage::app()->getCache()->save(
                serialize($products),
                $cacheId,
                array(Blugento_Campaign_Helper_Data::CACHE_TAG),
                $cacheLifetime
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
