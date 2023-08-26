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

class Blugento_Richsnippets_Block_Adminhtml_System_Config_Form_Field_Product_Select_Attributes extends Mage_Core_Block_Html_Select
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
