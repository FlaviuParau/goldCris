<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.3.16
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blog_Block_Last extends AW_Blog_Block_Menu_Sidebar implements Mage_Widget_Block_Interface
{
    protected function _toHtml()
    {
        $this->setTemplate('aw_blog/widget_post.phtml');
        if ($this->_helper()->getEnabled()) {
            return $this->setData('blog_widget_recent_count', $this->getBlocksCount())->renderView();
        }
    }

    public function getRecent()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $group = 0;
        } else {
            $group = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }

        $collection = Mage::getModel('blog/blog')->getCollection()
            ->addPresentFilter()
            ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
            ->addFieldToFilter('enable_for_customer_group', ['finset' => $group])
            ->addStoreFilter()
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
	
//	protected function getPostCategoriesIds($item)
//	{
//		$catsIds = array();
//
//		foreach ($this->getCategoriesCollectionByItem($item) as $cat) {
//			$catsIds[] = $cat->getId();
//		}
//
//		return $catsIds;
//	}

    protected function getPostCategoriesIds($item)
    {
        $catsIds = array();
        try {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $itemId = $item->getId();
            $result = $connection->query("SELECT DISTINCT cat_id FROM aw_blog_post_cat WHERE post_id = $itemId;");
            $rows = $result->fetchAll();
            foreach ($rows as $row) {
                $catsIds[] = $row['cat_id'];
            }
        } catch (Exception $e) {
            Mage::throwException($e);
        }

        return $catsIds;
    }
	
	protected function getCategoriesCollectionByItem($item)
	{
		return Mage::getModel('blog/cat')->getCollection()
			->addPostFilter($item->getId())
			->addStoreFilter(Mage::app()->getStore()->getId());
	}
}
