<?php
class Blugento_CategorySecondDescription_Block_Category_Description extends Mage_Catalog_Block_Category_View
{

    public function getSecondImageUrl($category){
        $url = false;
        if ($image = $category->getSecondImage()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;
    }

    public function isEnabled()
    {
        return Mage::helper('blugento_categoryseconddescription')->isEnabled();
    }

    public function isEnabledOnPagination()
    {
        $currentPage = (int) Mage::App()->getRequest()->getParam('p');
        $removeCategoryDescription = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/remove_category_description');

        if ($removeCategoryDescription && $currentPage > 1) {
            return false;
        }

        return true;
    }
}