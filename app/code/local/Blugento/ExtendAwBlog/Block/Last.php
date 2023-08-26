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
 * @package     Blugento_ExtendAwBlog
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ExtendAwBlog_Block_Last extends AW_Blog_Block_Last
{
    public function getRecent()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $group = 0;
        } else {
            $group = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }

        $confSortBy = Mage::getStoreConfig('blog/blog/sort_by')
            ? Mage::getStoreConfig('blog/blog/sort_by')
            : 'created_time';
        $confSortDirection = Mage::getStoreConfig('blog/blog/sort_direction')
            ? Mage::getStoreConfig('blog/blog/sort_direction')
            : 'desc';

        $collection = Mage::getModel('blog/blog')->getCollection()
            ->addPresentFilter()
            ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
            ->addFieldToFilter('enable_for_customer_group', ['finset' => $group])
            ->addStoreFilter()
            ->setOrder($confSortBy, $confSortDirection)
            ->setOrder('created_time', 'desc')
        ;

        if ($this->getBlocksCount()) {
            $blocksCount = $this->getBlocksCount();
        } else {
            $blocksCount = Mage::helper('blog')->getRecentPage();
        }

        $addedItems = 0;
        foreach ($collection as $key => $item) {
        	if (in_array($this->getCategories(), $this->getPostCategoriesIds($item)) && $addedItems < $blocksCount) {
	            $item->setAddress($this->getBlogUrl($item->getIdentifier()));
	            $item->setCategories($this->getRecentPostCat($item));
                $addedItems++;
	        } else {
        		$collection->removeItemByKey($key);
	        }
        }

        return $collection;
    }
	
	protected function getRecentPostCat($item)
	{
		$route = Mage::getStoreConfig('blog/blog/route');
		
		if ($route == "") {
			$route = "blog";
		}
		
		$route = Mage::getUrl($route);
		
		$catUrls = array();
		foreach ($this->getCategoriesCollectionByItem($item) as $cat) {
			$catUrls[$cat->getTitle()] = $route . "cat/" . $cat->getIdentifier();
		}
		return $catUrls;
	}
}
