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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Block_Adminhtml_Cms_Edit_Tab_Setup
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        $cms_page = Mage::registry('cms_page');
        if ($cms_page->getIdentifier() == 'home') {
            $this->getLayout()->getBlock('cms_page_edit_tabs')->removeTab('content_section');

            $tab = $this->getLayout()->createBlock('blugento_homepagemanager/adminhtml_cms_edit_tab_homepagemanager', 'cms_page_edit_tab_content');

            $this->getLayout()->getBlock('head')->addItem('skin_css', 'blugento/homepagemanager/css/main.css');
            $this->getLayout()->getBlock('head')->addItem('skin_css', 'blugento/homepagemanager/js/jquery-ui-1.11.4.custom/jquery-ui.min.css');

            $this->getLayout()->getBlock('head')->addItem('skin_js', 'blugento/homepagemanager/js/jquery-1.11.3.min.js');
            $this->getLayout()->getBlock('head')->addItem('skin_js', 'blugento/homepagemanager/js/jquery-ui-1.11.4.custom/jquery-ui.min.js');
            $this->getLayout()->getBlock('head')->addItem('skin_js', 'blugento/homepagemanager/js/blugento-utils.js');
            $this->getLayout()->getBlock('head')->addItem('skin_js', 'blugento/homepagemanager/js/blugento-hm-html.js');
            $this->getLayout()->getBlock('head')->addItem('skin_js', 'blugento/homepagemanager/js/blugento-hm.js');

            $this->getLayout()->getBlock('cms_page_edit_tabs')->addTabAfter('tab_homepagemanager', $tab, 'main_section');
            $tab = $this->getLayout()->createBlock('blugento_homepagemanager/adminhtml_cms_edit_tab_export', 'cms_page_edit_tab_export');
            $this->getLayout()->getBlock('cms_page_edit_tabs')->addTabAfter('cms_page_edit_tab_export', $tab, 'main_section');
        }
    }
}
