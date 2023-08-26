<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Product_SuperAttributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * @var Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Product_SuperAttributes _attributesRenderer
	 */
	protected $_attributesRenderer;
	
	/**
	 * Retrieve Attribute Column Renderer
	 *
	 * @return Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Product_SuperAttributes
	 */
	protected function _getAttributeRenderer()
	{
		if (!$this->_attributesRenderer) {
			$this->_attributesRenderer = $this->getLayout()->createBlock(
				'blugento_richsnippets/adminhtml_system_config_form_field_product_super_attributes',
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
			'name', array(
				'label' => Mage::helper('blugento_richsnippets')->__('Name')
			)
		);
		
		$this->addColumn(
			'attributes', array(
				'label'    => Mage::helper('blugento_richsnippets')->__('Attribute'),
				'renderer' => $this->_getAttributeRenderer()
			)
		);
		
		$this->_addButtonLabel = Mage::helper('blugento_richsnippets')->__('Add new attribute');
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
	}
}
