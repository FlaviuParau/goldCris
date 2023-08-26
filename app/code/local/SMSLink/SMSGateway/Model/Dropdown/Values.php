<?php

class SMSLink_SMSGateway_Model_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '0',
                'label' => 'POST',
            ),
            array(
                'value' => '1',
                'label' => 'GET',
            )                 
        );
        
    }
    
}