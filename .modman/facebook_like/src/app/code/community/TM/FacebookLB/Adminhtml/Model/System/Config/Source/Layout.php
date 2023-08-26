<?php

class TM_FacebookLB_Adminhtml_Model_System_Config_Source_Layout
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'button_count', 'label'=>'button_count'),
            array('value'=>'button', 'label'=>'button'),
            array('value'=>'standard', 'label'=>'standard'),
            array('value'=>'box_count', 'label'=>'box_count')
        );
    }
}
