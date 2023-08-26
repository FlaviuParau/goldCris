<?php
/**
 * Blugento Admin Menu
 * Block for left menu tabs
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Adminmenu_Block_Adminhtml_Menu_Tabs extends Mage_Adminhtml_Block_Widget
{
    /**
     * Tabs
     *
     * @var array
     */
    protected $_tabs;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setId('system_config_tabs');
        $this->setTitle(Mage::helper('adminhtml')->__('Blugento'));
        $this->setTemplate('system/config/tabs.phtml');
    }

    /**
     * Sort array
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function _sort($a, $b)
    {
        return (int)$a->sort_order < (int)$b->sort_order ? -1 : ((int)$a->sort_order > (int)$b->sort_order ? 1 : 0);
    }

    /**
     * Initialise tabs and section from config.xml
     */
    public function initTabs()
    {
        $current = $this->getRequest()->getParam('section');
        if (!$current) {
            $current = 'configuration';
        }

        $url = Mage::getModel('adminhtml/url');

        $tabs = $this->_getMenuTabs();
        $sections = $this->_getMenuSections();

        usort($tabs, array($this, '_sort'));
        usort($sections, array($this, '_sort'));

        foreach ($tabs as $tab) {
            $this->addTab((string)$tab->code, array(
                'label' => Mage::helper('adminhtml')->__((string)$tab->label),
                'class' => (string)$tab->class
            ));
        }

        foreach ($sections as $section) {
            $code = (string)$section->code;

            if (!$this->checkSectionPermissions($code)) {
                continue;
            }

            $params = array_merge(
                array('_current' => true, 'section' => $code),
                (array)$section->url->params
            );
            $this->addSection($code, (string)$section->tab, array(
                'class'     => (string)$section->class,
                'label'     => Mage::helper('blugento_adminmenu')->__((string)$section->label),
                'url'       => $url->getUrl((string)$section->url->action, $params),
            ));

            if ($code == $current) {
                $this->setActiveTab((string)$section->tab);
                $this->setActiveSection($code);
            }
        }

        return $this;
    }

    public function addTab($code, $config)
    {
        $tab = new Varien_Object($config);
        $tab->setId($code);
        $this->_tabs[$code] = $tab;
        return $this;
    }

    /**
     * Retrive tab
     *
     * @param string $code
     * @return Varien_Object
     */
    public function getTab($code)
    {
        if(isset($this->_tabs[$code])) {
            return $this->_tabs[$code];
        }

        return null;
    }

    public function addSection($code, $tabCode, $config)
    {
        if ($tab = $this->getTab($tabCode)) {
            if(!$tab->getSections()) {
                $tab->setSections(new Varien_Data_Collection());
            }

            $customMethod = '_canShowSection_' . $code;
            if (method_exists($this, $customMethod)) {
                if (!$this->{$customMethod}($tabCode, $config)) {
                    return $this;
                }
            }

            $section = new Varien_Object($config);
            $section->setId($code);
            $tab->getSections()->addItem($section);
        }
        return $this;
    }

    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Check section permissions
     *
     * @param string $code
     * @return boolean
     */
    public function checkSectionPermissions($code=null)
    {
        static $permissions;

        if (!$code or trim($code) == "") {
            return false;
        }

        if (!$permissions) {
            $permissions = Mage::getSingleton('admin/session');
        }

        $showTab = false;
        if ( $permissions->isAllowed('system/config/'.$code) ) {
            $showTab = true;
        }
        return $showTab;
    }

    /**
     * Load tabs from config.xml
     * @return array
     */
    protected function _getMenuTabs()
    {
        return (array) Mage::getConfig()->getNode('default/blugento_menu/tabs');
    }

    /**
     * Load sections from config.xml
     * @return array
     */
    protected function _getMenuSections()
    {
        return (array) Mage::getConfig()->getNode('default/blugento_menu/sections');
    }

    /**
     * Custom method for show/hide data feed provider tab. Linked to Blugento Localizer setup
     * @param $section
     */
    protected function _canShowSection_data_feeds($tabCode, $config)
    {
        return Mage::getStoreConfig('blugentolocalizer/data_feeds/enabled') == 1;
    }
}
