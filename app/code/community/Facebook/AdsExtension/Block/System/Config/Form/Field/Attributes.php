<?php

class Facebook_AdsExtension_Block_System_Config_Form_Field_Attributes
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
                'Facebook_AdsExtension/system_config_form_field_select_attributes', '',
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
            'magento_code', array(
                'label'    => Mage::helper('Facebook_AdsExtension')->__('Magento Attribute'),
                'renderer' => $this->_getAttributeRenderer(),
            )
        );
        $this->addColumn(
            'feed_code', array(
                'label' => Mage::helper('Facebook_AdsExtension')->__('Feed Attribute Code'),
                'style' => 'width: 100px',
            )
        );

        $this->_addButtonLabel = Mage::helper('Facebook_AdsExtension')->__('Add Attribute');
        $this->_addAfter       = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('magento_code')),
            'selected="selected"'
        );
    }
}
