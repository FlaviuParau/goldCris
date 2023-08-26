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

class Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Organization_Contact extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	/**
	 * Add columns, change button labels etc.
	 */
	protected function _prepareToRender()
	{
		$this->addColumn(
			'type', array(
				'label' => Mage::helper('blugento_richsnippets')->__('Type'),
			)
		);
		
		$this->addColumn(
			'telephone', array(
				'label' => Mage::helper('blugento_richsnippets')->__('Telephone')
			)
		);
		
		$this->addColumn(
			'email', array(
				'label' => Mage::helper('blugento_richsnippets')->__('Email')
			)
		);
		
		$this->_addAfter = false;
	}
}
