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

class Blugento_DesignCustomiser_Model_Adminhtml_Observer
{
    /**
     * Disable modules
     * @param $observer
     */
    public function disableModules($observer)
    {
        try {
            $model = Mage::getSingleton('blugento_designcustomiser/layout_save_disableModule');
            $model->saveFromConfig();
        } catch (Exception $e) {
            Mage::log($e);
        }
    }

    /**
     * Allow only specific languages for admin theme
     * @param $observer
     * @return $this
     */
    public function languageOptions($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (get_class($block) == 'Mage_Adminhtml_Block_Html_Select' && $block->getId() == 'interface_locale') {

            // This should be made to be read from configuration (?)
            $allowedLocales = Mage::getStoreConfig('blugento_designcustomiser/translations');
            //echo '<pre>'; print_r($allowedLocales); die;

            $observer->getEvent()->getBlock()->setOptions($allowedLocales);
        }
        return $this;
    }
}
