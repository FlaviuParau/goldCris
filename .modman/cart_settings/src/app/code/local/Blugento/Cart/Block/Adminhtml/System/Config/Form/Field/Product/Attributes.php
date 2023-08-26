<?php
/**
 * Blugento Cart Settings
 * Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @category    Blugento
 * @package     Blugento_Cart
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
	 * @var Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Attributes _attributeSetRenderer
	 */
	protected $_attributeSetRenderer;

    /**
     * @var Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Attributes _attributesRenderer
     */
    protected $_attributesRenderer;

    	/**
	 * Retrieve AttributeSet Column Renderer
	 *
	 * @return Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Attributes
	 */
	protected function _getAttributeSetRenderer()
	{
		if (!$this->_attributeSetRenderer) {
			$this->_attributeSetRenderer = $this->getLayout()->createBlock(
				'blugento_cart/adminhtml_system_config_form_field_product_select_attributesSet',
				'',
				array(
					'is_render_to_js_template' => true
				)
			);
		}
		
		return $this->_attributeSetRenderer;
	}

    /**
     * Retrieve Attribute Column Renderer
     *
     * @return Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Attributes
     */
    protected function _getAttributeRenderer()
    {
        if (!$this->_attributesRenderer) {
            $this->_attributesRenderer = $this->getLayout()->createBlock(
                'blugento_cart/adminhtml_system_config_form_field_product_select_attributes',
                '',
                array(
                	'is_render_to_js_template' => true
                )
            );
        }

        return $this->_attributesRenderer;
    }

    /**
     * Add columns, change button labels etc.
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
		    'attribute_set', array(
			    'label'    => Mage::helper('blugento_cart')->__('Attribute Set'),
			    'renderer' => $this->_getAttributeSetRenderer()
		    )
	    );

	    $this->addColumn(
		    'attributes', array(
			    'label'    => Mage::helper('blugento_cart')->__('Attribute'),
			    'renderer' => $this->_getAttributeRenderer()
		    )
	    );

        $this->addColumn(
        	'values', array(
        		'label' => Mage::helper('blugento_richsnippets')->__('Value')
            )
        );

        $this->_addButtonLabel = Mage::helper('blugento_cart')->__('Add new attribute');
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
            'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('attributes')),
            'selected="selected"'
        );
	
	    $row->setData(
		    'option_extra_attr_' . $this->_getAttributeSetRenderer()->calcOptionHash($row->getData('attribute_set')),
		    'selected="selected"'
	    );
    }
}
