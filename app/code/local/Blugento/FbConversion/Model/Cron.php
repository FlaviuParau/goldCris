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

class Blugento_FbConversion_Model_Cron extends Mage_Core_Model_Abstract
{
    /**
     * Send data events
     */
    public function sendEventData()
    {
        foreach (Mage::app()->getStores() as $store) {
            if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $store->getId())) {
                continue;
            }

            /** @var Blugento_FbConversion_Model_Event $eventModel */
            $eventModel = Mage::getModel('blugento_fbconversion/event');

            /** @var Blugento_FbConversion_Model_Api $apiModel */
            $apiModel = Mage::getModel('blugento_fbconversion/api');

            $eventCollection = $eventModel->getCollection()->addFieldToFilter('store_id', $store->getId());

            $eventIds = [];
            foreach ($eventCollection as $event) {
                $apiModel->sendEvent($event);

                $eventIds[] = $event->getId();
            }

            if (count($eventIds)) {
                $eventModel->deleteEvents($eventIds);
            }
        }
    }
}
