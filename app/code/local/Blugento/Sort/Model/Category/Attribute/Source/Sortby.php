<?php

class Blugento_Sort_Model_Category_Attribute_Source_Sortby
    extends Mage_Catalog_Model_Category_Attribute_Source_Sortby
{

    protected $sortingOptions = array('Popularity', 'New Products', 'Discount');

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(array(
                'label' => Mage::helper('catalog')->__('Best Value'),
                'value' => 'position'
            ));

            foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
                $this->_options[] = array(
                    'label' => Mage::helper('catalog')->__($attribute['frontend_label']),
                    'value' => $attribute['attribute_code']
                );
            }

            foreach($this->sortingOptions as $option){

                $helper = Mage::helper('blugento_sort');
                $value = $helper->getSortingOptionValue($option);

                if($helper->isSortOptionEnabled($value)) {
                    $this->_options[] = array(
                        'label' => Mage::helper('catalog')->__($option),
                        'value' => $value
                    );
                }
            }
        }
        return $this->_options;
    }
}
