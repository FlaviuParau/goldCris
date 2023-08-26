<?php

class Blugento_ExtendAwBlog_Block_Blog extends AW_Blog_Block_Blog
{

    protected function _prepareCollection()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $group = 0;
        } else {
            $group = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }

        if (!$this->getData('cached_collection')) {
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
                ->joinComments()
            ;

            if ($categoryId = Mage::getStoreConfig('blog/blog/category_general_page')) {
                $collection->addCatFilter($categoryId);
            }

            $sortOrder = $this->getRequest()->getParam('order', self::DEFAULT_SORT_ORDER);
            if (!in_array($sortOrder, array('created_time', 'sort_order'))){
                $sortOrder = 'sort_order';
            }
            $sortDirection = $this->getCurrentDirection();
            if (!in_array($sortDirection, array('ASC', 'DESC'))){
                $sortDirection = 'DESC';
            }

            $collection->getSelect()
                ->order(array($confSortBy." ".$confSortDirection, $sortOrder." ".$sortDirection));

            $collection->setPageSize((int)self::$_helper->postsPerPage());

            $this->setData('cached_collection', $collection);
        }
        return $this->getData('cached_collection');
    }
}