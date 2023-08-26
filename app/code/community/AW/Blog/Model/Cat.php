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


class AW_Blog_Model_Cat extends Mage_Core_Model_Abstract
{
    const NOROUTE_PAGE_ID = 'no-route';

    protected function _construct()
    {
        $this->_init('blog/cat');
    }

    public function load($id, $field = null)
    {
        return parent::load($id, $field);
    }

    public function noRoutePage()
    {
        $this->setData($this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName()));
        return $this;
    }

    public function getShortContent()
    {
        $content = $this->getData('short_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getPostContent()
    {
        $content = $this->getData('post_content');
        if (Mage::getStoreConfig(AW_Blog_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    public function getBlogCategories()
    {
        try {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result = $connection->query("SELECT cat_id, enable_for_customer_group FROM `aw_blog_cat`;");
            $rows = $result->fetchAll();

            $cats = array();
            foreach ($rows as $key => $row) {
                $row['enable_for_customer_group'] = explode(',', $row['enable_for_customer_group']);
                $cats[$key]['cat_id'] = $row['cat_id'];
                $cats[$key]['groups'] = $row['enable_for_customer_group'];
            }
            return $cats;

        } catch (Exception $e) {
            Mage::logException($e);
        }

    }
    
    public function getDefaultBlogRoute()
    {
	    $route = Mage::getStoreConfig('blog/blog/route');
	    if ($route == '') {
		    $route = 'blog';
	    }
	
	    return Mage::getUrl($route);
    }
    
    public function getCategoriesData()
    {
    	$route = $this->getDefaultBlogRoute();
	
	    $cats = Mage::getModel('blog/cat')->getCollection()
		    ->addStoreFilter(Mage::app()->getStore()->getId());
	
	    $categoryData = array();
	    foreach ($cats as $cat) {
		    $categoryData[] = array(
		    	'title' => $cat->getTitle(),
		    	'url'   => $route . 'cat/' . $cat->getIdentifier(),
		    );
	    }
	    
	    return $categoryData;
    }
}