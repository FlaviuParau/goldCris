<?php
class Blugento_Multiproducts_Model_Source_Select_Category
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => Mage::helper('multiproducts')->__('Please Select'),
                'value' => ''
            ),
            array(
                'label' => Mage::helper('multiproducts')->__('New products'),
                'value' => -1
            )
        );

        $catModel = Mage::getModel('catalog/category');
        $categories = $catModel->getCollection()
            ->addAttributeToSelect('name');

        foreach ($categories as $category) {
            if ($category->hasChildren() && $category->getId() != 1) {
                $subcategories = array();

                $children = explode(',', $category->getChildren());

                foreach ($children as $child) {
                    $subcategory = $catModel->load($child);

                    array_push($subcategories, array(
                        'label' => $subcategory->getName(),
                        'value' => $subcategory->getId()
                    ));
                }

                array_push($options, array(
                    'label' => $category->getName(),
                    'value' => $subcategories
                ));
            }
        }

        return $options;
    }
}
