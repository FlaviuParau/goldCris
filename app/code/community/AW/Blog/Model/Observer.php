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


class AW_Blog_Model_Observer
{
    const NOINDEX_FOLLOW = '<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>';
    const INDEX_FOLLOW = '<reference name="head"><action method="setRobots"><value>INDEX,FOLLOW</value></action></reference>';

    public function addBlogSection($observer)
    {
        $sitemapObject = $observer->getSitemapObject();
        if (!($sitemapObject instanceof Mage_Sitemap_Model_Sitemap)) {
            throw new Exception(Mage::helper('blog')->__('Error during generation sitemap'));
        }

        $storeId = $sitemapObject->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        /**
         * Generate blog pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/blog/changefreq');
        $priority = (string)Mage::getStoreConfig('sitemap/blog/priority');
        $collection = Mage::getModel('blog/blog')->getCollection()->addStoreFilter($storeId);
        Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
        $route = Mage::getStoreConfig('blog/blog/route',$sitemapObject->getStoreId());
        if ($route == "") {
            $route = "blog";
        }
        foreach ($collection as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $route . '/' . $item->getIdentifier()), $date, $changefreq, $priority
            );

            $sitemapObject->sitemapFileAddLine($xml);
        }
        unset($collection);
    }

    public function rewriteRssList($observer)
    {
        if (Mage::helper('blog')->getEnabled()) {
            $node = Mage::getConfig()->getNode('global/blocks/rss/rewrite');
            foreach (Mage::getConfig()->getNode('global/blocks/rss/drewrite')->children() as $dnode) {
                $node->appendChild($dnode);
            }
        }
    }

    public function changeRobots(Varien_Event_Observer $observer)
    {
        $uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
        if (strpos($uri, 'blog')) {
            if (strpos($uri, '/tag') || stristr($uri, '?') != '') {
                $layout = $observer->getEvent()->getLayout();
                if (stristr($uri, '?p=') != '') {
                    $layout->getUpdate()->addUpdate(self::INDEX_FOLLOW);
                } else {
                    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                }
                $layout->generateXml();
            }
        }

        return $this;
    }

    /**
     * Redirect blog post to non category url
     *
     * @param $observer
     */

    public function redirectToNonCategoryUrl($observer)
    {
        $fullActionName = $observer->getEvent()->getControllerAction()->getFullActionName();
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        if (Mage::getStoreConfig('blog/menu/redirect_to_non_category_url')) {
            if (strpos($fullActionName, 'blog') || strpos($currentUrl, 'blog') || (strpos($currentUrl, Mage::getStoreConfig('blog/blog/route')) && Mage::getStoreConfig('blog/blog/route') != '')) {
                $requestedString = $observer->getEvent()->getControllerAction()->getRequest()->getRequestString();
                $newUrl = rtrim(Mage::getBaseUrl(), '/') . rtrim($requestedString, '/');
                $arrayUrl = explode('/', $newUrl);
                if (in_array('cat', $arrayUrl) && in_array('post', $arrayUrl)) {
                    foreach ($arrayUrl as $key => $element) {
                        if ($element == 'cat') {
                            unset($arrayUrl[$key]);
                            unset($arrayUrl[$key + 1]);
                        }
                        if ($element == 'post') {
                            unset($arrayUrl[$key]);
                        }
                    }
                    $newUrl = implode('/', $arrayUrl);
                    if (!Mage::getStoreConfig('blog/blog/remove_last_slash')) {
                        $newUrl .= '/'; 
                    }
                    Mage::app()->getFrontController()->getResponse()->setRedirect($newUrl, 301);
                    Mage::app()->getResponse()->sendResponse();
                    exit;

                }
            }
        }
    }
}