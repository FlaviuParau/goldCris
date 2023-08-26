<?php

class Blugento_ExtendAwBlog_Block_Product_Toolbar extends AW_Blog_Block_Product_Toolbar
{
    public function setCollection($collection)
    {
        $viewPage = Mage::app()->getRequest()->getParam('identifier');

        parent::setCollection($collection);

        if (!$viewPage) {
            $confSortBy = Mage::getStoreConfig('blog/blog/sort_by')
                ? Mage::getStoreConfig('blog/blog/sort_by')
                : 'created_time';
            $confSortDirection = Mage::getStoreConfig('blog/blog/sort_direction')
                ? Mage::getStoreConfig('blog/blog/sort_direction')
                : 'desc';

            $this->_collection->setOrder($confSortBy, $confSortDirection);
        }

        return $this;
    }

    public function getCurrentOrder()
    {
        $viewPage = Mage::app()->getRequest()->getParam('identifier');

        if (!$viewPage) {
            return Mage::getStoreConfig('blog/blog/sort_by')
                ? Mage::getStoreConfig('blog/blog/sort_by')
                : 'created_time';
        }
    }
}
