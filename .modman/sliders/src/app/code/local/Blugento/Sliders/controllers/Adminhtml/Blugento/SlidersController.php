<?php
/**
 * Blugento Sliders
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Adminhtml_Blugento_SlidersController extends Blugento_Sliders_Controller_Adminhtml_Abstract
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title('Blugento Sliders');
        $this->_setActiveMenu('blugento_adminmenu/blugento_sliders');
        $this->renderLayout();
    }

    /**
     * Display the group grid
     *
     */
    public function groupGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('blugento_sliders/adminhtml_group_grid')->toHtml());
    }

    /**
     * Display the banner grid
     *
     */
    public function pageGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('blugento_sliders/adminhtml_banner_grid')->toHtml());
    }

    /**
     * Determine ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_sliders');
    }
}
