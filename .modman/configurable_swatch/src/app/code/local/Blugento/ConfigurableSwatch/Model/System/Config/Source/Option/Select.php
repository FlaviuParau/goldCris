<?php

class Blugento_ConfigurableSwatch_Model_System_Config_Source_Option_Select
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array (
                array(
                    'value' => 'none-selected',
                    'label' => 'None'
                ),
                array(
                    'value' => 'first-selected',
                    'label' => 'First',
                ),
                array(
                    'value' => 'last-selected',
                    'label' => 'Last',
                )
            );
        }
        return $this->_options;
    }
}