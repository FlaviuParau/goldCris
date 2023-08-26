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

class Blugento_GdprUserData_Model_Config_Status_Description extends Mage_Core_Model_Abstract
{

    static public function toOptionArray()
    {
        return array(
            'pending' => [
                'export' => Mage::helper('blugento_gdpruserdata')->__('After the request is made and if there is data to export'),
                'delete' => Mage::helper('blugento_gdpruserdata')->__('After the request is made and if there is data to delete')
            ],

            'processed' => [
                'export' => Mage::helper('blugento_gdpruserdata')->__('After the email with download link is sent'),
                'delete' => Mage::helper('blugento_gdpruserdata')->__('After the store owner accept/reject the delete request')
            ],

            'no data available' => [
                'export' => Mage::helper('blugento_gdpruserdata')->__('After the request is made and if there is no data to export'),
                'delete' => Mage::helper('blugento_gdpruserdata')->__('After the request is made and if there is no data to delete')
            ],

            'account exists rejection' => [
                'delete' => Mage::helper('blugento_gdpruserdata')->__('After a delete request is made but there is an account created with this email')
            ],

            'completed' => [
                'export' => Mage::helper('blugento_gdpruserdata')->__('After the download link is accessed and the data is downloaded'),
                'delete' => Mage::helper('blugento_gdpruserdata')->__('After the data is deleted and a confirmation email was send to the customer')
            ],

            'deleted' => [
                'export' => Mage::helper('blugento_gdpruserdata')->__('The export requests with status "processed" older than 24 hours'),
                'delete' => Mage::helper('blugento_gdpruserdata')->__('The delete requests with status "processed" older than 24 hours')
            ]
        );
    }
}