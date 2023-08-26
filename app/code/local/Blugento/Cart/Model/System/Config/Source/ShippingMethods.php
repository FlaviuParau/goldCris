<?php
/**
 * Blugento Cart Settings
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Cart
 * @link http://www.blugento.com
 */

class Blugento_Cart_Model_System_Config_Source_ShippingMethods extends Mage_Core_Helper_Abstract
{
    /**
     * Option array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = [
            'all' => [
                'label' => 'All Methods',
                'value' => 9999
            ]
        ];

        $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();

        foreach ($carriers as $carrierCode => $carrierModel) {
            try {
                $carrierMethods = $carrierModel->getAllowedMethods();
            } catch (Exception $e) {
                if ($e->getMessage() == 'Wrong Content Type.') {
                    Mage::getConfig()->saveConfig('carriers/' . $carrierCode . '/content_type', 'D');
                }
            }

            if (isset($carrierMethods) && !$carrierMethods) {
                continue;
            }

            $carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
            $methods[$carrierCode] = array(
                'label' => $carrierTitle,
                'value' => array(),
            );

            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = array(
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                );
            }
        }
        return $methods;
    }
}
