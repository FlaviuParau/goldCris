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

class Blugento_Campaign_Block_Catalog_Product_List extends Blugento_Theme_Block_Catalog_Product_List
{

    protected $imageHelper = null;
    protected $outputHelper = null;
    protected $helperTheme = null;
    protected $productImageBackgroundColorToHex = null;
    protected $priceDisplayType = null;
    protected $shippingCostUrl = null;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        if (!Mage::getStoreConfig('blugento_campaign/general/enabled')) {
            return parent::_construct();
        }

        $cacheKey = 'BG_CMP_' . $this->getCampaignId();

        $cacheLifetime = Mage::getStoreConfig('blugento_campaign/general/cache_lifetime');
        $cacheLifetime = $cacheLifetime ? $cacheLifetime : 3600;

        $this->addData(array(
            'cache_lifetime' => $cacheLifetime,
            'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG),
            'cache_key' => $cacheKey,
        ));

        //Helpers for ProductExtraData
        $this->imageHelper = Mage::helper('catalog/image');
        $this->outputHelper = Mage::helper('catalog/output');
        $this->helperTheme = Mage::helper('blugento_theme');
        $this->productImageBackgroundColorToHex = $this->getProductImageBackgroundColorToHex();
        $this->priceDisplayType = $this->getPriceDisplayType();
        $this->shippingCostUrl = '#shipping-info';
    }

    /**
     * Load and return an array of all the child categories of a given category
     *
     * @param int $parentId
     * @return array
     */
    protected function _getAllCategories($parentId)
    {
        $category = Mage::getModel('catalog/category')->load($parentId);
        $subCategoriesStr = $category->getChildren();

        $categories = array();
        $categories[] = $category;

        if ($subCategoriesStr) {
            $subCategories = explode(',', $category->getChildren());

            foreach ($subCategories as $sub) {
                $categories[] = Mage::getModel('catalog/category')->load($sub);
            }
        }

        return $categories;
    }

    /**
     * Getting product collection for a particular category
     *
     * @param Varien_Data_Collection $category
     * @param int $showOutOfStock
     * @return Varien_Data_Collection
     */
    protected function _getCategoryProductCollection($category, $showOutOfStock)
    {
        $isAnchorFlag = $category->getIsAnchor();
        $category->setIsAnchor(false);

        $products = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($category)
            ->addAttributeToSelect('*');

        if (!$showOutOfStock) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        }

        $category->setIsAnchor($isAnchorFlag);

        return $products;
    }

    /**
     * Return categories names and ids for menu
     *
     * @param int $categoryId
     * @return array
     */
    protected function _getCategoriesNames($categoryId)
    {
        /** @var Blugento_Campaign_Model_Category $category */
        $category = Mage::getModel('blugento_campaign/category');

        return $category->getCategoriesNames($categoryId);
    }

    /**
     * Return campaign model
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _getCampaign()
    {
        $campaignId = $this->getCampaignId();
        $campaign = Mage::getModel('blugento_campaign/campaign')->load($campaignId);

        return $campaign;
    }

    /**
     * Check if the campaign is valid to display
     *
     * @param $identifier
     * @return bool
     */
    protected function _isValidToDisplay($identifier)
    {
        if (!Mage::getStoreConfig('blugento_campaign/general/enabled')) {
            return false;
        }

        if ($identifier != Mage::getSingleton('cms/page')->getIdentifier()) {
            return false;
        }

        return true;
    }
}
