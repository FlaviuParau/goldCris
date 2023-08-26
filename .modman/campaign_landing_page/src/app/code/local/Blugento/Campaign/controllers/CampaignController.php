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

class Blugento_Campaign_CampaignController extends Mage_Core_Controller_Front_Action
{
    public function categoryAction()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $cacheId = 'bg_cmp_ajax_' . $categoryId;

        if ($cachedData = Mage::app()->getCache()->load($cacheId)) {
            $html = $cachedData;
        } else {
            $showOutOfStock = $this->getRequest()->getParam('stock');

            $category = Mage::getModel('catalog/category')->load($categoryId);

            $isAnchorFlag = $category->getIsAnchor();
            $category->setIsAnchor(false);

            $products = Mage::getResourceModel('catalog/product_collection')
                ->addCategoryFilter($category)
                ->addAttributeToSelect('*');

            if (!$showOutOfStock) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
            }

            $category->setIsAnchor($isAnchorFlag);

            $this->loadLayout();

            $html = $this->getLayout()
                ->createBlock('blugento_campaign/catalog_product_ajax_listajax')
                ->setProductsCollection($products)
                ->setTemplate('blugento_campaign/product/ajax/listajax.phtml')
                ->toHtml();

            if ($html) {
                try {
                    $cacheLifetime = Mage::getStoreConfig('blugento_campaign/general/cache_lifetime');
                    $cacheLifetime = $cacheLifetime ? $cacheLifetime : 3600;

                    Mage::app()->getCache()->save($html, $cacheId, array(Blugento_Campaign_Helper_Data::CACHE_TAG), $cacheLifetime);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        $this->getResponse()->setHeader('Content-Type', 'text/html')->setBody($html);
    }
}