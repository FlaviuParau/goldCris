<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


/**
 * @method string getToggleClasses()
 * @method Amasty_Shopby_Block_Advanced setToggleClasses(string $value)
 *
 * Class Amasty_Shopby_Block_Advanced
 */
class Amasty_Shopby_Block_Advanced extends Mage_Catalog_Block_Navigation
{
    /** @var  Amasty_Shopby_Model_Url_Builder */
    protected $urlBuilder;

    /** @var  int */
    protected $maxOptions;
    /** @var int */
    protected $renderedItemsCount = 0;

    /** @var  Amasty_Shopby_Helper_Data */
    private $dataHelper;

    protected function _construct()
    {
        parent::_construct();
        $this->dataHelper = Mage::helper('amshopby');
        $this->maxOptions = max(0, Mage::getStoreConfig('amshopby/category_filter/categories_max_options'));
    }

    public function getHtml()
    {
        return $this->_toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param int $level
     * @return string
     */
    public function drawOpenCategoryItem($category, $level = 0)
    {
        if ($this->_isExcluded($category->getId()) || !$category->getIsActive()) {
            return '';
        }

        $cssClass = array(
            'amshopby-cat',
            'level' . $level
        );
        $cssClass = $this->_addToggleCss($cssClass, $category);

        $currentCategory = $this->dataHelper->getCurrentCategory();

        if ($currentCategory->getId() == $category->getId()) {
            $cssClass[] = 'active';
        }

        if ($this->isCategoryActive($category)) {
            $cssClass[] = 'parent';
        }


        $productCount = '';
        if ($this->showProductCount()) {
            $productCount = $category->getProductCount();
            if ($productCount > 0) {
                $productCount = '&nbsp;<span class="count">(' . $productCount . ')</span>';
            } else {
                $productCount = '';
            }
        }

        $html = array();
        $label = $this->htmlEscape($category->getName()) . $productCount;
        $html[1] = '<a href="' . $this->getCategoryUrl($category) . '">' . $label . '</a>';

        $showAll   = Mage::getStoreConfig('amshopby/category_filter/show_all_categories');
        $showDepth = Mage::getStoreConfig('amshopby/category_filter/tree_depth');

        $hasChild = false;

        $inPath = in_array($category->getId(), $currentCategory->getPathIds());
        $showAsAll = $showAll && ($showDepth == 0 || $showDepth > $level + 1);
        if (($inPath || $showAsAll) && $category->getData('children_count')) {
            $childrenIds = $this->dataHelper->getCategoryChildrenIds($category->getId());
            if ($childrenIds) {
                $children = $this->_getCategoryCollection()->addIdFilter($childrenIds);
                $this->_getFilterModel()->addCounts($children);
                $children = $this->asArray($children);

                if ($children) {
                    $hasChild = true;
                    $htmlChildren = '';
                    foreach($children as $child) {
                        $htmlChildren .= $this->drawOpenCategoryItem($child, $level + 1);
                    }

                    if($htmlChildren != '') {
                        $cssClass[] = 'has-child';
                        $cssClass[] = 'expanded';
                        $html[2] = '<ol>' . $htmlChildren . '</ol>';
                    }
                }
            }
        }

        $html[0] = sprintf('<li class="%s">', implode(" ", $cssClass));
        $html[3] = '</li>';

        ksort($html);

        if ($category->getProductCount() || ($hasChild && $htmlChildren)) {
            $result = implode('', $html);
        } else {
            $result = '';
            $this->renderedItemsCount--;
        }

        return $result;
    }

    /**
     * @param array $cssClass
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    protected function _addToggleCss($cssClass, $category)
    {
        $this->renderedItemsCount++;
        if ($this->getMaxOptions() && $this->getRenderedItemsCount() > $this->getMaxOptions()) {
            $cssClass[] = $this->getToggleClasses();
        }
        return $cssClass;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        $this->urlBuilder->category = $category;
        $this->urlBuilder->changeQuery(array('cat' => $category->getId()));
        $url = $this->urlBuilder->getUrl();
        return $url;
    }

    /**
     * I need an array with the index being continunig numbers, so
     * it's possible to check for the previous/next category
     *
     * @param mixed $collection
     *
     * @return array
     */
    public function asArray($collection)
    {
        $array = array();
        foreach ($collection as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Get categories of current store, using the max depth setting for the vertical navigation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategories()
    {
        return $this->_getFilterModel()->getAdvancedCollection();
    }

    protected function _getCategoryCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('position', 'asc')
            ->joinUrlRewrite();

        return $collection;
    }

    public function showProductCount()
    {
        return Mage::getStoreConfigFlag('amshopby/category_filter/display_product_count');
    }

    protected function _toHtml()
    {
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder->reset();
        $urlBuilder->clearPagination();
        $this->urlBuilder = $urlBuilder;

        $html = '';

        $cats = $this->getCategories();

        $storeCategories = $this->asArray($cats);

        if (count($storeCategories) > 0) {
             foreach ($storeCategories as $c) {
                 if (!$this->_isExcluded($c->getId())) {
                    $html .= $this->drawOpenCategoryItem($c, 0);
                 }
             }
        }
        return $html;
    }

    /**
     * @return Amasty_Shopby_Model_Catalog_Layer_Filter_Category
     */
    protected function _getFilterModel()
    {
        return Mage::registry('amshopby_category_filter_model');
    }

    protected function _isExcluded($categoryId)
    {
        if (!$this->hasData('exclude_ids')) {
            $excludeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/category_filter/exclude_cat'));
            $excludeIds = $excludeIds ? explode(',', $excludeIds) : array();
            $this->setData('exclude_ids', $excludeIds);
        }
        $excludeIds = $this->getData('exclude_ids');
        if (in_array($categoryId, $excludeIds)) {
            return true;
        };

        if (!$this->hasData('include_ids')) {
            $includeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/category_filter/include_cat'));
            $includeIds = $includeIds ? explode(',', $includeIds) : array();
            $this->setData('include_ids', $includeIds);
        }
        $includeIds = $this->getData('include_ids');
        if ($includeIds && !in_array($categoryId, $includeIds)) {
            return true;
        };

        return false;
    }

    public function getRenderedItemsCount()
    {
        return $this->renderedItemsCount;
    }

    public function getMaxOptions()
    {
        return $this->maxOptions;
    }
}
