<?php


class Blugento_Feeds_Block_System_Config_Form_Field_ReplaceAttributes
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected $_attributeRenderer;

    /**
     * Retrieve country column renderer
     *
     * @return Blugento_Feeds_Block_A_System_Config_Form_Field_Select_Attributes
     */
    protected function _getAttributeRenderer()
    {
        if ( ! $this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                'blugento_feeds/system_config_form_field_select_attributes', '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_attributeRenderer;
    }

    /**
     * Add columns, change button labels etc.
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'replaced_attribute', array(
                'label'    => Mage::helper('blugento_feeds')->__('Replace Attribute (same key name like in XML)'),
                'style' => 'width: 100px',
            )
        );
        $this->addColumn(
            'replaced_attribute_with', array(
                'label' => Mage::helper('blugento_feeds')->__('Replace With'),
                'renderer' => $this->_getAttributeRenderer(),
            )
        );

        $this->_addButtonLabel = Mage::helper('blugento_feeds')->__('Add Replace');
        $this->_addAfter       = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData('option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('replaced_attribute_with')), 'selected="selected"');
    }
}

