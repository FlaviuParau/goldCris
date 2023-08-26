<?php

class Blugento_GdprCookies_Block_Adminhtml_System_Config_Form_Field_Scripts
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_optionRenderer;
    protected $_scripttypeRenderer;
    protected $_scriptasyncRenderer;
    protected $_textareaRenderer;

    public function _prepareToRender()
    {

        $this->addColumn('script_type', array(
            'label' => Mage::helper('gdprcookies')->__('Script Type'),
            'renderer' => $this->_getScriptTypeRenderer()
        ));
        $this->addColumn('script_async', array(
            'label' => Mage::helper('gdprcookies')->__('Script Async'),
            'renderer' => $this->_getScriptAsyncRenderer()
        ));
        $this->addColumn('script_src', array(
            'label' => Mage::helper('gdprcookies')->__('Script Src')
        ));
        $this->addColumn('category', array(
            'label' => Mage::helper('gdprcookies')->__('Category'),
            'renderer' => $this->_getCategoryRenderer()
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('gdprcookies')->__('Add Script');
    }


    protected function _getCategoryRenderer()
    {
        if (!$this->_optionRenderer) {
            $this->_optionRenderer = $this->getLayout()->createBlock(
                'gdprcookies/adminhtml_system_config_form_field_select_category',
                '',
                array(
                    'is_render_to_js_template' => true
                )
            );
        }

        return $this->_optionRenderer;
    }

    protected function _getScriptTypeRenderer()
    {
        if (!$this->_scripttypeRenderer) {
            $this->_scripttypeRenderer = $this->getLayout()->createBlock(
                'gdprcookies/adminhtml_system_config_form_field_select_scripttype',
                '',
                array(
                    'is_render_to_js_template' => true
                )
            );
        }

        return $this->_scripttypeRenderer;
    }

    protected function _getScriptAsyncRenderer()
    {
        if (!$this->_scriptasyncRenderer) {
            $this->_scriptasyncRenderer = $this->getLayout()->createBlock(
                'gdprcookies/adminhtml_system_config_form_field_select_scriptasync',
                '',
                array(
                    'is_render_to_js_template' => true
                )
            );
        }

        return $this->_scriptasyncRenderer;
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {

        $row->setData(
            'option_extra_attr_' . $this->_getCategoryRenderer()->calcOptionHash($row->getData('category')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getScriptTypeRenderer()->calcOptionHash($row->getData('script_type')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getScriptAsyncRenderer()->calcOptionHash($row->getData('script_async')),
            'selected="selected"'
        );
    }
}