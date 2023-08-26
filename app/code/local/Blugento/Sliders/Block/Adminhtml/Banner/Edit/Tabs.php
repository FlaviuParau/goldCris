<?php
/**
 * Blugento Sliders
 * Tabs Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blugento_sliders_banner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Sliders / Banner'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'   => $this->__('General'),
            'title'   => $this->__('General'),
            'content' => $this->getLayout()->createBlock('blugento_sliders/adminhtml_banner_edit_tab_form')->toHtml()
        ));

        return parent::_beforeToHtml();
    }
}
