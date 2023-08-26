<?php
/**
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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_Model_Observer
{
    /**
     * Add "Required" Option to Checkout Agreements creation
     *
     * Event: <adminhtml_block_html_before>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return Blugento_Localizer_Model_Observer Observer
     */
    public function addOptionsForAgreements(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Checkout_Agreement_Edit_Form) {
            $helper = Mage::helper('blugento_localizer');
            $form = $block->getForm();

            /** @var Varien_Data_Form_Element_Fieldset $fieldset */
            $fieldset = $form->getElement('base_fieldset');

            $form->getElement('content')->setRequired(false);

            $fieldset->addField('is_required', 'select', array(
                'label'    => $helper->__('Required'),
                'title'    => $helper->__('Required'),
                'note'     => $helper->__('Display Checkbox on Frontend'),
                'name'     => 'is_required',
                'required' => true,
                'options'  => array(
                    '1' => $helper->__('Yes'),
                    '0' => $helper->__('No'),
                ),
            ));

            Mage::dispatchEvent('blugento_localizer_adminhtml_checkout_agreement_edit_form', array(
                'form'     => $form,
                'fieldset' => $fieldset,
            ));

            $model = Mage::registry('checkout_agreement');
            $form->setValues($model->getData());
            $block->setForm($form);
        }

        return $this;
    }

    /**
     * Add "Visible on Checkout Review on Front-end" Option to Attribute Settings
     *
     * Event: <adminhtml_catalog_product_attribute_edit_prepare_form>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return FireGento_MageSetup_Model_Observer Observer
     */
    public function addIsVisibleOnCheckoutOption(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $form = $event->getForm();

        $fieldset = $form->getElement('front_fieldset');
        $source = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $fieldset->addField(
            'is_visible_on_checkout',
            'select',
            array(
                'name'   => 'is_visible_on_checkout',
                'label'  => Mage::helper('blugento_localizer')->__('Visible in Checkout'),
                'title'  => Mage::helper('blugento_localizer')->__('Visible in Checkout'),
                'values' => $source,
            )
        );

        return $this;
    }
}
