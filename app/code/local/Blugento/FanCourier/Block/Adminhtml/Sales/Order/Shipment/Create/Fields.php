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

class Blugento_FanCourier_Block_Adminhtml_Sales_Order_Shipment_Create_Fields extends Mage_Adminhtml_Block_Template
{
    /**
     * Process
     *
     * @var Blugento_FanCourier_Model_Process
     */
    protected $process;

    /**
     * Fan Courier Service
     *
     * @var string
     */
    protected $service;

    /**
     * Blugento_FanCourier_Block_Adminhtml_Sales_Order_Shipment_Create_Fields constructor.
     */
    public function __construct()
    {
        $this->initData();
        parent::__construct();
    }

    /**
     * Initialise data
     */
    public function initData()
    {
        $this->setProcess();
        $this->setService();
    }

    /**
     * Set Process
     */
    public function setProcess()
    {
        /** @var Blugento_FanCourier_Model_Process $process */
        $process = Mage::getModel('blugento_fancourier/process');

        $this->process = $process;
    }

    /**
     * Set Fan Courier Service
     */
    public function setService()
    {
        $service = 'Standard';

        try {
            $order = $this->getShipment()->getOrder();
            $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();

            if (in_array($paymentMethod, $this->process->getCashOnDeliveryMethods())
                && Mage::getStoreConfig('carriers/bgfancourier/cash_on_delivery') == 'bank'
            ) {
                $service = Mage::getStoreConfig('carriers/bgfancourier/service_bank');
            }

            if (in_array($paymentMethod, $this->process->getCashOnDeliveryMethods())) {
                if (Mage::getStoreConfig('carriers/bgfancourier/cash_on_delivery') == 'bank') {
                    $service = Mage::getStoreConfig('carriers/bgfancourier/service_bank');
                } else {
                    $service = Mage::getStoreConfig('carriers/bgfancourier/service_cash');
                }
            } else {
                if (Mage::getStoreConfig('carriers/bgfancourier/cash_on_delivery') == 'cash') {
                    $service = Mage::getStoreConfig('carriers/bgfancourier/service_cash');
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->service = $service;
    }

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
     * Return pickup locations
     *
     * @return array
     */
    public function getServices()
    {
        $services = $this->process->getServices();

        if (is_array($services)) {
            $data = $services;
        } else {
            $data['error'] = $services;
        }
        
        return $data;
    }

    /**
     * Return default selected value
     *
     * @param string $field
     * @param string $value
     * @return string
     */
    public function getSelectedValue($field, $value)
    {
        $html = '';
        if (Mage::getStoreConfig('carriers/bgfancourier/' . $field) == $value) {
            $html = 'selected="selected"';
        }

        return $html;
    }

    /**
     * Select service in dropdown
     *
     * @param string $value
     * @return string
     */
    public function getSelectedService($value)
    {
        $html = '';

        if ($value == $this->service) {
            $html = 'selected="selected"';
        }

        return $html;
    }

    /**
     * Return default package size
     *
     * @param string $type
     * @return string
     */
    public function getPackageSize($type)
    {
        return Mage::getStoreConfig('carriers/bgfancourier/default_' . $type);
    }

    /**
     * Return package weight
     *
     * @param string $value
     * @return string
     */
    public function getPackageWeight($value)
    {
        if (!intval($value)) {
            $value = $this->getPackageSize('weight');
        }

        return intval(ceil($value));
    }

    /**
     * Check if multiple client is enabled
     *
     * @return int
     */
    public function isMultipleClientEnable()
    {
        return (int) Mage::getStoreConfig('carriers/bgfancourier/enable_multiple_client_id');
    }

    /**
     * Return client ids
     *
     * @return array
     */
    public function getClientIds()
    {
        $clientIds = Mage::getStoreConfig('carriers/bgfancourier/multiple_client_id');

        return explode(',', str_replace(' ', '', $clientIds));
    }
}
