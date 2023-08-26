<?php

class Blugento_Catalog_Block_Category_Widget_Link extends Mage_Catalog_Block_Category_Widget_Link
{
	/**
	 * Get child categories as list from current category enabled in widget link
	 *
	 * @return mixed
	 */
	public function getChildCategories()
	{
		$idPath = explode('/', $this->_getData('id_path'));
		
		$children = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect(array('name', 'url_path'))
			->addAttributeToFilter('parent_id', $idPath[1])
			->addAttributeToFilter('is_active', 1)
			->addAttributeToSort('position');
		
		return $children;
	}
	
	public function getEnableChildCategories()
	{
		if (!$this->hasData('enable_child_categories')) {
			$this->setData('enable_child_categories', 0);
		}
		
		return $this->getData('enable_child_categories');
	}
	
	public function getEnableCategoryShortDescription()
	{
		if (!$this->hasData('enabled_category_short_description')) {
			$this->setData('enabled_category_short_description', 0);
		}
		
		return $this->getData('enabled_category_short_description');
	}
	
	public function getEnableCategoryDescription()
	{
		if (!$this->hasData('enabled_category_description')) {
			$this->setData('enabled_category_description', 0);
		}
		
		return $this->getData('enabled_category_description');
	}
	
	public function getCategoryImageWidth()
	{
		if (!$this->hasData('category_widget_image_width')) {
			$this->setData('category_widget_image_width', 0);
		}
		
		return $this->getData('category_widget_image_width');
	}
	
	public function getCategoryImageHeight()
	{
		if (!$this->hasData('category_widget_image_height')) {
			$this->setData('category_widget_image_height', 0);
		}
		
		return $this->getData('category_widget_image_height');
	}
	
	public function getButtonCustomText()
	{
		if (!$this->hasData('button_custom_text')) {
			$this->setData('button_custom_text', 'Shop Now');
		}
		
		return $this->getData('button_custom_text');
	}
	
	/**
	 * Prepare url using passed id and return it
	 * or return false if path was not found.
	 *
	 * @return string|false
	 */
	public function getHref()
	{
		if (!$this->_href) {
			$idPath = explode('/', $this->_getData('id_path'));
			
			if (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'product') {
				
				/** @var $helper Mage_Catalog_Helper_Product */
				$helper = $this->_getFactory()->getHelper('catalog/product');
				$productId = $idPath[1];
				$categoryId = isset($idPath[2]) ? $idPath[2] : null;
				
				$this->_href = $helper->getFullProductUrl($productId, $categoryId);
				
			} elseif (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'category') {
				$categoryId = $idPath[1];
				if ($categoryId) {
					/** @var $helper Mage_Catalog_Helper_Category */
					$helper = $this->_getFactory()->getHelper('catalog/category');
					$category = Mage::getModel('catalog/category')->load($categoryId);
					$this->_href = $helper->getCategoryUrl($category);
				}
			}
		}
		
		return $this->_href;
	}
}
