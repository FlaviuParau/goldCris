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

class Blugento_FormsGenerator_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'blugento_formsgenerator';
        $this->_controller = 'adminhtml_form';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('blugento_formsgenerator')->__('Save Form'));
        $this->_updateButton('delete', 'label', Mage::helper('blugento_formsgenerator')->__('Delete Form'));

        $this->_addButton(
            'preview',
            array(
                'label' => Mage::helper('adminhtml')->__('Preview Form'),
                'onclick' => 'previewForm()'
            )
        );

        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );

        $this->_formScripts[]
            = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        return Mage::helper('blugento_formsgenerator')->__('Add/Edit Form');
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }
}