<?php
class Blugento_CategoryShowcase_Model_Catalog_Category_Attribute_Source_Mode
    extends Mage_Catalog_Model_Category_Attribute_Source_Mode
{
    /**
     * Set a new display mode for categories
     */
    public function getAllOptions()
    {
        $options = parent::getAllOptions();

        $optionsNew = array(
            array(
                'value' => Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY,
                'label' => Mage::helper('blugento_categoryshowcase')->__('Subcategories only'),
            ),
            array(
                'value' => Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_PRODUCTS,
                'label' => Mage::helper('blugento_categoryshowcase')->__('Subcategories and products')
            ),
            array(
                'value' => Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_PAGE,
                'label' => Mage::helper('blugento_categoryshowcase')->__('Static block and subcategories')
            ),
            array(
                'value' => Blugento_CategoryShowcase_Model_Catalog_Category::DM_SUBCATEGORY_MIXED_ALL,
                'label' => Mage::helper('blugento_categoryshowcase')->__('Static block, subcategories and products')
            )
        );

        $this->_options = array_merge($options, $optionsNew);

        return $this->_options;
    }
}
