<?php

class europaymentrate_euplatescrate_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('euplatescrate')->__('Authorize Only')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('euplatescrate')->__('Authorize and Capture')
            ),
        );
    }
}
