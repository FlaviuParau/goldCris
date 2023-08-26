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
class Blugento_AdminTheme_Model_Observer
{
    /**
     * Redirect admin login page to base url
     *
     * @param Varien_Event_Observer $observer
     */
    public function redirectAdminToBaseUrl(Varien_Event_Observer $observer)
    {
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_login') {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $baseUrl = Mage::getStoreConfig('web/unsecure/base_url');

            if (stripos($currentUrl, $baseUrl) === false) {
                try {
                    Mage::getSingleton('core/cookie')->delete('adminhtml');
                } catch (Exception $e) {
                    Mage::logException($e);
                }

                $adminBaseUrl = Mage::getUrl('adminhtml');
                Mage::app()->getFrontController()->getResponse()->setRedirect($adminBaseUrl);
                Mage::app()->getResponse()->sendResponse();
                exit;
            }
        }
    }
}