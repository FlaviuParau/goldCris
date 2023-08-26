<?php
class Blugento_Multiproducts_Model_Source_Select_Template
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('multiproducts')->__('Grid Template'),
                'value' => 'catalog/product/widget/multiproducts/content/multiproducts_grid.phtml'
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('List Template'),
                'value' => 'catalog/product/widget/multiproducts/content/multiproducts_list.phtml'
            )
        );
    }
}
