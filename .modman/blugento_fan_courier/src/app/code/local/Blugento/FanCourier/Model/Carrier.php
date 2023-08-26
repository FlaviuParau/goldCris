<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Blugento_FanCourier_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'bgfancourier';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Check if tracking is available or not
     *
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result|bool|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('display_on_frontend') && Mage::getDesign()->getArea() == 'frontend') {
            return false;
        }

        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        // check if sales rule is applied for free shipping
        if ($request->getFreeShipping() === true) {
            $shippingPrice = 0;
        } else {
            $shippingPrice = (double)$this->getConfigData('price');

            if ($this->checkFreeShipping($request->getBaseSubtotalInclTaxWithDiscount())) {
                $shippingPrice = 0;
            } else if ($this->getConfigData('dynamic_price')) {
                $dynamicPrice = $this->getDynamicPrice($request);

                if ($dynamicPrice) {
                    $shippingPrice = (double)$dynamicPrice;
                }
            } else {
                $shippingPrice += ((double)$this->getConfigData('handling_fee') * (double)$request->getPackageWeight());
            }
        }

        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($this->_code);
        $rate->setMethodTitle($this->getConfigData('name'));
        $rate->setMethodDescription($this->getConfigData('description'));
        $rate->setPrice($shippingPrice);
        $rate->setCost(0);

        $result->append($rate);

        return $result;
    }

    /**
     * Return tracking information
     *
     * @param $number
     * @return mixed
     */
    public function getTrackingInfo($number)
    {
        $deliveryData['title'] = $this->getConfigData('title');
        $deliveryData['number'] = $number;
        $deliveryData['carrier_code'] = $this->_code;
        return $deliveryData;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array (
            'bgfancourier' => $this->getConfigData('name'),
        );
    }

    /**
     * Check if order has free shipping
     *
     * @param float $amount
     * @return bool
     */
    protected function checkFreeShipping($amount)
    {
        $free = $this->getConfigData('free_shipping_over');

        if ($free && $free != '' && $free > 0 && $amount >= $free) {
            return true;
        }

        return false;
    }

    /**
     * Calculate dynamic price
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return |null
     */
    protected function getDynamicPrice(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Blugento_FanCourier_Model_Api $api */
        $api = Mage::getModel('blugento_fancourier/api');

        $price = $api->getShippingPrice($request);

        if (isset($price['error'])) {
            if (is_array($price['error'])) {
                Mage::log('blugento_fancourier: ' . implode('; ', $price['error']));
            } else {
                Mage::log('blugento_fancourier: ' . $price['error']);
            }
            return null;
        }

        if (!is_numeric($price) || $price == 0) {
            Mage::log('blugento_fancourier: ' . $price);
            return null;
        }

        return $price;
    }
}
