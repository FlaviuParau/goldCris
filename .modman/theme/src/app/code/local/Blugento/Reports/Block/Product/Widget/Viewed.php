<?php

class Blugento_Reports_Block_Product_Widget_Viewed extends Mage_Reports_Block_Product_Widget_Viewed
{
	public function getDisplayType()
	{
		if (!$this->hasData('display_type')) {
			$this->setData('display_type', 1);
		}

		return $this->getData('display_type');
	}
	
	public function getSliderItemRow()
	{
		if (!$this->hasData('slider_item_row')) {
			$this->setData('slider_item_row', 4);
		}
		
		return $this->getData('slider_item_row');
	}
	
	public function getSliderAnimation()
	{
		if (!$this->hasData('slider_animation')) {
			$this->setData('slider_animation', 500);
		}
		
		return $this->getData('slider_animation');
	}
	
	public function getSliderItemScroll()
	{
		if (!$this->hasData('slider_item_scroll')) {
			$this->setData('slider_item_scroll', 1);
		}
		
		return $this->getData('enable_child_categories');
	}
	
	public function getSliderItemLoop()
	{
		if (!$this->hasData('slider_item_loop')) {
			$this->setData('slider_item_loop', 2);
		}
		
		return $this->getData('slider_item_loop');
	}
	
	public function getSliderItemAutoplay()
	{
		if (!$this->hasData('slider_item_autoplay')) {
			$this->setData('slider_item_autoplay', 2);
		}
		
		return $this->getData('slider_item_autoplay');
	}
	
	public function getSliderItemCssease()
	{
		if (!$this->hasData('slider_item_cssease')) {
			$this->setData('slider_item_cssease', 'linear');
		}

		return $this->getData('slider_item_cssease');
	}

	public function getSliderItemCenter()
	{
		if (!$this->hasData('slider_item_center')) {
			$this->setData('slider_item_center', '2');
		}

		return $this->getData('slider_item_center');
	}
	
	public function getSliderMobileMaxItems()
	{
		if (!$this->hasData('mobile_max_items')) {
			$this->setData('mobile_max_items', 1);
		}

		return $this->getData('mobile_max_items');
	}

	public function getShowPrice()
	{
		if (!$this->hasData('show_price')) {
			$this->setData('show_price', 0);
		}

		return $this->getData('show_price');
	}

	public function getShowAddToCart()
	{
		if (!$this->hasData('show_add_to_cart')) {
			$this->setData('show_add_to_cart', 0);
		}

		return $this->getData('show_add_to_cart');
	}

	public function getShowAddToWishlist()
	{
		if (!$this->hasData('show_add_to_wishlist')) {
			$this->setData('show_add_to_wishlist', 0);
		}

		return $this->getData('show_add_to_wishlist');
	}

	public function getShowAddToCompare()
	{
		if (!$this->hasData('show_add_to_compare')) {
			$this->setData('show_add_to_compare', 0);
		}

		return $this->getData('show_add_to_compare');
	}

	public function getShowShortDescription()
	{
		if (!$this->hasData('show_short_description')) {
			$this->setData('show_short_description', 0);
		}

		return $this->getData('show_short_description');
	}
}
