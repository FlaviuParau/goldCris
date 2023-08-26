<?php

class Blugento_Googleshopping_Block_Adminhtml_Config_Form_Field_Filter
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var array
     */
    protected $_renders = array();

    /**
     * Blugento_Googleshopping_Block_Adminhtml_Config_Form_Field_Filter constructor.
     */
    public function __construct()
    {
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $rendererAttributes = $layout->createBlock(
            'googleshopping/adminhtml_config_form_renderer_select',
            '',
            array('is_render_to_js_template' => true)
        );
        $rendererAttributes->setOptions(
            Mage::getModel('googleshopping/adminhtml_system_config_source_attribute')->toOptionArray()
        );

        $rendererConditions = $layout->createBlock(
            'googleshopping/adminhtml_config_form_renderer_select',
            '',
            array('is_render_to_js_template' => true)
        );
        $rendererConditions->setOptions(
            Mage::getModel('googleshopping/adminhtml_system_config_source_conditions')->toOptionArray()
        );

        $rendererTypes = $layout->createBlock(
            'googleshopping/adminhtml_config_form_renderer_select',
            '',
            array('is_render_to_js_template' => true)
        );

        $rendererTypes->setOptions(
            Mage::getModel('googleshopping/adminhtml_system_config_source_producttypes')->toOptionArray()
        );

        $this->addColumn(
            'attribute', array(
            'label'    => Mage::helper('googleshopping')->__('Attribute'),
            'style'    => 'width:100px',
            'renderer' => $rendererAttributes
            )
        );

        $this->addColumn(
            'condition', array(
            'label'    => Mage::helper('googleshopping')->__('Condition'),
            'style'    => 'width:100px',
            'renderer' => $rendererConditions
            )
        );

        $this->addColumn(
            'value', array(
            'label' => Mage::helper('googleshopping')->__('Value'),
            'style' => 'width:100px',
            )
        );

        $this->addColumn(
            'product_type', array(
                'label'    => Mage::helper('googleshopping')->__('Apply To'),
                'style'    => 'width:150px',
                'renderer' => $rendererTypes
            )
        );

        $this->_renders['attribute'] = $rendererAttributes;
        $this->_renders['condition'] = $rendererConditions;

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('googleshopping')->__('Add Filter');
        parent::__construct();
    }

    /**
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        foreach ($this->_renders as $key => $render) {
            $row->setData(
                'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
                'selected="selected"'
            );
        }
    }

}