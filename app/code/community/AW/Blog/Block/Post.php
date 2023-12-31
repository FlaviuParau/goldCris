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


class AW_Blog_Block_Post extends AW_Blog_Block_Abstract
{
    const DEFAULT_COMMENT_SORT_ORDER = 'created_time';
    const DEFAULT_COMMENT_SORT_DIR = 'desc';

    public function getPost()
    {
        if (!$this->hasData('post')) {
            if ($this->getPostId()) {
                $post = Mage::getModel('blog/post')->load($this->getPostId());
            } else {
                $post = Mage::getSingleton('blog/post');
            }
            $category = Mage::getSingleton('blog/cat')->load(
                $this->getRequest()->getParam(self::$_catUriParam), "identifier"
            );
            if ($category->getIdentifier()) {
                $post->setAddress(
                    $this->getBlogUrl(
                        null,
                        array(
                            self::$_catUriParam  => $category->getIdentifier(),
                            self::$_postUriParam => $post->getIdentifier()
                        )
                    )
                );
            } else {
                $post->setAddress($this->getBlogUrl($post->getIdentifier()));
            }

            $this->_prepareData($post)->_prepareDates($post);

            $this->setData('post', $post);
        }

        return $this->getData('post');
    }

    public function getBookmarkHtml($post)
    {
        if ($this->_helper()->isBookmarksPost()) {
            return $this->setTemplate('aw_blog/bookmark.phtml')->setPost($post)->renderView();
        }
    }

    public function getComment()
    {
        if (!$this->hasData('commentCollection')) {
            $sortOrder = $this->getRequest()->getParam('order', self::DEFAULT_COMMENT_SORT_ORDER);
            if (!in_array($sortOrder, array('created_time', 'sort_order'))){
                $sortOrder = 'sort_order';
            }
            $sortDirection = $this->getRequest()->getParam('dir', self::DEFAULT_COMMENT_SORT_DIR);
            if (!in_array($sortDirection, array('ASC', 'DESC'))){
                $sortOrder = 'ASC';
            }
            $collection = Mage::getModel('blog/comment')
                ->getCollection()
                ->addPostFilter($this->getPost()->getPostId())
                ->addApproveFilter(2)
            ;
            $collection->setOrder($collection->getConnection()->quote($sortOrder), $sortDirection);
            $collection->setPageSize((int)Mage::helper('blog')->commentsPerPage());
            $this->setData('commentCollection', $collection);
        }
        return $this->getData('commentCollection');
    }

    public function getCommentsEnabled()
    {
        return Mage::getStoreConfig('blog/comments/enabled');
    }

    public function getLoginRequired()
    {
        return Mage::getStoreConfig('blog/comments/login');
    }

    public function getFormAction()
    {
        return $this->getUrl('*/*/*');
    }

    public function getFormData()
    {
        return $this->getRequest();
    }

    protected function _prepareLayout()
    {
        $this->_prepareCrumbs()->_prepareHead();
    }

    protected function _beforeToHtml()
    {
        Mage::helper('blog/toolbar')->create(
            $this,
            array(
                 'orders'        => array('created_time' => $this->__('Created At'), 'user' => $this->__('Added By')),
                 'default_order' => 'created_time',
                 'dir'           => 'desc',
                 'limits'        => self::$_helper->commentsPerPage(),
                 'method'        => 'getComment'
            )
        );
        return $this;
    }

    protected function _prepareCrumbs()
    {
        $breadcrumbs = $this->getCrumbs();
        if ($breadcrumbs) {
            $helper = $this->_helper();
            $breadcrumbs->addCrumb(
                'blog',
                array(
                     'label' => $helper->getTitle(),
                     'title' => $this->__('Return to %s', $helper->getTitle()),
                     'link'  => Mage::getUrl($helper->getRoute()),
                )
            );

            $title = trim($this->getCategory()->getTitle());
            if ($title) {
                $breadcrumbs->addCrumb(
                    'cat',
                    array(
                        'label' => $title,
                        'title' => $this->__('Return to %s', $title),
                        'link'  => Mage::getUrl(
                            $helper->getRoute() . '/cat/' . $this->getCategory()->getIdentifier()
                        ),
                    )
                );
            }

            $breadcrumbs->addCrumb(
                'blog_page', array('label' => htmlspecialchars_decode($this->getPost()->getTitle()))
            );
        }

        return $this;
    }

    protected function getCategory()
    {
        if (!$this->hasData('postCategory')) {
            $this->setData(
                'postCategory', Mage::getSingleton('blog/cat')->load($this->getRequest()->getParam('cat'), "identifier")
            );
        }

        return $this->getData('postCategory');
    }

    protected function _prepareHead()
    {
        parent::_prepareMetaData($this->getPost());

        return $this;
    }

    public function setCommentDetails($name, $email, $comment)
    {
        return $this
            ->setData('commentName', $name)
            ->setData('commentEmail', $email)
            ->setData('commentComment', $comment)
        ;
    }

    public function getCommentText()
    {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();
        if ($blogPostModelFromSession) {
            return $blogPostModelFromSession->getComment();
        }

        if (!empty($this->_data['commentComment'])) {
            return $this->_data['commentComment'];
        }
        return;
    }

    public function getCommentEmail()
    {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();
        if ($blogPostModelFromSession) {
            return $blogPostModelFromSession->getEmail();
        }

        if (!empty($this->_data['commentEmail'])) {
            return $this->_data['commentEmail'];
        } elseif ($customer = Mage::getSingleton('customer/session')->getCustomer()) {
            return $customer->getEmail();
        }
        return;
    }

    public function getCommentName()
    {
        $blogPostModelFromSession = Mage::getSingleton('customer/session')->getBlogPostModel();

        $name = null;
        if ($blogPostModelFromSession) {
            $name = $blogPostModelFromSession->getUser();
        }
        if (!empty($this->_data['commentName'])) {
            $name = $this->_data['commentName'];
        } elseif ($customer = Mage::getSingleton('customer/session')->getCustomer()) {
            $name = $customer->getName();
        }
        return trim($name);
    }

    public function getClearComment($str)
    {
        $str = str_replace('{{', '&#123;&#123;', $str);
        $str = str_replace('}}', '&#125;&#125;', $str);
        return $str;
    }

    public function getPostImage($image)
    {
        $imageHelper = Mage::helper('blog/image');

        $imageWidthPost = Mage::getStoreConfig('blog/blog/image_width_view');
        $imageHeightPost = Mage::getStoreConfig('blog/blog/image_height_view');

        if (!$imageWidthPost || !$imageHeightPost) {
            $postImage = $imageHelper->getImageUrl($image);
        } else {
            $postImage = $imageHelper->init($image)
                ->constrainOnly(false)
                ->keepAspectRatio(true)
                ->keepFrame(true)
                ->backgroundColor(array(255, 255, 255))
                ->resize($image, $imageWidthPost, $imageHeightPost);
        }

        return $postImage;
    }
}
