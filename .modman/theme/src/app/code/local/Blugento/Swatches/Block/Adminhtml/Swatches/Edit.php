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
 * @package     Blugento_Swatches
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Swatches_Block_Adminhtml_Swatches_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'blugento_swatches';
        $this->_controller = 'adminhtml_swatches';
        $this->_mode = 'edit';

        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('blugento_swatches')->__('Save And Continue Edit'),
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

        $this->removeButton('delete');
    }

    public function getHeaderText()
    {
        $headerText = Mage::helper('blugento_swatches')->__('Edit Item');

        if ($optionId = $this->getRequest()->getParam('option_id')) {
            /** @var Blugento_Swatches_Model_Swatches $swatches */
            $swatches = Mage::getModel('blugento_swatches/swatches');

            $value = $swatches->getOptionValue($optionId);
            $headerText .= ': "' . $value . '"';
        }

        return $headerText;
    }
}