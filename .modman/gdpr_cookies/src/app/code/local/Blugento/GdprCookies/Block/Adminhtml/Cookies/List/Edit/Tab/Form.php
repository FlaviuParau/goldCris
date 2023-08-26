<?php

class Blugento_GdprCookies_Block_Adminhtml_Cookies_List_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('cookies_list_form',
            array('legend'=>'Cookie informations'));
        $fieldset->addField('cookie_name', 'text',
            array(
                'label' => 'Cookie Name',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'cookie_name',
            ));
        $fieldset->addField('cookie_category', 'select',
            array(
                'label' => 'Cookie Category',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'cookie_category',
                'values'=> Mage::getModel('gdprcookies/system_config_source_categories')->toOptionArray()
            ));
        $fieldset->addField('cookie_description', 'textarea',
            array(
                'label' => 'Cookie Description',
                'name' => 'cookie_description',
            ));


        if ( Mage::registry('cookies_list_data') )
        {
            $form->setValues(Mage::registry('cookies_list_data')->getData());
        }

        return parent::_prepareForm();
    }
}