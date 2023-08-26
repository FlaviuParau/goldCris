<?php

class Blugento_FullFeed_Model_System_Config_Source_Categorytype
{

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = array(
                array('value' => 'include', 'label' => 'Include by Category'),
                array('value' => 'exclude', 'label' => 'Exclude by Category'),
            );
        }

        return $this->options;
    }
}
