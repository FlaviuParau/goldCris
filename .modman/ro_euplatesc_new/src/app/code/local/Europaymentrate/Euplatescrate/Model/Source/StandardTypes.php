<?php

class europaymentrate_euplatescrate_Model_Source_StandardTypes
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'IPN',
                'label' => Mage::helper('euplatescrate')->__('IPN')
            ),
        );
    }
}
