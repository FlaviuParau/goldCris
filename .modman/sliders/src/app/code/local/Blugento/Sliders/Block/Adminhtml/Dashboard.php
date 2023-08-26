<?php
/**
 * Blugento Sliders
 * Dashboard Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('blugento_sliders_dashboard_tabs');
        $this->setDestElementId('blugento-sliders-tab-content');
        $this->setTitle($this->__('Blugento Sliders'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
        $tabs = array(
            'group'     => 'Groups',
            'banner'    => 'Banners'
        );

        $_layout = $this->getLayout();

        $activeTabId = Mage::getSingleton('admin/session')->getActiveTabId();
        if ( !$activeTabId) {
            $activeTabId = 'group';
        }

        foreach ($tabs as $alias => $label) {
            $this->addTab($alias, array(
                'label'     => Mage::helper('catalog')->__($label),
                'content'   => $_layout->createBlock('blugento_sliders/adminhtml_' . $alias)->toHtml(),
                'active'    => $alias === $activeTabId
            ));
        }

        return parent::_prepareLayout();
    }
}
