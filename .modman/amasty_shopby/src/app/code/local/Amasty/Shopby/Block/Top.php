<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Block_Top extends Mage_Core_Block_Template
{
    CONST METADATA_PROCESSED = 'amshopby_category_metadata_processed';

    private $options = array();

    private function trim($str)
    {
        $str = strip_tags($str);
        $str = str_replace('"', '', $str);
        return trim($str, " -");
    }

    public function getBlockId()
    {
        return 'amshopby-filters-wrapper';
    }

    /**
     * @param Amasty_Shopby_Model_Page|null $page
     */
    protected function _handleCanonical($page = null)
    {
        if (Mage::app()->getRequest()->getModuleName() == 'amlanding') {
            return;
        }

        if (is_object($page) && $page->getUrl()) {
            $url = $page->getUrl();
        } else {
            /** @var Amasty_Shopby_Helper_Url $urlHelper */
            $urlHelper = Mage::helper('amshopby/url');
            $url = $urlHelper->getCanonicalUrl();
        }

        if ($url) {
            $this->_replaceCanonical($url);
        }
    }

    protected function _replaceCanonical($url)
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head = Mage::app()->getLayout()->getBlock('head');
        if ($head) {
            $this->removeCanonical($head);
            $head->addLinkRel('canonical', $url);
        }
    }

    /**
     * @param $head Mage_Page_Block_Html_Head
     */
    protected function removeCanonical($head)
    {
        foreach ($head->getData('items') as $item) {
            if (strpos($item['params'], 'canonical') !== false) {
                $head->removeItem('link_rel', $item['name']);
            };
        }
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return Amasty_Shopby_Model_Page|null
     */
    protected function getMatchedPage($category)
    {
        /** @var Amasty_Shopby_Model_Mysql4_Page $pageResource */
        $pageResource = Mage::getResourceModel('amshopby/page');
        $page = $pageResource->getCurrentMatchedPage($category->getId());
        return $page;
    }

    /**
     * Mage_Catalog_Model_Category $category Will be updated according matched Page
     * @param Amasty_Shopby_Model_Page
     * @return bool
     */
    protected function updateCategoryByPage($page, $category)
    {
        if (is_null($page)) {
            return;
        }

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        // metas
        $title = $head->getTitle();
        // trim prefix if any
        $prefix = Mage::getStoreConfig('design/head/title_prefix');
        $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));
        if ($prefix){
            $title = substr($title, strlen($prefix));
        }
        $suffix = Mage::getStoreConfig('design/head/title_suffix');
        $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));
        if ($suffix){
            $title = substr($title, 0, -1-strlen($suffix));
        }
        $descr = $head->getDescription();
        $kw = $head->getKeywords();

        if ($page->getUseCat()){
            $titleSeparator = ($page->getMetaTitle() && $title) ? Mage::getStoreConfig('amshopby/meta/title_separator') : '';
            $title = $title . $titleSeparator . $page->getMetaTitle();

            $descrSeparator = ($page->getMetaDescr() && $descr) ? Mage::getStoreConfig('amshopby/meta/descr_separator') : '';
            $descr = $descr . $descrSeparator . $page->getMetaDescr();

            $kwSeparator = ($page->getMetaKw() && $kw) ? ',' : '';
            $kw = $page->getMetaKw() . $kwSeparator . $kw;
        }
        else {
            $title = $page->getMetaTitle();
            $descr = $page->getMetaDescr();
            $kw = $page->getMetaKw();
        }

        $head->setTitle($this->trim($title));
        $head->setDescription($this->trim($descr));
        $head->setKeywords($this->trim($kw));

        // in-page description
        if ($page->getCmsBlockId()) {
            $this->setCategoryCmsBlock($category, $page->getCmsBlockId());
        }
        if ($page->getBottomCmsBlockId()) {
            $this->addBottomCmsBlock($page->getBottomCmsBlockId());
        }

        if ($page->getTitle()) {
            $category->setData('name', $page->getTitle());
            if($category->getData('h1_tag')) {
                $category->setData('h1_tag', $page->getTitle());
            }
        }
        if ($page->getDescription()) {
            $category->setData('description', $page->getDescription());
        }

        Mage::register(self::METADATA_PROCESSED, true, true);
    }

    /**
     * @param Amasty_Shopby_Model_Page $page
     * @return array
     */
    protected function getPageAttributes($page)
    {
        if (is_null($page)) {
            return array();
        }
        $pageAttr = $page->getConditions();

        return ($pageAttr != null) ? $pageAttr : array();
    }

    protected function _prepareLayout()
    {
        $this->setCacheLifetime(null);

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        /** @var Amasty_Shopby_Block_Catalog_Product_List_Toolbar $toolbar */
        $toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        if ($toolbar instanceof Amasty_Shopby_Block_Catalog_Product_List_Toolbar) {
            $toolbar->replacePager();
        }

        /** @var Mage_Catalog_Model_Layer $layer */
        $layer = Mage::getSingleton('catalog/layer');
        $category = $layer->getCurrentCategory();
        $page = Mage::registry('amshopby_page');

        $this->_handleCanonical($page);
        $this->updateCategoryByPage($page,$category);
        $matchedPageAttributes = $this->getPageAttributes($page);

        $filters = Mage::getResourceModel('amshopby/filter_collection')
            ->addTitles()
            ->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC);

        if($head) {
            $robots = $this->getSeoIndexFollowTags($filters, $category, $matchedPageAttributes);
            if (!$robots['index'] || !$robots['follow']) {
                $head->setRobots(($robots['index'] ? 'index' : 'noindex') . ', ' . ($robots['follow'] ? 'follow' : 'nofollow'));
                if (!$robots['index']) {
                    $this->removeCanonical($head);
                }
            }
        }

        if($matchedPageAttributes)
            return parent::_prepareLayout();

        $hash = $this->getRequestValuesHash($filters);
        if (!$hash) {
            return parent::_prepareLayout();
        }

        $options =  Mage::getModel('amshopby/value')
            ->getCollectionByMixedIds(array_keys($hash))->load();

        $cnt = $options->count();
        if (!$cnt) {
            return parent::_prepareLayout();
        }

        //some of the options value have wrong value;
        if ($cnt && $cnt < count($hash)) {
            return parent::_prepareLayout();
            // or make 404 ?
        }

        // sort options by attribute ids and add "show_on_list" property
        foreach ($options as $opt) {
            /** @var Amasty_Shopby_Model_Value $opt */
            $id = $opt->getOptionId();
            $opt->setShowOnList($hash[$id]);
            $hash[$id] = clone $opt;
        }

        // unset "fake"  options (not object)
        foreach ($hash as $id => $opt) {
            if (!is_object($opt)) {
                unset($hash[$id]);
            }
        }
        if (!$hash) {
            return parent::_prepareLayout();
        }

        $this->options = $hash;

        if ($head) {
            $this->changeMetaData($head);
        }

        $this->addBrandBreadcrumb();

        $this->addBottomCmsBlocks();


        $this->changeCategoryData($category);

        return parent::_prepareLayout();
    }

    protected function addBrandBreadcrumb()
    {
        $brandPageBrand = $this->getCurrentBrandPageBrand();
        if ($brandPageBrand) {
            /** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbs) {
                $breadcrumbs->addCrumb('amshopby-brand', array('label' => $brandPageBrand->getCurrentTitle(), 'title' => $brandPageBrand->getCurrentTitle()));
            }
        }
    }

    protected function addBottomCmsBlocks()
    {
        $currentBrand = $this->getCurrentBrandPageBrand();

        foreach ($this->options as $opt) {
            /** @var Amasty_Shopby_Model_Value $opt */
            if (!$opt->getShowOnList()){
                if (!$currentBrand || $currentBrand->getId() != $opt->getId()) {
                    continue;
                }
            }

            $bottomBlockId = $opt->getCurrentCmsBlockBottomId();
            if ($bottomBlockId) {
                $this->addBottomCmsBlock($bottomBlockId);
            }
        }
    }

    protected function addBottomCmsBlock($cmsBlockId)
    {
        $blocksObject = Mage::registry('amshopby_bottom_cms_object');
        if (!$blocksObject) {
            $blocksObject = Mage::getModel('varien/object');
            $blocksObject->setData('blocks', array());
            Mage::register('amshopby_bottom_cms_object', $blocksObject);
        }
        $blocks = $blocksObject->getData('blocks');

        /** @var Mage_Cms_Block_Block $block */
        $block = $this->getLayout()->createBlock('cms/block');
        $block->setBlockId($cmsBlockId);
        $blocks[] = $block;
        $blocksObject->setData('blocks', $blocks);
    }

    protected function changeCategoryData(Mage_Catalog_Model_Category $category)
    {
        $brandPageBrand = $this->getCurrentBrandPageBrand();
        if ($brandPageBrand) {
            $category->setData('name', $brandPageBrand->getCurrentTitle());
            if($category->getData('h1_tag')) {
                $category->setData('h1_tag', $brandPageBrand->getCurrentTitle());
            }
            $category->setData('description', $brandPageBrand->getCurrentDescr());
            $category->setData('image', $brandPageBrand->getImgBig() ? '../../amshopby/' .$brandPageBrand->getImgBig() : null);
            $this->setCategoryCmsBlock($category, $brandPageBrand->getCurrentCmsBlockId());
        }

        $titles = array();
        $descriptions = array();
        $imageUrl = null;
        $cmsBlockId = null;

        foreach ($this->options as $opt){
            /** @var Amasty_Shopby_Model_Value $opt */

            if ($brandPageBrand && $brandPageBrand->getId() == $opt->getId()) {
                // Already applied
                continue;
            }

            if (!$opt->getShowOnList()){
                continue;
            }

            if ($opt->getCurrentTitle()) {
                $titles[] = $opt->getCurrentTitle();
            }

            if ($opt->getCurrentDescr()) {
                $descriptions[] = $opt->getCurrentDescr();
            }

            if ($opt->getCurrentCmsBlockId() && is_null($cmsBlockId)) {
                // Keep only first matched CMS block due to standard template limitation
                $cmsBlockId = $opt->getCurrentCmsBlockId();
            }

            if ($opt->getImgBig() && is_null($imageUrl)){
                $imageUrl = '../../amshopby/' . $opt->getImgBig();
            }
        }

        $position = Mage::getStoreConfig('amshopby/heading/add_title');
        $title = $this->insertContent($category->getName(), $titles, $position, Mage::getStoreConfig('amshopby/heading/h1_separator'));
        $category->setData('name', $title);

		if($category->getData('h1_tag')) {
			$title = $this->insertContent($category->getData('h1_tag'), $titles, $position, Mage::getStoreConfig('amshopby/heading/h1_separator'));
			$category->setData('name', $title);
			$category->setData('h1_tag', $title);
		}

        $position = Mage::getStoreConfig('amshopby/heading/add_description');
        if ($descriptions && $position != Amasty_Shopby_Model_Source_Description_Position::DO_NOT_ADD) {
            $oldDescription = $category->getData('description');
            $description = '<span class="amshopby-descr">' . join('<br>', $descriptions) . '</span>';
            switch ($position) {
                case Amasty_Shopby_Model_Source_Description_Position::AFTER:
                    $description = $oldDescription ? $oldDescription . '<br>' . $description : $description;
                    break;
                case Amasty_Shopby_Model_Source_Description_Position::BEFORE:
                    $description = $oldDescription ? $description . '<br>' . $oldDescription : $description;
                    break;
                case Amasty_Shopby_Model_Source_Description_Position::REPLACE:
                    break;
            }
            $category->setData('description', $description);
        }

        if (isset($imageUrl) && Mage::getStoreConfig('amshopby/heading/add_image')) {
            $category->setData('image', $imageUrl);
        }

        if (isset($cmsBlockId) && Mage::getStoreConfig('amshopby/heading/add_cms_block')) {
            $this->setCategoryCmsBlock($category, $cmsBlockId);
        }

        if (Mage::getStoreConfigFlag('amshopby/brands/categories_enable')
            && $this->getCurrentBrandPageBrand()) {
            $mode = $category->getData('display_mode');
            if (!$mode || $mode == Mage_Catalog_Model_Category::DM_PRODUCT) {
                $category->setData('display_mode', Mage_Catalog_Model_Category::DM_MIXED);
            }
        }
    }

    /**
     * @param Mage_Page_Block_Html_Head $head
     */
    protected function changeMetaData($head)
    {
        $brandPageBrand = $this->getCurrentBrandPageBrand();
        if ($brandPageBrand) {
            $head->setTitle($brandPageBrand->getCurrentMetaTitle());
            $head->setDescription($brandPageBrand->getCurrentMetaDescr());
            $head->setKeywords($brandPageBrand->getCurrentMetaKw());
        }

        $titles = array();
        $descriptions = array();
        $keywords = array();

        foreach ($this->options as $opt){
            /** @var Amasty_Shopby_Model_Value $opt */
            if ($brandPageBrand && $brandPageBrand->getId() == $opt->getId()) {
                // Was added above
                continue;
            }

            if ($opt->getCurrentMetaTitle())
                $titles[] = $opt->getCurrentMetaTitle();

            if ($opt->getCurrentMetaDescr())
                $descriptions[] = $opt->getCurrentMetaDescr();

            if ($opt->getCurrentMetaKw())
                $keywords[] = $opt->getCurrentMetaKw();
        }

        $oldTitle = $this->getOldMetaTitle($head);
        $titlePosition = Mage::getStoreConfig('amshopby/meta/add_title');
        $titleSeparator = Mage::getStoreConfig('amshopby/meta/title_separator');
        $title = $this->insertContent($oldTitle, $titles, $titlePosition, $titleSeparator);
        $head->setTitle($title);

		$replacedData = Mage::registry('ammeta_replaced_data');
		if(isset($replacedData["meta_title"]) && empty($replacedData["meta_title_from_url"])) {
			$head->setTitle($title, false, true);
		}

        $oldDescription = $head->getDescription();
        $descriptionPosition = Mage::getStoreConfig('amshopby/meta/add_description');
        $descrSeparator = Mage::getStoreConfig('amshopby/meta/descr_separator');
        $description = $this->insertContent($oldDescription, $descriptions, $descriptionPosition, $descrSeparator);
        $head->setDescription($description);

		if(isset($replacedData["meta_description"]) && empty($replacedData["meta_title_from_url"])) {
			$head->setDescription($description, false, true);
		}

        $keywordsPosition = Mage::getStoreConfig('amshopby/meta/add_keyword');
        $oldKeywords = $head->getKeywords();
        $kwSeparator = ', ';
        $keywords = $this->insertContent($oldKeywords, $keywords, $keywordsPosition, $kwSeparator);
        $head->setKeywords($keywords);

		if(isset($replacedData["meta_keywords"]) && empty($replacedData["meta_title_from_url"])) {
			$head->setKeywords($keywords, false, true);
		}
		Mage::register(self::METADATA_PROCESSED, true, true);
    }

    /**
     * @return Amasty_Shopby_Model_Value|null
     */
    protected function getCurrentBrandPageBrand()
    {
        return Mage::helper('amshopby/attributes')->getCurrentBrandPageBrand();
    }

    /**
     * @param Mage_Page_Block_Html_Head $head
     * @return string
     */
    protected function getOldMetaTitle($head)
    {
        $title = $head->getTitle();
        // trim prefix if any
        $prefix = Mage::getStoreConfig('design/head/title_prefix');
        $prefix = htmlspecialchars(html_entity_decode(trim($prefix), ENT_QUOTES, 'UTF-8'));
        if ($prefix){
            $title = substr($title, strlen($prefix));
        }
        $suffix = Mage::getStoreConfig('design/head/title_suffix');
        $suffix = htmlspecialchars(html_entity_decode(trim($suffix), ENT_QUOTES, 'UTF-8'));
        if ($suffix && strpos($title, $suffix) === strlen($title) - strlen($suffix)){
            $title = substr($title, 0, -1-strlen($suffix));
        }
        return $title;
    }

    protected function insertContent($original, array $newParts, $position, $separator)
    {
        if ($newParts && $position != Amasty_Shopby_Model_Source_Description_Position::DO_NOT_ADD) {
            if ($original) {
                switch ($position) {
                    case Amasty_Shopby_Model_Source_Description_Position::AFTER:
                        array_unshift($newParts, $original);
                        break;
                    case Amasty_Shopby_Model_Source_Description_Position::BEFORE:
                        array_push($newParts, $original);
                        break;
                }
            }
            $result = join($separator, $newParts);
        }
        else {
            $result = $original;
        }
        $result = $this->trim($result);

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param int $cmsBlockId
     */
    protected function setCategoryCmsBlock($category, $cmsBlockId)
    {
        $category->setData('landing_page', $cmsBlockId);
        if ($cmsBlockId) {
            $mode = $category->getData('display_mode');
            if (!$mode || $mode == Mage_Catalog_Model_Category::DM_PRODUCT) {
                $category->setData('display_mode', Mage_Catalog_Model_Category::DM_MIXED);
            }

            $categoryView = Mage::app()->getLayout()->getBlock('category.products');
            if ($categoryView) {
                $categoryView->setData('cms_block_html', null);
            }
        }
    }

    /**
     * @deprecated
     * @return array
     */
    public function getOptions()
    {
        return array();
    }

    /**
     * @param Amasty_Shopby_Model_Mysql4_Filter_Collection $filters
     * @return array
     */
    protected function getRequestValuesHash($filters)
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        $hash = array();
        foreach ($filters as $f) {
            /** @var Amasty_Shopby_Model_Filter $f */
            $code = $f->getAttributeCode();
            $vals = $helper->getRequestValues($code, $f->getBackendType());
            if ($vals) {
                foreach ($vals as $v) {
                    $hash[$v] = $f->getShowOnList();
                }
            }
        }
        return $hash;
    }

    /**
     * @param Amasty_Shopby_Model_Mysql4_Filter_Collection $filters
     * @param Mage_Catalog_Model_Category $category
     * @param array $matchedPageAttributes
     * @return array
     */
    public function getSeoIndexFollowTags($filters, $category, $matchedPageAttributes)
    {
        $robots['index'] = true;
        $robots['follow'] = true;

        $robots = $this->updateRobotsByShopby($robots);
        $robots = $this->updateRobotsByRequestedParameters($robots, $filters, $matchedPageAttributes);
        $robots = $this->updateRobotsByCategoryParameter($robots, $category, $matchedPageAttributes);
        $robots = $this->updateRobotsByExtraAttributes($robots);

        return $robots;
    }

    protected function updateRobotsByShopby($robots)
    {
        $isCleanShopby = Mage::app()->getRequest()->getModuleName() == 'amshopby' && is_null($this->getCurrentBrandPageBrand());
        if ($isCleanShopby) {
            $noFollow = Mage::getStoreConfigFlag('amshopby/seo/shopby_nofollow');
            if ($noFollow) {
                $robots['follow'] = false;
            }

            $noIndex = Mage::getStoreConfigFlag('amshopby/seo/shopby_noindex');
            if ($noIndex) {
                $robots['index'] = false;
            }
        }
        return $robots;
    }

    /**
     * @param array $robots
     * @param Amasty_Shopby_Model_Mysql4_Filter_Collection $filters
     * @param array $matchedPageAttributes
     * @return array
     */
    protected function updateRobotsByRequestedParameters($robots, $filters, $matchedPageAttributes)
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        $matchedPageAttributesOrigin = $matchedPageAttributes;
        $params = Mage::app()->getRequest()->getParams();
        $currentBrand = $this->getCurrentBrandPageBrand();
        $appliedFiltersCount = 0;
        foreach ($filters as $filter) {
            if (!$robots['follow'] && !$robots['index']) {
                break;
            }
            /** @var Amasty_Shopby_Model_Filter $filter */
            $code = $filter->getAttributeCode();
            if (!isset($matchedPageAttributes[$code])) {
                $matchedPageAttributes[$code] = array();
            }

            $requestValues = $helper->getRequestValues($code, $filter->getBackendType());
            if ($requestValues || isset($params[$code]) && $filter->getBackendType() == 'decimal') {
                if (!$currentBrand || $currentBrand->getFilterId() != $filter->getId()) {
                    $appliedFiltersCount++;
                    if ($filter->getSeoNofollow()) {
                        $robots['follow'] = false;
                    }

                    $nonPageValues = array_diff($requestValues, $matchedPageAttributes[$code]);
                    if ($nonPageValues) {
                        if($this->isChildFilterOptionApplied($nonPageValues)) {
                            $robots['follow'] = $robots['index'] = false;
                            break;
                        }

                        if (($filter->getSeoNoindex() == Amasty_Shopby_Model_Filter::SEO_NO_INDEX_MULTIPLE_MODE && count($requestValues) > 1)
                            || $filter->getSeoNoindex() == Amasty_Shopby_Model_Filter::SEO_NO_INDEX_YES_MODE
                        ) {
                            $robots['index'] = false;
                        }
                    }
                }
            }
        }

        if (empty($matchedPageAttributesOrigin)
            && ($appliedFiltersCount > 1)
            && Mage::getStoreConfig('amshopby/seo/noindex_multiple')
        ) {
            $robots['index'] = false;
        }

        return $robots;
    }

    /**
     * @param array $requestedValues
     * @return bool
     */
    protected function isChildFilterOptionApplied($requestedValues)
    {
        $mappedOptions = array_keys(Mage::helper('amshopby/url')->getMappedOptionsWithParents());
        return (bool) array_intersect($mappedOptions, $requestedValues);
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param array $robots
     * @param array $matchedPageAttributes
     * @return array
     */
    protected function updateRobotsByCategoryParameter($robots, $category, $matchedPageAttributes)
    {
        $currentCategoryId = $category->getId();

        $catNoFollow = Mage::getStoreConfig('amshopby/seo/cat_nofollow');
        if ($catNoFollow != '') {
            $categoriesIds = array_flip(explode(",", $catNoFollow));
            if (isset($categoriesIds[$currentCategoryId])) {
                $robots['follow'] = false;
            }
        }

        if (!$matchedPageAttributes){
            $catNoIndex = Mage::getStoreConfig('amshopby/seo/cat_noindex');
            if ($catNoIndex != '') {
                $categoriesIds = array_flip(explode(",", $catNoIndex));
                if (isset($categoriesIds[$currentCategoryId])) {
                    $robots['index'] = false;
                }
            }
        }

        return $robots;
    }

    /**
     * @param array $robots
     * @return array mixed
     */
    protected  function updateRobotsByExtraAttributes($robots)
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        $priceVals = Mage::app()->getRequest()->getParam('price');
        if ($priceVals) {
            if ($helper->getSeoPriceNoindex()){
                $robots['index'] = false;
            }
            if ($helper->getSeoPriceNofollow()){
                $robots['follow'] = false;
            }
        }

        return $robots;
    }
}
