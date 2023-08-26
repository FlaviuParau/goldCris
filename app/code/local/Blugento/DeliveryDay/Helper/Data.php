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
 * @package     Blugento_DeliveryDay
 * @author      Marius Boia <marius.boia@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DeliveryDay_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled($storeId)
    {
        return (int)Mage::getStoreConfig('delivery_day/general/enabled', $storeId);
    }

    public function isWeekend($date)
    {
        return (date('N', strtotime($date)) >= 6);
    }

    public function isLegalHoliday($date, $storeId)
    {
        $legalHolidays = explode(PHP_EOL, Mage::getStoreConfig('delivery_day/general/legal_holidays', $storeId));

        if (in_array($date, array_map('trim', $legalHolidays))) {
            return true;
        }

        return false;
    }

    public function getDisplayDate($additionalDays, $storeId, $difference = null)
    {
        $currentTime = explode(' ', Mage::getModel('core/date')->date('Y-m-d H:i'));
        $currentDay = $currentTime[0];
        $currentHour = $currentTime[1];

        if (Mage::getStoreConfig('delivery_day/general/extra_day_hour', $storeId) < $currentHour && in_array($additionalDays, [0, 1])) {
            $additionalDays = $additionalDays + 1;
        }

        $date = new DateTime($currentDay);
        $date->modify('+1 days');
        while ($this->isWeekend($date->format('Y-m-d')) || $this->isLegalHoliday($date->format('Y-m-d'), $storeId)) {
            $date->modify('+1 days');
        }

        $date->modify('+'. $additionalDays - 1 . ' days');

        while ($this->isWeekend($date->format('Y-m-d')) || $this->isLegalHoliday($date->format('Y-m-d'), $storeId)) {
            $date->modify('+1 days');
        }

        if ($difference) {
            $date->modify('+' . $difference . ' days');
            while ($this->isWeekend($date->format('Y-m-d')) || $this->isLegalHoliday($date->format('Y-m-d'), $storeId)) {
                $date->modify('+1 days');
            }
        }

        return $date->format('d.m.Y') ;
    }
}
