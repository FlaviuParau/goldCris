<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */
class Blugento_Pdf_Block_Adminhtml_System_Config_Form_Field_Notes
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var Blugento_Pdf_Block_Adminhtml_System_Config_Form_Field_Notes_Country
     */
    protected $_countryRenderer;

    /**
     * Retrieve country column renderer
     *
     * @return Blugento_Pdf_Block_Adminhtml_System_Config_Form_Field_Notes_Country
     */
    protected function _getCountryRenderer()
    {
        if ( ! $this->_countryRenderer) {
            $this->_countryRenderer = $this->getLayout()->createBlock(
                'blugento_pdf/adminhtml_system_config_form_field_notes_country', '',
                array('is_render_to_js_template' => true)
            );
        }

        return $this->_countryRenderer;
    }

    /**
     * Add columns, change button labels etc.
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'country', array(
                'label'    => Mage::helper('blugento_pdf')->__('Shipping Country'),
                'renderer' => $this->_getCountryRenderer()
            )
        );
        $this->addColumn(
            'note', array(
                'label' => Mage::helper('blugento_pdf')->__('Note')
            )
        );
        $this->_addButtonLabel = Mage::helper('blugento_pdf')->__('Add Note');
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
            'option_extra_attr_' . $this->_getCountryRenderer()->calcOptionHash($row->getData('country')),
            'selected="selected"'
        );
    }

}
