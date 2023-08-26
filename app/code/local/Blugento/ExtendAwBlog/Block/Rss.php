<?php

class Blugento_ExtendAwBlog_Block_Rss extends AW_Blog_Block_Rss
{
    protected function _toHtml()
    {
        $rssObj = Mage::getModel('rss/rss');
        $route = Mage::helper('blog')->getRoute();
        $url = $this->getUrl($route);
        $title = Mage::getStoreConfig('blog/blog/title');
        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );

        if (Mage::getStoreConfig('blog/rss/image') != "") {
            $data['image'] = $this->getSkinUrl(Mage::getStoreConfig('blog/rss/image'));
        }

        $rssObj->_addHeader($data);

        $confSortBy = Mage::getStoreConfig('blog/blog/sort_by')
            ? Mage::getStoreConfig('blog/blog/sort_by')
            : 'created_time';
        $confSortDirection = Mage::getStoreConfig('blog/blog/sort_direction')
            ? Mage::getStoreConfig('blog/blog/sort_direction')
            : 'desc';

        $collection = Mage::getModel('blog/blog')->getCollection()
            ->addPresentFilter()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setOrder($confSortBy, $confSortDirection)
        ;

        $identifier = $this->getRequest()->getParam('identifier');

        $tag = $this->getRequest()->getParam('tag');
        if ($tag) {
            $collection->addTagFilter(urldecode($tag));
        }

        if ($catId = Mage::getSingleton('blog/cat')->load($identifier)->getcatId()) {
            Mage::getSingleton('blog/status')->addCatFilterToCollection($collection, $catId);
        }

        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);

        $collection->setPageSize((int)Mage::getStoreConfig('blog/rss/posts'));
        $collection->setCurPage(1);

        if ($collection->getSize()) {
            $processor = Mage::helper('cms')->getBlockTemplateProcessor();
            foreach ($collection as $post) {

                $data = array(
                    'title'       => $post->getTitle(),
                    'link'        => $this->getUrl($route . "/" . $post->getIdentifier()),
                    'description' => $processor->filter($post->getPostContent()),
                    'lastUpdate'  => strtotime($post->getCreatedTime()),
                );
                $rssObj->_addEntry($data);
            }
        }
        return $rssObj->createRssXml();
    }
}