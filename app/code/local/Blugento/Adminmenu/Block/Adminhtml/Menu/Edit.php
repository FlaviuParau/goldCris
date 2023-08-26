<?php
/**
 * Blugento Admin Menu
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Adminmenu_Block_Adminhtml_Menu_Edit extends Mage_Adminhtml_Block_Widget
{
    const DEFAULT_SECTION_BLOCK = 'Blugento_Adminmenu_Block_Adminhtml_Menu_Edit_Form';

    protected $_section;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/config/edit.phtml');

        $sectionCode = $this->getRequest()->getParam('section');
        if (!$sectionCode) {
            $sectionCode = 'configuration';
        }
        $sections = Mage::getConfig()->getNode('default/blugento_menu/sections');

        $this->_section = $sections->$sectionCode;

        $this->setTitle((string)$this->_section->label);
        $this->setHeaderCss((string)$this->_section->header_css);
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save Config'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
        return parent::_prepareLayout();
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function initForm()
    {
        $blockName = (string)$this->_section->frontend_model;
        if (empty($blockName)) {
            $blockName = self::DEFAULT_SECTION_BLOCK;
        }
        $this->setChild('form',
            $this->getLayout()->createBlock($blockName)
                ->initForm()
        );
        return $this;
    }
}
