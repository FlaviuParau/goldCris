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
 * @package     Blugento_FullFeed
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FullFeed_Block_Adminhtml_System_Config_Form_Field_Attributes
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * @var Blugento_FullFeed_Block_Adminhtml_System_Config_Form_Field_Select_Attributes
     */
    protected $_attributeRenderer;

    /**
     * Retrieve country column renderer
     *
     * @return Blugento_FullFeed_Block_Adminhtml_System_Config_Form_Field_Select_Attributes
     */
    protected function _getAttributeRenderer()
    {
        if ( ! $this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                'blugento_fullfeed/adminhtml_system_config_form_field_select_attributes', '',
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
                'label'    => Mage::helper('blugento_fullfeed')->__('Magento Attribute'),
                'renderer' => $this->_getAttributeRenderer(),
            )
        );
        $this->addColumn(
            'feed_code', array(
                'label' => Mage::helper('blugento_fullfeed')->__('Feed Attribute Code'),
                'style' => 'width: 100px',
            )
        );
        $this->addColumn(
            'mapped_values', array(
                'label' => Mage::helper('blugento_fullfeed')->__('Map Values'),
                'style' => 'width: 80px'
            )
        );
        $this->addColumn(
            'sort_order', array(
                'label' => Mage::helper('blugento_fullfeed')->__('Sort Order'),
                'style' => 'width: 70px'
            )
        );
        $this->_addButtonLabel = Mage::helper('blugento_fullfeed')->__('Add Attribute');
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
