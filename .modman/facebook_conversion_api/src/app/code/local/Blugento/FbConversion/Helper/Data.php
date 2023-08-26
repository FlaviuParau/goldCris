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

class Blugento_FbConversion_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get config data
     *
     * @param string $field
     * @param int $storeId
     * @return mixed
     */
    public function getConfigurations($field, $storeId)
    {
        return Mage::getStoreConfig("blugento_fbconversion/general/$field", $storeId);
    }

    /**
     * Generate event ID
     *
     * @param string $type
     * @param string $additional
     * @return string
     */
    public function createEventId($type, $additional = '')
    {
        $hash = $additional . time();

        $visitorData = Mage::getSingleton('core/session')->getVisitorData();
        if (isset($visitorData['session_id'])) {
            $hash .= $visitorData['session_id'];
        }

        return $type . '.id.' . crc32($hash);
    }
}
