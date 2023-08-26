<?php

class AW_Blog_Block_Search extends Mage_Rss_Block_Abstract
{
    /**
     * Return the blog url
     *
     * @return string
     */
    public function getSearchTerm()
    {
        $searchTerm = $this->getRequest()->getParam('search');
        $searchTerm = Mage::helper('blog')->sanitize($searchTerm);

        if ($searchTerm && $searchTerm != '') {
            return $searchTerm;
        }

        return false;
    }

    /**
     * Return the blog url
     *
     * @return string
     */
    public function getBlogUrl()
    {
        $blogRoute = Mage::helper('blog')->getRoute();

        return Mage::getBaseUrl() . $blogRoute;
    }
}
