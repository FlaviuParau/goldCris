<?php

class Blugento_SmartbillSync_Model_System_Config_Source_Hours
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0.5, 'label'=>'30m'),
            array('value' => 1, 'label'=>'1h'),
            array('value' => 4, 'label'=>'4h'),
            array('value' => 12, 'label'=>'12h'),
            array('value' => 24, 'label'=>'24h'),
        );
    }
}
