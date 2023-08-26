<?php

class Blugento_ExtendAwBlog_Helper_Post extends AW_Blog_Helper_Post
{
    /**
     * Renders CMS page
     * Call from controller action
     *
     * @param Mage_Core_Controller_Front_Action $action
     * @param integer                              $identifier
     *
     * @return bool
     */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $identifier = null)
    {
        $page = Mage::getModel('blog/post');
        $imageHelper = Mage::helper('blog/image');

        if (!is_null($identifier) && $identifier !== $page->getId()) {
            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($identifier)) {
                return false;
            }
        }

        if (!$page->getId()) {
            return false;
        }
        if ($page->getStatus() == 2) {
            return false;
        }
        $blogPost = Mage::getSingleton('blog/post')->load($identifier);

        $pageTitle = $blogPost->getTitle();
        $blogTitle = Mage::getStoreConfig('blog/blog/title') . " - ";
        if (!Mage::getStoreConfig('blog/blog/title') || Mage::getStoreConfig('blog/blog/title') == '') {
            $blogTitle = '';
        }

        $action->loadLayout();
        if ($storage = Mage::getSingleton('customer/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }

        if (!Mage::registry('blog-post')) {
            Mage::register('blog-post', $blogPost);
        }

        $action->getLayout()->getBlock('head')->setTitle($blogTitle . $pageTitle);
        $action->getLayout()->getBlock('root')->setTemplate(Mage::getStoreConfig('blog/blog/layout'));
        $action->getLayout()->getBlock('root')->setOgImage($imageHelper->getImageUrl($blogPost->getFeaturedImage()));
        $action->renderLayout();

        return true;
    }
}