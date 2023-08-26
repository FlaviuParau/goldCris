<?php
/**
 * Blugento Sliders
 * Form Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('group_');
        $form->setFieldNameSuffix('group');
        
        $this->setForm($form);

        $fieldset = $form->addFieldset('group_general', array('legend' => $this->__('General Information')));

        $fieldset->addField('is_enabled', 'select', array(
            'name'      => 'is_enabled',
            'title'     => $this->__('Enabled'),
            'label'     => $this->__('Enabled'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $this->__('Title'),
            'title'     => $this->__('Title'),
            'required'  => true,
            'class'     => 'required-entry'
        ));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => $this->__('Code'),
            'title'     => $this->__('Code'),
            'note'      => $this->__('This is a unique identifier that is used to inject the banner group via XML'),
            'required'  => true,
            'class'     => 'required-entry validate-code'
        ));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => $this->__('Store'),
            'title'     => $this->__('Store'),
            'required'  => true,
            'class'     => 'required-entry',
            'values'    => $this->_getStores()
        ));

        $fieldset = $form->addFieldset('group_settings', array('legend' => $this->__('Carousel Settings')));

        $fieldset->addField('carousel_animate', 'select', array(
            'name'      => 'carousel_animate',
            'title'     => $this->__('Enable Animation'),
            'label'     => $this->__('Enable Animation'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('carousel_duration', 'text', array(
            'name'      => 'carousel_duration',
            'title'     => $this->__('Animation Duration'),
            'label'     => $this->__('Animation Duration'),
            'class'     => 'validate-greater-than-zero'
        ));

        $fieldset->addField('carousel_auto', 'select', array(
            'name'      => 'carousel_auto',
            'title'     => $this->__('Auto-Start'),
            'label'     => $this->__('Auto-Start'),
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField('carousel_autospeed', 'text', array(
            'name'      => 'carousel_autospeed',
            'title'     => $this->__('Auto Start Speed'),
            'label'     => $this->__('Auto Start Speed'),
            'note'      => $this->__('Autoplay Speed in milliseconds'),
            'class'     => 'validate-greater-than-zero'
        ));

        $fieldset->addField('carousel_effect', 'select', array(
            'name'      => 'carousel_effect',
            'title'     => $this->__('Effect'),
            'label'     => $this->__('Effect'),
            'values'    => Mage::getModel('blugento_sliders/system_config_source_carousel_effect')->toOptionArray()
        ));

        $fieldset = $form->addFieldset('group_controls', array('legend' => $this->__('Controls')));

        $fieldset->addField('controls_position', 'select', array(
            'name'      => 'controls_position',
            'title'     => $this->__('Position'),
            'label'     => $this->__('Position'),
            'values'    => Mage::getModel('blugento_sliders/system_config_source_controls_position')->toOptionArray()
        ));

        if ($group = Mage::registry('blugento-sliders-group')) {
            $form->setValues($group->getData());
        } else {
            $form->setValues(Mage::getModel('blugento_sliders/group')->getAnimationData());
        }

        return parent::_prepareForm();
    }

    /**
     * Retrieve an array of all of the stores
     *
     * @return array
     */
    protected function _getStores()
    {
        $stores = Mage::getResourceModel('core/store_collection');
        $options = array(0 => $this->__('Global'));

        foreach ($stores as $store) {
            $options[$store->getId()] = $store->getWebsite()->getName() . ': ' . $store->getName();
        }

        return $options;
    }
}
