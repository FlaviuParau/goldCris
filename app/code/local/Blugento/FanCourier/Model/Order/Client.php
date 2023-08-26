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

class Blugento_FanCourier_Model_Order_Client extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_fancourier/order_client');
    }

    /**
     * Return client id by order id
     *
     * @param int $orderId
     * @return int|null
     */
    public function getClientIdByOrderId($orderId)
    {
        $query = 'SELECT client_id FROM blugento_fancourier_order_client WHERE order_id = ' . $orderId;

        try {
            return Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }
}
