<?php
class Blugento_CategoryShowcase_Block_Category_View
    extends Mage_Catalog_Block_Category_View
{
    /**
     * Check if category display mode is "Subcategories only"
     *
     * @return bool
     */
    public function isSubcategoryMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY;
    }

    /**
     * Check if category display mode is "Subcategories and products"
     *
     * @return bool
     */
    public function isSubcategoryProductsMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_PRODUCTS;
    }

    /**
     * Check if category display mode is "Static block and subcategories"
     *
     * @return bool
     */
    public function isSubcategoryPageMode()
    {
        return $this->getCurrentCategory()->getDisplayMode() == Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_PAGE;
    }

    /**
     * Check if category display mode is "Static block, subcategories and products"
     *
     * @return bool
     */
    public function isSubcategoryMixedAll()
    {
        return $this->getCurrentCategory()->getDisplayMode() == Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_MIXED_ALL;
    }

    /**
     * @return string
     */
    public function getSubcategoryHtml()
    {
        return $this->getChildHtml('category.subcategories');
    }

    public function getCurrentCategoryCollection()
    {
        $category = $this->getCurrentCategory();

        $categoriesCollection = $category->getCollection()
            ->addAttributeToSelect(array('name', 'image'))
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($category->getChildren())
            ->addOrderField('position');

        return $categoriesCollection ;
    }
	
	/**
	 * Return category background image
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return bool|string
	 */
	public function getBackgroundImageUrl($category)
	{
		$url = false;
		if ($image = $category->getBlugentoBackgroundImage()) {
			$url = Mage::getBaseUrl('media') . 'catalog/category/' . $image;
		}
		
		return $url;
	}
}
