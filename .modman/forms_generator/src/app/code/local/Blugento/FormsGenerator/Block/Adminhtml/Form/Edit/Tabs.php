<?php
/**
 *
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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Block_Adminhtml_Form_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('formsgenerator_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blugento_formsgenerator')->__('Form Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section',
            array(
                'label'   => Mage::helper('blugento_formsgenerator')->__('Form Information'),
                'title'   => Mage::helper('blugento_formsgenerator')->__('Form Information'),
                'content' => $this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_edit_tab_form')->toHtml(),
            )
        );

        $this->addTab(
            'field_section',
            array(
                'label'   => Mage::helper('blugento_formsgenerator')->__('Field Information'),
                'title'   => Mage::helper('blugento_formsgenerator')->__('Field Information'),
                'content' => $this->getLayout()->createBlock('blugento_formsgenerator/adminhtml_form_edit_tab_fields')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}