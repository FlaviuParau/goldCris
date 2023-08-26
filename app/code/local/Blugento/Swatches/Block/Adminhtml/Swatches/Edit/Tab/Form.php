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

class Blugento_Swatches_Block_Adminhtml_Swatches_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('swatches_form', array('legend' => Mage::helper('blugento_swatches')->__('General')));
        $this->_addElementTypes($fieldset);

        $modeField = $fieldset->addField(
            'mode',
            'select',
            array(
                'label' => Mage::helper('blugento_swatches')->__('Mode'),
                'name' => 'mode',
                'values' => Mage::getSingleton('blugento_swatches/adminhtml_system_config_source_mode')->getOptionArray()
            )
        );

        $imageField = $fieldset->addField(
            'image_name',
            'image',
            array(
                'label' => Mage::helper('blugento_swatches')->__('Image File'),
                'name' => 'image_name'
            )
        );

        $colorField = $fieldset->addField(
            'color',
            'text',
            array(
                'label' => Mage::helper('blugento_swatches')->__('Color'),
                'name' => 'color',
                'class' => 'color'
            )
        );

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($modeField->getHtmlId(), $modeField->getName())
                ->addFieldMap($imageField->getHtmlId(), $imageField->getName())
                ->addFieldMap($colorField->getHtmlId(), $colorField->getName())
                ->addFieldDependence($imageField->getName(), $modeField->getName(), 2)
                ->addFieldDependence($colorField->getName(), $modeField->getName(), 1));

        if (Mage::getSingleton('adminhtml/session')->getBlugentoswatchesData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlugentoswatchesData());
            Mage::getSingleton('adminhtml/session')->getBlugentoswatchesData(null);
        } elseif (Mage::registry('blugentoswatches_data')) {
            $form->setValues(Mage::registry('blugentoswatches_data')->getData());
        }
        return parent::_prepareForm();
    }
}


