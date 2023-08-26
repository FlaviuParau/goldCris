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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Model_Adminhtml_Observer
{
    public function saveContent(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        $cms_page = $event->getPage();

        if ($cms_page->getIdentifier() == 'home') {
            $helper = Mage::helper('blugento_homepagemanager');
            $helper->saveLayout($event->getRequest()->getParam('form_values'), $event->getRequest()->getParams());
        }
    }

    public function skipWidgets(Varien_Event_Observer $observer)
    {
        $skipped = array('catalog/product_widget_new', 'reports/product_widget_compared');
        $skipped = Mage::getSingleton('widget/widget_config')->encodeWidgetsToQuery($skipped);

        Mage::app()->getRequest()->setParam('skip_widgets', $skipped);

        return $this;
    }
}
