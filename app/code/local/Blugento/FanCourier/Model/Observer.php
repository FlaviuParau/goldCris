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

class Blugento_FanCourier_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Generate AWB.
     *
     * @param $observer
     */
    public function generateAwb(Varien_Event_Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $data = $controller->getRequest()->getParams();

        if ($data['shipment']['shipping_method'] == 'bgfancourier') {
            $errorMessage = '';

            if (isset($data['shipment']['fancourier']['service'])) {
                try {
                    $orderData = $this->getOrderData($data);

                    if (count($orderData)) {
                        /** @var Blugento_FanCourier_Model_Api $api */
                        $api = Mage::getModel('blugento_fancourier/api');

                        $response = $api->generateAwb($orderData);

                        if (isset($response['error'])) {
                            $errorMessage = $response['error'];
                        } else {
                            // set client id on order
                            $this->saveClientId($orderData);

                            // set tracking number
                            $awb = array(
                                'carrier_code' => 'bgfancourier',
                                'title' => Mage::getStoreConfig('carriers/bgfancourier/title'),
                                'number' => $response,
                            );
                            $controller->getRequest()->setPost('tracking', array(1 => $awb));

                            Mage::getSingleton('core/session')
                                ->addNotice(
                                    Mage::helper('blugento_fancourier')
                                        ->__('The AWB was created on Fan Courier platform.')
                                );
                        }
                    } else {
                        $errorMessage = Mage::helper('blugento_fancourier')->__('Cannot retrieve order data.');
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $errorMessage = Mage::helper('blugento_fancourier')->__('Cannot retrieve order data. Check exception logs for more info.');
                }
            } else {
                $errorMessage = Mage::helper('blugento_fancourier')->__('Service was not set.');
            }

            if ($errorMessage) {
                $controller->getRequest()->setDispatched(true);
                $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('core/session')->addError($errorMessage);

                Mage::app()->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());
            }
        }
    }

    /**
     * Add print awb button to admin order view page
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addPrintAwbButton(Varien_Event_Observer $observer)
    {
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');

        if (!$block) {
            return $this;
        }

        $order = Mage::registry('current_order');

        $hasFanAwb = false;
        foreach ($order->getTracksCollection() as $track){
            if ($track->getCarrierCode() == 'bgfancourier') {
                $hasFanAwb = true;
                break;
            }
        }

        if ($hasFanAwb) {
            $url = Mage::helper("adminhtml")->getUrl("adminhtml/fancourier/printAwb", array('order_id' => $order->getId()));

            $block->addButton(
                'blugento_fancourier_printawb',
                array(
                    'label' => Mage::helper('blugento_fancourier')->__('Print Fan Courier AWB'),
                    'onclick' => 'setLocation(\'' . $url . '\')',
                    'class' => 'go',
                ), 0, 1
            );
        }

        return $this;
    }

    /**
     * Set client ID per order
     * @param array $orderData
     */
    protected function saveClientId($orderData)
    {
        $clientId = $orderData['client_id'] ?: Mage::getStoreConfig('carriers/bgfancourier/client_id');

        /** @var Blugento_FanCourier_Model_Order_Client $client */
        $client = Mage::getModel('blugento_fancourier/order_client');

        try {
            $client->setOrderId($orderData['entity_id']);
            $client->setClientId($clientId);
            $client->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return order data, order shipping address data and order items data
     *
     * @param array $formData
     * @return array
     * @throws Mage_Core_Exception
     */
    private function getOrderData($formData)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($formData['order_id']);

        // set order data
        $data = $order->getData();

        // set order shipping address data
        $shippingAddress = $order->getShippingAddress();
        $data['shipping_address'] = $shippingAddress->getData();

        // set order payment method
        $data['payment_method'] = $order->getPayment()->getMethodInstance()->getCode();

        // set order items data
        $orderItems = $order->getAllItems();

        $shippingQty = 0;
        foreach ($orderItems as $item) {
            $itemData = array();
            foreach ($formData['shipment']['items'] as $itemId => $itemQty) {
                if ($item->getId() == $itemId && $itemQty > 0) {
                    $itemData = $item->getData();

                    if ($item->getQtyOrdered() != $itemQty) {
                        $itemData['qty_ordered'] = $itemQty;
                    }

                    $shippingQty += $itemQty;
                    break;
                }
            }
            if (count($itemData) > 0) {
                $data['items'][] = $itemData;
            }
        }

        // set fan courier additional info
        $noOfParcels = $formData['shipment']['fancourier']['number_of_parcels'];
        $noOfParcels = is_numeric($noOfParcels) && $noOfParcels > 0 ? $noOfParcels : 1;

        $data['envelope'] = $formData['shipment']['fancourier']['delivery_type'] == 'envelope' ? $noOfParcels : 0;
        $data['parcel'] = $formData['shipment']['fancourier']['delivery_type'] == 'parcel' ? $noOfParcels : 0;
        $data['observations'] = $formData['shipment']['fancourier']['observations'];
        $data['order_amount'] = $formData['shipment']['fancourier']['order_amount'];
        $data['service'] = $formData['shipment']['fancourier']['service'];
        $data['weight'] = $formData['shipment']['fancourier']['weight'];
        $data['client_id'] = isset($formData['shipment']['fancourier']['client']) ? $formData['shipment']['fancourier']['client'] : null;

        // set shipping quantity
        $data['shipping_qty'] = $shippingQty;

        return $data;
    }
}
