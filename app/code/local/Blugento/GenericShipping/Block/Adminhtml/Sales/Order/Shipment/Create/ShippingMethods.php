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

class Blugento_GenericShipping_Block_Adminhtml_Sales_Order_Shipment_Create_ShippingMethods extends Mage_Adminhtml_Block_Template
{
    /**
     * Blugento shipping methods
     *
     * @var array
     */
    protected $shippingMethods = array ('bgurgentcargus', 'bgfancourier', 'bgsamedaycourier', 'bgsamedayeasybox',
        'nemoexpress'); //TODO add all Blugento shipping methods here

    /**
     * @var bool
     */
    protected $isManual = true;

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Return all Blugento available carriers
     *
     * @return array
     */
    public function getAvailableCarriers()
    {
        $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();

        $methods = array();
        $displayedMethods = array ();
        foreach ($carriers as $carrier) {
            if (in_array($carrier->getId(), $this->shippingMethods)) {
                $methods[$carrier->getId()] = array(
                    'title' => Mage::getStoreConfig('carriers/' . $carrier->getId() . '/title'),
                    'name' => Mage::getStoreConfig('carriers/' . $carrier->getId() . '/name'),
                );

                $displayedMethods[] = $carrier->getId() . '_' . $carrier->getId();
            }
        }

        $order = $this->getShipment()->getOrder();
        $orderShippingMethodDesc = explode('-', $order->getShippingDescription());
        $orderShippingMethod = $order->getShippingMethod();

        if (in_array($orderShippingMethod, $displayedMethods)) {
            $additionalMethod = array(
                'title' => 'Manual',
                'name' => 'Manual'
            );
        } else {
            $additionalMethod = array(
                'title' => $orderShippingMethodDesc[0],
                'name' => $orderShippingMethodDesc[1]
            );
        }

        $methods['manual'] = $additionalMethod;

        return $methods;
    }

    /**
     * Return price and currency
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getShippingPrice($order)
    {
        $price = number_format($order->getShippingInclTax(), 2);

        return $price . ' ' . $order->getOrderCurrencyCode();
    }

    /**
     * Check if order shipping method is one of the available shipping methods and check radio button
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $code
     * @return string
     */
    public function isSelected($order, $code)
    {
        $orderShipping = $order->getShippingMethod();

        $html = '';
        if (($orderShipping == $code . '_' . $code)
            || (strpos($orderShipping, $code . '_' . $code) !== false)
            || ($code == 'manual' && $this->isManual)
        ) {
            $html = 'checked="checked"';
            $this->isManual = false;
        }

        return $html;
    }
}