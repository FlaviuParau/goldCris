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

class Blugento_Cart_Block_Adminhtml_System_Config_Form_Field_Product_Select_Attributes extends Mage_Core_Block_Html_Select
{
    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render Block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
	        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
		        ->addVisibleFilter()
		        ->addFieldToFilter('frontend_input', array('text', 'select', 'textarea'));

	        foreach ($attributeCollection as $id => $attribute) {
		        $name        = $attribute->getData('frontend_label');
		        $attributeId = $attribute->getData('attribute_code');
		        
		        $this->addOption($attributeId, $name);
	        }
        }

        return parent::_toHtml();
    }

    /**
     * Get Options of the Element
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->_options;
        asort($options);

        return $options;
    }
}
