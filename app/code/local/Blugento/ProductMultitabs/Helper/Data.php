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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blugento_ProductMultitabs_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_model;

    public function __construct()
    {
        $this->_model = $this->_model = Mage::getModel('blugento_productmultitabs/multitabs');;
    }

    /**
     * Unset default tabs if are disabled.
     *
     * @param array $tabs
     * @return array
     */
    public function processTabs($tabs)
    {
        $defaultTabs = $this->_model->getDefaultTabs();

        foreach ($defaultTabs as $tab) {
            if ($tab['status'] == 0 && isset($tabs[$tab['identifier']])) {
                unset($tabs[$tab['identifier']]);
            }
        }

        return $tabs;
    }

    /**
     * Return custom tabs.
     *
     * @param mixed $product
     * @param int $storeId
     * @return array
     */
    public function getCustomTabs($product, $storeId)
    {
        $customTabs = $this->_model->getCustomTabs();

        foreach ($customTabs as $key => $tab) {
            $stores = explode(',', $tab['stores']);
            if (!in_array(0, $stores) && !in_array($storeId, $stores)) {
                unset($customTabs[$key]);
            } else {
                if ($tab['content'] == 1) {
                    $customTabs[$key]['html'] = Mage::app()->getLayout()->createBlock('catalog/product_view')->getLayout()->createBlock("cms/block")->setBlockId($tab["content_block"])->toHtml();
                } else if ($tab['content'] == 2) {
                    if ($product->getData($tab["content_attribute"])){
                        $customTabs[$key]['html'] = $product->getData($tab["content_attribute"]);
                    } else {
                        unset($customTabs[$key]);
                    }

                }
            }

            if ($tab['active_on_products'] == 2) {
                $codes = explode(',', $tab['products_codes']);
                $codes = array_map('trim', $codes);

                if (!in_array($product->getSku(), $codes)) {
                    unset($customTabs[$key]);
                }
            }
        }

        return $customTabs;
    }

    /**
     * Check if tab is enabled.
     *
     * @param string $tab
     * @return bool
     */
    public function isTabEnabled($tab)
    {
        return $this->_model->isTabEnabled($tab);
    }

    /**
     * Return tabs sort order.
     *
     * @return array
     */
    public function getTabsSortOrder()
    {
        $data = $this->_model->getSortOrder();

        $sortOrder = array();
        if ($data) {
            foreach ($data as $item) {
                $sortOrder[$item['identifier']] = $item['sort_order'];
            }
        }

        return $sortOrder;
    }
}
