<?php
/**
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
 * @package     Blugento_Contacts
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Contacts_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Obtain the identifier of the CMS block to be displayed on contacts page.
     *
     * @param mixed $store
     * @return string|null
     */
    public function getContactsBlockIdentifier($store = null)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '';
        }
        return Mage::getModel('blugento_contacts/config')->getContactsBlockIdentifier($store);
    }
}
