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

class Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Organization_Address extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * @var Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Organization_Address _organizationAddress
	 */
	protected $_organizationAddress;
	
	/**
	 * Retrieve attribute column renderer
	 *
	 * @return Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Organization_Address
	 */
	protected function _getAttributeRenderer()
	{
		if (!$this->_organizationAddress) {
			$this->_organizationAddress = $this->getLayout()->createBlock(
				'blugento_richsnippets/adminhtml_system_config_form_field_organization_select_address',
				'',
				array(
					'is_render_to_js_template' => true
				)
			);
		}
		
		return $this->_organizationAddress;
	}
	
	/**
	 * Add columns, change button labels etc.
	 */
	protected function _prepareToRender()
	{
		$this->addColumn(
			'street', array(
				'style' => 'width: 250px',
				'label' => Mage::helper('blugento_richsnippets')->__('Street'),
			)
		);
		
		$this->addColumn(
			'city', array(
				'style' => 'width: 200px',
				'label' => Mage::helper('blugento_richsnippets')->__('City')
			)
		);
		
		$this->addColumn(
			'region', array(
				'style' => 'width: 200px',
				'label' => Mage::helper('blugento_richsnippets')->__('Region')
			)
		);
		
		$this->addColumn(
			'postal_code', array(
				'style' => 'width: 100px',
				'label' => Mage::helper('blugento_richsnippets')->__('Postal Code')
			)
		);
		
		$this->addColumn(
			'country', array(
				'label'    => Mage::helper('blugento_richsnippets')->__('Country'),
				'renderer' => $this->_getAttributeRenderer()
			)
		);
		
		$this->_addAfter = false;
	}
	
	/**
	 * Prepare existing row data object
	 *
	 * @param Varien_Object
	 */
	protected function _prepareArrayRow(Varien_Object $row)
	{
		$row->setData(
			'option_extra_attr_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('country')),
			'selected="selected"'
		);
	}
}
