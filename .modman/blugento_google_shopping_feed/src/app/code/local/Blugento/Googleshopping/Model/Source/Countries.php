<?php

class Blugento_Googleshopping_Model_Source_Countries
{

    /**
     * @return mixed
     */
    public function toOptionArray()
    {
        return Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(true);
    }

}