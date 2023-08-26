<?php

class Blugento_Googleshopping_Block_Adminhtml_Config_Form_Field_Shipping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var array
     */
    protected $_renders = array();

    /**
     * Blugento_Googleshopping_Block_Adminhtml_Config_Form_Field_Shipping constructor.
     */
    public function __construct()
    {
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();

        $rendererCoutries = $layout->createBlock(
            'googleshopping/adminhtml_config_form_renderer_select',
            '',
            array('is_render_to_js_template' => true)
        );

        $rendererCoutries->setOptions(
            Mage::getModel('googleshopping/adminhtml_system_config_source_countries')->toOptionArray()
        );

        $this->addColumn(
            'country', array(
                'label'    => Mage::helper('googleshopping')->__('Country'),
                'style'    => 'width:120px',
                'renderer' => $rendererCoutries
            )
        );

        $this->addColumn(
            'service', array(
                'label' => Mage::helper('googleshopping')->__('Service'),
                'style' => 'width:120px',
            )
        );

        $this->addColumn(
            'price_from', array(
                'label' => Mage::helper('googleshopping')->__('From Product Price'),
                'style' => 'width:60px',
            )
        );
        $this->addColumn(
            'price_to', array(
                'label' => Mage::helper('googleshopping')->__('To Product Price'),
                'style' => 'width:60px',
            )
        );

        $this->addColumn(
            'price', array(
                'label' => Mage::helper('googleshopping')->__('Shipping Price'),
                'style' => 'width:60px',
            )
        );

        $this->_renders['country'] = $rendererCoutries;

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('googleshopping')->__('Add Shipping');
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