<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */

class Amasty_Payrestriction_Model_Source_Carriers {

    public function toOptionArray()
    {
        $options = array();
        foreach (Mage::getSingleton('shipping/config')->getAllCarriers() as $k => $carrier) {
                $options[] = array(
                    'value' => $k,
                    'label' => $carrier->getConfigData('title'),
                );
        }

        return $options;
    }

}