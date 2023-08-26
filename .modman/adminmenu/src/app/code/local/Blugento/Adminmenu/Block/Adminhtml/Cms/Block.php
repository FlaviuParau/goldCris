<?php
/**
 * Blugento Adminhtml CMS blocks content block
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */
class Blugento_Adminmenu_Block_Adminhtml_Cms_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_cms_block';
        $this->_blockGroup = 'blugento_adminmenu';
        $this->_headerText = Mage::helper('cms')->__('Blugento Static Blocks');
        parent::__construct();

        $this->_removeButton('add');
    }

}
