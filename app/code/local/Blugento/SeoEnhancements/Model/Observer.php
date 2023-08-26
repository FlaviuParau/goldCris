<?php
class Blugento_SeoEnhancements_Model_Observer
{
	const NOINDEX_ON_FILTERS = 'blugento_seoenhancements/enhancements_group/add_nofollow_on_filters_general';
    const NOINDEX_ON_PAGINATION = 'blugento_seoenhancements/enhancements_group/add_nofollow_on_pagination';

	const NOINDEX_ON_FILTERS_AND_PAGINATION = 'blugento_seoenhancements/enhancements_group/add_nofollow_on_filters';
	const NOINDEX_ON_MULTIPLE_FILTERS = 'blugento_seoenhancements/enhancements_group/add_nofollow_on_multiple_filters';

	const NOINDEX_ON_FACEBOOK = 'blugento_seoenhancements/enhancements_group/add_noindex_on_facebook_links';

    const NOINDEX_FOLLOW_SEARCH_PAGE = 'blugento_seoenhancements/enhancements_group/search_page_noindex_follow';

	const NOINDEX_FOLLOW = '<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>';
	const NOINDEX_NOFOLLOW = '<reference name="head"><action method="setRobots"><value>NOINDEX,NOFOLLOW</value></action></reference>';

    public function update(Varien_Event_Observer $observer)
    {
        $simplifyCategoryTitle = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/simplify_category_meta_title');
        $simplifyProductTitle = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/simplify_product_meta_title');
        $addPaginationToTitle = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/add_pagination_to_title');
	    $addPaginationToDescription = Mage::getStoreConfig('blugento_seoenhancements/enhancements_group/add_pagination_to_description');

        $headBlock = Mage::app()->getLayout()->getBlock('head');

        if ($headBlock) {
            $title = $headBlock->getTitle();
            $title = str_replace(Mage::getStoreConfig('design/head/title_prefix'), '', $title);
            $title = str_replace(Mage::getStoreConfig('design/head/title_suffix'), '', $title);
            $description = $headBlock->getDescription();

            if ($simplifyCategoryTitle) {
                if (Mage::registry('current_category')) {
                    $title = Mage::registry('current_category')->getName();
                }
            }

            if ($simplifyProductTitle) {
                if (Mage::registry('current_product')) {
                    $title = Mage::registry('current_product')->getName();
                }
            }

            if ($addPaginationToTitle) {
                $current_page = Mage::getBlockSingleton('page/html_pager')->getCurrentPage();

                if ($current_page > 1)
                {
                    $title = trim($title . ' - pag. ' . $current_page);
                }
            }

	        if ($addPaginationToDescription) {
		        $current_page = Mage::getBlockSingleton('page/html_pager')->getCurrentPage();

		        if ($current_page > 1) {
			        $description = trim($description . ' - pag. ' . $current_page);
		        }
	        }

            $headBlock->setTitle($title);
            $headBlock->setDescription($description);
        }

        return $this;
    }

    public function changeRobots(Varien_Event_Observer $observer)
    {
        $uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
        if ($observer->getEvent()->getAction()->getFullActionName() == 'catalog_category_view') {
            // noindex on filters in general
		    if (
                $this->_getStoreConfig(self::NOINDEX_ON_FILTERS) &&
                (stristr($uri, '?') != '')
            ) {
                if (stristr($uri, 'p=') != '') {
                    if ($this->_getStoreConfig(self::NOINDEX_ON_PAGINATION)) {
                        $layout = $observer->getEvent()->getLayout();
                        $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                        $layout->generateXml();
                    } else {
                        // do nothing
                    }
                } else {
                    $layout = $observer->getEvent()->getLayout();
                    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                    $layout->generateXml();
                }
            }

		    // noindex on multiple filters with pagination
            if (
		    	$this->_getStoreConfig(self::NOINDEX_ON_FILTERS_AND_PAGINATION) &&
			    (stristr($uri, '?') != '' && stristr($uri, 'p=') != '' && stristr($uri, '&') != '')
		    ) {
			    $layout = $observer->getEvent()->getLayout();
			    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
			    $layout->generateXml();
		    }

		    // noindex on multiple filters
		    if (
		    	$this->_getStoreConfig(self::NOINDEX_ON_MULTIPLE_FILTERS) &&
			    (stristr($uri, '?') != '' && stristr($uri, '&') != '')
		    ) {
			    $layout = $observer->getEvent()->getLayout();
			    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
			    $layout->generateXml();
		    }

            //noindex on facebook links
            if (
                $this->_getStoreConfig(self::NOINDEX_ON_FACEBOOK) &&
                (stristr($uri, 'fbclid=') != '')
            ) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                $layout->generateXml();
            }

        } else {
            // noindex on filters in general
            if (
                $this->_getStoreConfig(self::NOINDEX_ON_FILTERS) &&
                (stristr($uri, '?') != '')
            ) {
                if (stristr($uri, 'p=') != '') {
                    if ($this->_getStoreConfig(self::NOINDEX_ON_PAGINATION)) {
                        $layout = $observer->getEvent()->getLayout();
                        $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                        $layout->generateXml();
                    } else {
                        // do nothing
                    }
                } else {
                    $layout = $observer->getEvent()->getLayout();
                    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                    $layout->generateXml();
                }
            }

            //search page
            if (strpos($uri, 'catalogsearch/result/?q=') || strpos($uri, 'catalogsearch/result/index/?p=')) {
                $layout = $observer->getEvent()->getLayout();
                if ($this->_getStoreConfig(self::NOINDEX_FOLLOW_SEARCH_PAGE)) {
                    $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                } else {
                    $layout->getUpdate()->addUpdate(self::NOINDEX_NOFOLLOW);
                }
                $layout->generateXml();
            }

            //noindex on facebook links
            if (
                $this->_getStoreConfig(self::NOINDEX_ON_FACEBOOK) &&
                (stristr($uri, 'fbclid=') != '')
            ) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate(self::NOINDEX_FOLLOW);
                $layout->generateXml();
            }

            //noindex nofollow on review page
            if (strpos($uri, 'review/product/')) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate(self::NOINDEX_NOFOLLOW);
                $layout->generateXml();
            }

            //noindex nofollow on customer pages
            if (strpos($uri, 'customer/account')) {
                $layout = $observer->getEvent()->getLayout();
                $layout->getUpdate()->addUpdate(self::NOINDEX_NOFOLLOW);
                $layout->generateXml();
            }
        }

        return $this;
    }
	
	/**
	 * Retrieve config value by path
	 *
	 * @param string $path
	 * @return mixed
	 */
	protected function _getStoreConfig($path)
	{
		return Mage::getStoreConfig($path);
	}
}
