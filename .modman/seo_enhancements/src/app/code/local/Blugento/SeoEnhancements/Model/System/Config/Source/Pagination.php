<?php

class Blugento_SeoEnhancements_Model_System_Config_Source_Pagination
{
    public function toOptionArray()
    {
        $options = array(
            array('value' => 1, 'label' => 'No pagination in canonical'),
            array('value' => 2, 'label' => 'Pagination in canonical'),
            array('value' => 3, 'label' => 'Only page 1 without pagination in canonical'),
        );

        return $options;
    }
}