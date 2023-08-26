<?php
/**
 * Blugento Admin Menu
 * Changed product configuration to add product attributes on checkout
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Localizer_Helper_Catalog_Product_Configuration
    extends Mage_Catalog_Helper_Product_Configuration
{
    /**
     * @var array
     */
    protected $_finished = array();

    /**
     * @var array
     */
    protected $_products = array();

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * Merge Attributes
     *
     * @param  Mage_Catalog_Model_Product_Configuration_Item_Interface $item Quote item
     * @return array Custom Options
     */
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $optionsParent = parent::getCustomOptions($item);
        $optionsSelf = $this->_getAttributes($item);
        $options = array_merge($optionsSelf, $optionsParent);

        return $options;
    }

    /**
     * Get the product for the current quote item
     *
     * @param  Mage_Catalog_Model_Product_Configuration_Item_Interface $item Quote item
     * @return Mage_Catalog_Model_Product Product Model
     */
    protected function _getProduct($item)
    {
        return $item->getProduct();
    }

    /**
     * Retrieve the product attributes
     *
     * @param  Mage_Catalog_Model_Product_Configuration_Item_Interface $item Quote item
     * @return array Attributes
     */
    protected function _getAttributes($item)
    {
        $itemId = $item->getId();
        $storeId = $item->getStoreId();
        if (!isset($this->_finished[$itemId])) {
            $this->_finished[$itemId] = true;
            $product = $this->_getProduct($item);
            $this->_attributes[$itemId] = $this->_getAdditionalData($product, $storeId);
        }

        if (count($this->_attributes[$itemId]) > 0) {
            return $this->_attributes[$itemId];
        }

        return array();
    }

    /**
     * Retrieve the attributes which are visible on the checkout page
     *
     * @param  Mage_Catalog_Model_Product $product Product Model
     * @return array Addition data as array
     */
    protected function _getAdditionalData(Mage_Catalog_Model_Product $product)
    {
        $data = array();

        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnCheckout()) {
                $value = Mage::getModel('catalog/product')
                    ->load($product->getId())
                    ->getAttributeText($attribute->getAttributeCode());
                
	            if (
	            	$attribute->getFrontendInput() === 'text' ||
		            $attribute->getFrontendInput() === 'textarea' ||
		            $attribute->getFrontendInput() === 'weight'
	            ) {
		            $value = Mage::getResourceModel('catalog/product')
			            ->getAttributeRawValue($product->getId(), $attribute->getAttributeCode(),  Mage::app()->getStore()->getStoreId());
	            }
	            
                if (!$value) {
                    continue;
                }
                
                if ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label'       => $attribute->getStoreLabel(),
                        'value'       => $value,
                        'print_value' => $value,
                        'code'        => $attribute->getAttributeCode()
                    );
                }
            }
        }

        return $data;
    }
}
