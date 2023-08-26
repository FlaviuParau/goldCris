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

class Blugento_Contacts_Model_Config
{
    const CONFIG_XML_PATH_CONTACTS_BLOCK = 'contacts/content/cms_block';

    /**
     * Obtain the CMS block identifier from config.
     *
     * @param mixed $store
     * @return string|null
     */
    public function getContactsBlockIdentifier($store = null)
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CONTACTS_BLOCK, $store);
    }
}
