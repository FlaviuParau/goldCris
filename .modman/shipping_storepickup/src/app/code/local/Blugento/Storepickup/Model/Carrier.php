<?php
/**
 * Blugento Store Pickup Shipping Method
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Storepickup
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Storepickup_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'blugento_storepickup';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * StorePickup Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('enabled')) {
            return false;
        }

        $helper = Mage::helper('blugento_storepickup');
        $result = Mage::getModel('shipping/rate_result');

        $shippingMethods = Mage::getStoreConfig('carriers/blugento_storepickup/shipping_methods');
        $shippingMethods = unserialize($shippingMethods);

        //sort array by sort_order
        usort($shippingMethods, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        if(is_array($shippingMethods) || count($shippingMethods) > 0){

            $index = 1;

            foreach($shippingMethods as $shippingMethod) {

                $method = Mage::getModel('shipping/rate_result_method');

                $method->setCarrier('storepickup');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod($helper->getMethodCode($shippingMethod['title'], $index));
                $method->setMethodTitle($helper->getMethodTitle($shippingMethod['title'], $shippingMethod['address'], $shippingMethod['info']));
                $method->setMethodDescription($shippingMethod['info']);

                if ($request->getFreeShipping()) {
                    $method->setPrice(0);
                } else {
                    $price = $shippingMethod['price'];

                    if ($shippingMethod['additional_price']) {
                        $price += str_replace(',', '.', $shippingMethod['additional_price']) / 100 * $request->getBaseSubtotalInclTax();
                    }

                    $method->setPrice($price);
                }

                $method->setCost('0.00');

                $result->append($method);

                $index++;
            }
        }
        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $helper = Mage::helper('blugento_storepickup');
        $shippingMethods = Mage::getStoreConfig('carriers/blugento_storepickup/shipping_methods');
        $shippingMethods = unserialize($shippingMethods);

        //sort array by sort_order
        usort($shippingMethods, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        if(is_array($shippingMethods) || count($shippingMethods) > 0){

            $index = 1;
            $methods = array();
            foreach($shippingMethods as $shippingMethod) {
                $methods[$helper->getMethodCode($shippingMethod['title'], $index)] = $shippingMethod['title'];
                $index++;
            }
        } else {
            $methods = array('freeshipping' => $this->getConfigData('name'));
        }

        return $methods;
    }

    public function isTrackingAvailable()
    {
        return true;
    }
}
