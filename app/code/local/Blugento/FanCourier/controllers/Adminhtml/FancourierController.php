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

class Blugento_FanCourier_Adminhtml_FancourierController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Print AWB
     */
    public function printAwbAction()
    {
        /** @var Mage_Sales_Model_Order $orderModel */
        $orderModel = Mage::getModel('sales/order');

        /** @var Blugento_FanCourier_Model_Process $process */
        $process = Mage::getModel('blugento_fancourier/process');

        $orderId = $this->getRequest()->getParam('order_id');
        $order = $orderModel->load($orderId);

        $trackNumbers = array();
        foreach ($order->getTracksCollection() as $track) {
            if ($track->getCarrierCode() == 'bgfancourier') {
                $trackNumbers[] = $track->getNumber();
            }
        }

        if (count($trackNumbers) > 0) {
            if (count($trackNumbers) > 1) {
                $result = $process->printMultipleAwb($trackNumbers, $order->getIncrementId(), $orderId);
            } else {
                $result = $process->printSingleAwb($trackNumbers[0], $orderId);
            }

            if (isset($result['error'])) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Cannot get pdf file(s) from Fan Courier. Error(s): ') . $result['error']);
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('There is no Fan Courier AWB number for this order.'));
        }

        $this->_redirectReferer();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
