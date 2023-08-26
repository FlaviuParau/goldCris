<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Model_Setup_Cms extends Blugento_Localizer_Model_Setup_Abstract
{
    /**
     * @var array
     */
    protected $_footerLinks = array();

    /**
     * Setup Pages, Blocks and especially Footer Block
     *
     * @param string $country Country Code
     */
    public function setup($locale)
    {
        $storeId = $locale['store'];
        $locale  = $locale['code'];

        // execute pages
        foreach ($this->_getConfigPages() as $name => $data) {
            if ($data['execute'] == 1) {
                $backup = (array_key_exists('backup', $data) && $data['backup'] == 1) ? true : false;
                $this->_createCmsPage($data, $locale, true, $storeId, $backup);
            }
        }

        // execute footer links
        foreach ($this->_getConfigFooterLinks($locale) as $name => $data) {
            if ($data['execute'] == 1) {
                $this->_createFooterLinks($data, $locale, true, $storeId);
            }
        }

        // execute blocks
        foreach ($this->_getConfigBlocks($locale) as $name => $data) {
            if ($data['execute'] == 1) {
                if ($name == 'footerlinks') {
                    $this->_updateFooterLinksBlock($data, $storeId);
                } else {
                    $backup = (array_key_exists('backup', $data) && $data['backup'] == 1) ? true : false;
                    $this->_createCmsBlock($data, $locale, true, $storeId, $backup);
                }
            }
        }
    }

    /**
     * Sort Config Nodes based on values of a given tagname, defaults to <position>
     *
     * @param array  $nodes
     * @param string $sortTag
     *
     * @return array Config Nodes
     */
    protected function _sortConfigNodes(array $nodes, $sortTag = 'position')
    {
        $i = 0;
        foreach ($nodes as &$node) {
            if (array_key_exists($sortTag, $node) && !is_null($node[$sortTag]) && $node[$sortTag] > $i) {
                $i = $node[$sortTag];
            }
        }
        foreach ($nodes as &$node) {
            if (!array_key_exists($sortTag, $node) || is_null($node[$sortTag])) {
                $i += 10;
                $node[$sortTag] = (string)$i;
            }
        }

        uasort($nodes, function ($a, $b) {
            if ($a['position'] == $b['position']) {
                return 0;
            }

            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $nodes;
    }

    /**
     * Get pages/default from config file
     *
     * @return array Config pages
     */
    protected function _getConfigPages()
    {
        $configPages = $this->_getConfigNode('pages', 'default');
        $configPages = $this->_sortConfigNodes($configPages);

        return $configPages;
    }

    /**
     * Get blocks/default from config file
     *
     * @return array Config blocks
     */
    protected function _getConfigBlocks()
    {
        return $this->_getConfigNode('blocks', 'default');
    }

    /**
     * Get blocks/default from config file
     *
     * @return array Config blocks
     */
    protected function _getConfigFooterLinks()
    {
        return $this->_getConfigNode('footerlinks', 'default');
    }

    /**
     * Get footer_links/default from config file
     *
     * @param  int|null $storeId Store ID
     * @return array Footer Links
     */
    protected function _getFooterLinks($storeId)
    {
        if (!$storeId) {
            $storeId = 'default';
        }
        if (!isset($this->_footerLinks[$storeId])) {
            return array();
        }

        return $this->_footerLinks[$storeId];
    }

    /**
     * Collect data and create CMS page
     *
     * @param  array    $pageData Cms page data
     * @param  string   $locale   Locale
     * @param  boolean  $override Override email template if set
     * @param  int|null $storeId  Store ID
     * @return void
     */
    protected function _createCmsPage($pageData, $locale, $override = true, $storeId = null, $backup)
    {
        if (!is_array($pageData)) {
            return;
        }

        $data = array(
            'stores'    => $storeId ? $storeId : 0,
            'is_active' => 1,
        );

        $filename = Mage::getBaseDir('locale') . DS . $locale . DS . 'template' . DS . $pageData['filename'];
        if (!file_exists($filename)) {
            return;
        }

        $templateContent = $this->getTemplateContent($filename);

        if (preg_match('/<!--@title\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $data['title'] = $matches[1];
            $data['content_heading'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        if (preg_match('/<!--@identifier\s*((?:.)*?)\s*@-->/us', $templateContent, $matches)) {
            $data['identifier'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        if (preg_match('/<!--@root_template\s*(.*?)\s*@-->/s', $templateContent, $matches)) {
            $data['root_template'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        /**
         * Remove comment lines
         */
        $templateContent = preg_replace('#\{\*.*\*\}#suU', '', $templateContent);

        $data['content'] = $templateContent;

        if (is_null($storeId)) {
            $page = $this->_getDefaultPage($data['identifier']);
        } else {
            $page = Mage::getModel('cms/page')->setStoreId($storeId)->load($data['identifier']);
        }

        if (is_array($page->getStoreId()) && !in_array(intval($storeId), $page->getStoreId())) {
            $page = Mage::getModel('cms/page');
        } else {
            $data['page_id'] = $page->getId();
        }

        if ((int)$page->getId() && $backup) {

            /** @var Mage_Cms_Model_Page $backupPage */
            $backupPage = Mage::getModel('cms/page');
            $pageIdentifier = $data['identifier'] . '-backup-' . time();

            if (!$backupPage->load($pageIdentifier, 'identifier')->getId()) {
                $backupPage->setIsActive(0);
                $backupPage->setStores(array(0));
                $backupPage->setTitle($data['title']);
                $backupPage->setRootTemplate($data['root_template']);
                $backupPage->setIdentifier($pageIdentifier);
                $backupPage->setContent($page->getContent());
                $backupPage->save();
            }
        }

        if (!(int)$page->getId() || $override) {
            $page->addData($data);
            $page->save();
        }

        if (!$storeId) {
            $storeId = 'default';
        }

        if ($pageData['footerlink'] == 1) {
            $this->_footerLinks[$storeId][] = array(
                'title'  => $data['title'],
                'target' => $data['identifier'],
                'column' => isset($pageData['footerlinkcolumn']) && intval($pageData['footerlinkcolumn']) > 0 ? intval($pageData['footerlinkcolumn']) : 1
            );
        }

        if (isset($pageData['config_option'])) {
            $this->setConfigData($pageData['config_option'], $data['identifier'], $storeId);
        }
    }

    /**
     * Collect data and create Footer Links
     *
     * @param  array    $linkData  Footer Links data
     * @param  string   $locale    Locale
     * @param  boolean  $override  Override email template if set
     * @param  int|null $storeId   Store ID
     * @return void
     */
    protected function _createFooterLinks($linkData, $locale, $override = true, $storeId = null)
    {
        if ( $linkData["execute"] == 1 ) {
            $this->_footerLinks[$storeId][] = array(
                'title'   => $linkData['title'],
                'target'  => $linkData['path'],
                'extern'  => $linkData['extern'],
                'heading' => isset($linkData['heading']) ? intval($linkData['heading']) : 0,
                'column'  => isset($linkData['footerlinkcolumn']) && intval($linkData['footerlinkcolumn']) > 0 ? intval($linkData['footerlinkcolumn']) : 1
            );
        }
    }

    /**
     * Collect data and create CMS block
     *
     * @param  array    $blockData Cms block data
     * @param  string   $locale    Locale
     * @param  boolean  $override  Override email template if set
     * @param  int|null $storeId   Store ID
     * @return void
     */
    protected function _createCmsBlock($blockData, $locale, $override = true, $storeId = null, $backup)
    {
        $block = Mage::getModel('cms/block')->setStoreId($storeId)->load($blockData['identifier']);
        if (is_array($block->getStores()) && !in_array(intval($storeId), $block->getStores())) {
            $block = Mage::getModel('cms/block');
        }

        $filename = Mage::getBaseDir('locale') . DS . $locale . DS . 'template' . DS . $blockData['filename'];

        if (!file_exists($filename)) {
            return;
        }

        $templateContent = $this->getTemplateContent($filename);

        // Find title and remove comment line from content
        if (preg_match('/<!--@title\s*(.*?)\s*@-->/u', $templateContent, $matches)) {
            $blockData['title'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        // Find identifier and remove comment line from content
        if (preg_match('/<!--@identifier\s*((?:.)*?)\s*@-->/us', $templateContent, $matches)) {
            $blockData['identifier'] = $matches[1];
            $templateContent = str_replace($matches[0], '', $templateContent);
        }

        // Remove comment lines
        $templateContent = preg_replace('#\{\*.*\*\}#suU', '', $templateContent);

        if ((int)$block->getId() && $backup) {

            /** @var Mage_Cms_Model_Block $backupBlock */
            $backupBlock = Mage::getModel('cms/block');
            $blockIdentifier = $blockData['identifier'] . '-backup-' . time();

            if (!$backupBlock->load($blockIdentifier, 'identifier')->getId()) {
                $blockTitle        = array_key_exists('title', $blockData) ? $blockData['title'] : '';
                $blockRootTemplate = array_key_exists('root_template', $blockData) ? $blockData['root_template'] : '';
                $blockContent      = $templateContent;
                $backupBlock->setIsActive(0);
                $backupBlock->setStores(array(0));
                $backupBlock->setTitle($blockTitle);
                $backupBlock->setRootTemplate($blockRootTemplate);
                $backupBlock->setIdentifier($blockIdentifier);
                $backupBlock->setContent($blockContent);
                $backupBlock->save();
            }
        }

        if (!$block->getId() || $override) {
            $blockData['content'] = $templateContent;
            $blockData['stores'] = $storeId ? $storeId : 0;
            $blockData['is_active'] = '1';
            $blockData['block_id'] = $block->getId();

            $block->setData($blockData)->save();
        }
    }

    /**
     * Generate footer_links block from config data
     *
     * @param  int|null $storeId Store ID
     * @return string Footer Links Content
     */
    protected function _createFooterLinksContent($storeId)
    {
        $footerLinksHtmlLeft  = array();
        $footerLinksHtmlRight = array();
        $footerTitleLeft  = '';
        $footerTitleRight = '';

        foreach ($this->_getFooterLinks($storeId) as $data) {
            $title  = $data['title'];
            $target = $data['target'];
            $column = isset($data['column']) && intval($data['column']) > 0 ? intval($data['column']) : 1;

            $extern   = array_key_exists('extern', $data) ? $data['extern'] : null;
            $heading  = array_key_exists('heading', $data) ? $data['heading'] : null;
            $noFollow = array_key_exists('nofollow', $data) ? $data['nofollow'] : null;

            if ($heading == 1) {
                if ($column == 1) {
                    $footerTitleLeft  = '<li>' . $title . '</li>';
                } else {
                    $footerTitleRight = '<li>' . $title . '</li>';
                }
            } else {
                $footerLinksHtml = '<li>';

                if ($extern == 1) {
                    $noFollow = $noFollow ? 'rel="nofollow"' : '';
                    $footerLinksHtml .= '<a href="' . $target . '" ' . $noFollow . '>' . $title . '</a></li>';
                } else {
                    $footerLinksHtml .= '<a href="{{store url="' . $target . '"}}">' . $title . '</a></li>';
                }

                if ($column == 1) {
                    $footerLinksHtmlLeft[] = $footerLinksHtml;
                } else {
                    $footerLinksHtmlRight[] = $footerLinksHtml;
                }
            }
        }

        $footerLinksHtml = '';
        if ($footerLinksHtmlLeft || $footerTitleLeft) {
            $footerLinksHtml  = '<ul>' . $footerTitleLeft . implode('', $footerLinksHtmlLeft) . '</ul>';
        }
        if ($footerLinksHtmlRight || $footerTitleRight) {
            $footerLinksHtml .= '<ul>' . $footerTitleRight . implode('', $footerLinksHtmlRight) . '</ul>';
        }

        return $footerLinksHtml;
    }

    /**
     * Update footer_links cms block
     *
     * @param array    $blockData Cms block data
     * @param int|null $storeId   Store ID
     */
    protected function _updateFooterLinksBlock($blockData, $storeId = null)
    {
        /** @var $block Mage_Cms_Model_Block */
        if (is_null($storeId)) {
            $block = $this->_getDefaultBlock('footer_links');
        } else {
            $block = Mage::getModel('cms/block')->setStoreId($storeId)->load('footer_links');
        }

        if (is_array($block->getStores()) && !in_array(intval($storeId), $block->getStores())) {
            $block = Mage::getModel('cms/block');
        }

        if ($block->getId()) {

            /** @var $backupBlock Mage_Cms_Model_Block */
            $identifier = 'footer_links_backup_' . time();
            $backupBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');
            if (!$backupBlock->getId()) {

                // create copy of original block
                $data = array();
                $data['is_active'] = 0;
                $data['block_id']  = $block->getId();
                $data['identifier'] = $identifier;

                $block->setData($data)->save();

                /** @var $block Mage_Cms_Model_Block */
                $block = Mage::getModel('cms/block');
            }
        }

        $data = array(
            'title'      => 'Footer Links',
            'identifier' => 'footer_links',
            'content'    => $this->_createFooterLinksContent($storeId),
            'stores'     => $storeId ? $storeId : 0,
            'is_active'  => '1',
        );

        if ($storeId) {
            $data['stores'] = array($storeId);
        }

        $block->addData($data)->save();
    }

    /**
     * Retrieve the default block for the given identifier
     *
     * @param  string $identifier Block Identifier
     * @return Mage_Cms_Model_Block Block Model
     */
    protected function _getDefaultBlock($identifier)
    {
        return Mage::getResourceModel('cms/block_collection')
            ->addFieldToFilter('identifier', $identifier)
            ->addStoreFilter(0)->getFirstItem();
    }

    /**
     * Retrieve the default page for the given identifier
     *
     * @param  string $identifier Page Identifier
     * @return Mage_Cms_Model_Page Page Model
     */
    protected function _getDefaultPage($identifier)
    {
        return Mage::getResourceModel('cms/page_collection')
            ->addFieldToFilter('identifier', $identifier)
            ->addStoreFilter(0)->getFirstItem();
    }
}
