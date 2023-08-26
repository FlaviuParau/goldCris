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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Set campaign layout when cms page is saved.
     *
     * @param $observer
     */
    public function setCampaignLayout(Varien_Event_Observer $observer)
    {
        $page = $observer->getEvent()->getObject();

        $campaign = $this->_getModel();
        $campaign->setCampaignsLayout($page->getIdentifier(), $page->getRootTemplate());
    }

    /**
     * Redirect homepage to campaign landing page
     *
     * @param Varien_Event_Observer $observer
     */
    public function redirectToCampaign(Varien_Event_Observer $observer)
    {
        $frontController = Mage::app()->getFrontController();

        if (Mage::getSingleton('cms/page')->getIdentifier() == 'home'
            && $frontController->getRequest()->getRouteName() == 'cms') {
            if (Mage::getStoreConfig('blugento_campaign/general/enabled')) {
                $campaign = $this->_getModel()->getActiveCampaign();
                $page = $campaign->getCmsPage();

                if ($page && !Mage::getSingleton('core/cookie')->get('blugento-campaign')) {
                    $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                    $params = explode('?', $currentUrl);

                    $url = Mage::getBaseUrl() . $page;
                    if (isset($params[1])) {
                        $url .= '?' . $params[1];
                    }

                    $frontController->getResponse()->setRedirect($url);
                }
            }
        }
    }

    /**
     * Return campaign model
     *
     * @return Blugento_Campaign_Model_Campaign
     */
    private function _getModel()
    {
        /** @var Blugento_Campaign_Model_Campaign $campaign */
        $campaign = Mage::getModel('blugento_campaign/campaign');

        return $campaign;
    }
}