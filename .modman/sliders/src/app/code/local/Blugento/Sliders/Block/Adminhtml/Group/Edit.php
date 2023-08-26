<?php
/**
 * Blugento Sliders
 * Edit Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'blugento_sliders';
        $this->_headerText = $this->_getHeaderText();

        $this->_addButton('save_and_edit_button', array(
            'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
            'onclick'   => 'editForm.submit(\'' . $this->getSaveAndContinueUrl() . '\')',
            'class'     => 'save'
        ));
    }

    /**
     * Retrieve the URL used for the save and continue link
     * This is the same URL with the back parameter added
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit'
        ));
    }
    
    /**
     * Retrieve the header text
     * If splash page exists, use name
     *
     * @return string
     */
    protected function _getHeaderText()
    {
        if ($group = Mage::registry('blugento-sliders-group')) {
            if ($displayName = $group->getTitle()) {
                return $displayName;
            }
        }

        return $this->__('Edit Group');
    }
}
