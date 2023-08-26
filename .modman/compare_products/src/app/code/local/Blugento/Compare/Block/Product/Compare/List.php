<?php

class Blugento_Compare_Block_Product_Compare_List extends Mage_Catalog_Block_Product_Compare_List
{
    /**
     * Retrieve Product Compare Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
        }

        foreach($this->_attributes as $attribute) {
            $size  = sizeof($this->getItems());
            $count = 0;

            foreach($this->getItems() as $item) {
                $itemAttribute = $item->getData($attribute->getName());
                $trimAttribute = trim($itemAttribute);
                if(is_null($itemAttribute) || empty($trimAttribute)) {
                    $count++;
                }

                if($count == $size) {
                    unset($this->_attributes[$attribute->getName()]);
                }
            }
        }

        return $this->_attributes;
    }
}