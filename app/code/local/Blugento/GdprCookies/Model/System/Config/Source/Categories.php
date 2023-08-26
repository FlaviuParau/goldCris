<?php

class Blugento_GdprCookies_Model_System_Config_Source_Categories
{

    public function toOptionArray()
    {
        $categories = array(
            array('value' => 1, 'label' => 'Necessary'),
            array('value' => 2, 'label' => 'Analytics'),
            array('value' => 3, 'label' => 'Marketing')
        );

        return $categories;
    }
}