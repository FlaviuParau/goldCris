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


class AW_Blog_Block_Blog extends AW_Blog_Block_Abstract
{
    public function getPosts()
    {
        $collection = $this->_prepareCollection();

        $search = Mage::app()->getRequest()->getParam('search');
        $search = Mage::helper('blog')->sanitize($search);
        if ($search && $search != '') {
            $collection->addFieldToFilter(
                array('title', 'post_content'),
                array(
                    array('like'=>"%$search%"),
                    array('like'=>"%$search%")
                )
            );
        }

        $tag = $this->getRequest()->getParam('tag');
        if ($tag) {
            $collection->addTagFilter(urldecode($tag));
        }
        parent::_processCollection($collection);
        return $collection;
    }

    protected function _prepareLayout()
    {
        if ($this->isBlogPage() && ($breadcrumbs = $this->getCrumbs())) {
            parent::_prepareMetaData(self::$_helper);
            $tag = $this->getRequest()->getParam('tag', false);
            if ($tag) {
                $tag = urldecode($tag);
                $breadcrumbs->addCrumb(
                    'blog',
                    array(
                        'label' => self::$_helper->getTitle(),
                        'title' => $this->__('Return to ' . self::$_helper->getTitle()),
                        'link'  => $this->getBlogUrl(),
                    )
                );
                $breadcrumbs->addCrumb(
                    'blog_tag',
                    array(
                        'label' => $this->__('Tagged with "%s"', self::$_helper->convertSlashes($tag)),
                        'title' => $this->__('Tagged with "%s"', $tag),
                    )
                );
            } else {
                $breadcrumbs->addCrumb('blog', array('label' => self::$_helper->getTitle()));
            }
        }
    }

    protected function _prepareCollection()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $group = 0;
        } else {
            $group = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        
        if (!$this->getData('cached_collection')) {
            $sortOrder = $this->getRequest()->getParam('order', self::DEFAULT_SORT_ORDER);
            if (!in_array($sortOrder, array('created_time', 'sort_order'))){
                $sortOrder = 'sort_order';
            }
            $sortDirection = $this->getCurrentDirection();
            if (!in_array($sortDirection, array('ASC', 'DESC'))){
                $sortDirection = 'DESC';
            }
            $collection = Mage::getModel('blog/blog')->getCollection()
                ->addPresentFilter()
                ->addEnableFilter(AW_Blog_Model_Status::STATUS_ENABLED)
                ->addFieldToFilter('enable_for_customer_group', ['finset' => $group])
                ->addStoreFilter()
                ->joinComments()
            ;
            $collection->setOrder($collection->getConnection()->quote($sortOrder), $sortDirection);
            $collection->setPageSize((int)self::$_helper->postsPerPage());

            $this->setData('cached_collection', $collection);
        }
        return $this->getData('cached_collection');
    }


    public function getCurrentDirection()
    {
        $dir = $this->getRequest()->getParam('dir');

        if (in_array($dir, array('asc', 'desc'))) {
            return $dir;
        }

        return Mage::helper('blog')->defaultPostSort(Mage::app()->getStore()->getId());
    }

    public function getListImage($image)
    {
        $imageHelper = Mage::helper('blog/image');

        $imageWidth = Mage::getStoreConfig('blog/blog/image_width');
        $imageHeight = Mage::getStoreConfig('blog/blog/image_height');

        if (!$imageWidth || !$imageHeight) {
            $postImage = $imageHelper->init($image)
                ->constrainOnly(false)
                ->keepAspectRatio(true)
                ->keepFrame(true)
                ->backgroundColor(array(255, 255, 255))
                ->resize($image, 400);
        } else {
            $postImage = $imageHelper->init($image)
                ->constrainOnly(false)
                ->keepAspectRatio(true)
                ->keepFrame(true)
                ->backgroundColor(array(255, 255, 255))
                ->resize($image, $imageWidth, $imageHeight);
        }

        return $postImage;
    }

    protected function isOptionEnabled()
    {
        return Mage::getStoreConfig('blog/blog/canonical');
    }

    public function blogCanonical()
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
        $path = $url->getPath();
        $blogPaths = array('/blog', '/blog/', '/index.php/blog/', '/index.php/blog', '/' . Mage::getStoreConfig('blog/blog/route'), '/' . Mage::getStoreConfig('blog/blog/route') . '/');

//        if (stristr($currentUrl, '?') != '' && strpos($path, 'blog')) {
//            return null;
//        }

        if(trim($path, '/') == 'blog') {
            return rtrim(Mage::getBaseUrl() . 'blog', '/');
        }
        if(Mage::getStoreConfig('blog/blog/route') && Mage::getStoreConfig('blog/blog/route') != '' && trim($path, '/') == Mage::getStoreConfig('blog/blog/route')) {
            return rtrim(Mage::getBaseUrl() . Mage::getStoreConfig('blog/blog/route'), '/');
        }

        if(in_array($path, $blogPaths))
        {
            return rtrim($currentUrl, '/');
        }

        //set canonical link for other blog links also
        if (Mage::getStoreConfig('blog/blog/canonical_extra') && (strpos($path, 'blog') || strpos($path, Mage::getStoreConfig('blog/blog/route')))) {
            return rtrim(Mage::getBaseUrl() . ltrim($path, '/'), '/');
        }
    }
    
    public function getBlogCategoriesData()
    {
    	return Mage::getModel('blog/cat')->getCategoriesData();
    }
}