<?php

class Blugento_SeoEnhancements_Block_Page_Html_Head_Category_Canonical extends Mage_Core_Block_Template
{

    protected function isOptionEnabled()
    {
        return Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/category_canonical');
    }

    public function categoryCanonical()
    {
        $categoryId = $this->getRequest()->getParam('id');

        $filters= Mage::getSingleton('catalog/layer')->getState()->getFilters();
        $pagination = $this->getRequest()->getParam('p');
        $canonicalPagination = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/category_canonical_pagination');

        if (!is_array($filters)) {
            return Mage::getModel("catalog/category")->load($categoryId)->getUrl();
        } else {
            $filterCategoryId = $this->getRequest()->getParam('cat');
            if (isset($filterCategoryId)) {
                return Mage::getModel("catalog/category")->load($filterCategoryId)->getUrl();
            } else if (isset($pagination)) {
                switch ($canonicalPagination) {
                    case 1:
                        return Mage::getModel("catalog/category")->load($categoryId)->getUrl();
                        break;
                    case 2:
                        return Mage::getModel("catalog/category")->load($categoryId)->getUrl() . '?p=' . $pagination;
                        break;
                    case 3:
                        if ($pagination != 1) {
                            return Mage::getModel("catalog/category")->load($categoryId)->getUrl() . '?p=' . $pagination;
                        } else {
                            return Mage::getModel("catalog/category")->load($categoryId)->getUrl();
                        }
                        break;
                    default:
                        return Mage::getModel("catalog/category")->load($categoryId)->getUrl();
                }
            }
        }

        return Mage::getModel("catalog/category")->load($categoryId)->getUrl();
    }

    protected function noCanonicalOnPagination()
    {
        $flag = false;
        $pagination = $this->getRequest()->getParam('p');
        $noCanonical = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/pagination_no_canonical');
        if ($noCanonical && $pagination && $pagination != 1) {
            $flag = true;
        }

        return $flag;
    }
}
