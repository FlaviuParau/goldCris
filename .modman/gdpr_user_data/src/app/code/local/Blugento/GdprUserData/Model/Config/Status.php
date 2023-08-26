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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_Model_Config_Status extends Mage_Core_Model_Abstract
{

    static public function toOptionArray()
    {
        return array(
            'pending'                  => Mage::helper('blugento_gdpruserdata')->__('Pending'),
            'processed'                => Mage::helper('blugento_gdpruserdata')->__('Processed'),
            'no data available'        => Mage::helper('blugento_gdpruserdata')->__('No data available'),
            'account exists rejection' => Mage::helper('blugento_gdpruserdata')->__('Account exists rejection'),
            'completed'                => Mage::helper('blugento_gdpruserdata')->__('Completed'),
            'deleted'                  => Mage::helper('blugento_gdpruserdata')->__('Deleted')
        );
    }
}