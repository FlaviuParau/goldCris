<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Field_Categories
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_optionRenderer;
    protected $_textareaRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('category', array(
            'label' => Mage::helper('gdprcookies')->__('Category')
        ));
        $this->addColumn('option', array(
            'label' => Mage::helper('gdprcookies')->__('Option'),
            'renderer' => $this->_getOptionRenderer()
        ));
        $this->addColumn('textarea', array(
            'label' => Mage::helper('gdprcookies')->__('Scripts'),
            'renderer' => $this->_getTextareaRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('gdprcookies')->__('Add Category');
    }


    protected function _getOptionRenderer()
    {
        if (!$this->_optionRenderer) {
            $this->_optionRenderer = $this->getLayout()->createBlock(
                'gdprcookies/adminhtml_system_config_form_field_select_option',
                '',
                array(
                    'is_render_to_js_template' => true
                )
            );
        }

        return $this->_optionRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getOptionRenderer()->calcOptionHash($row->getData('option')),
            'selected="selected"'
        );
    }

    protected function  _getTextareaRenderer()
    {
        if (!$this->_textareaRenderer) {
            $this->_textareaRenderer = $this->getLayout()->createBlock(
                'gdprcookies/system_config_form_field_textarea', ''
            );
        }
        return $this->_textareaRenderer;
    }
}