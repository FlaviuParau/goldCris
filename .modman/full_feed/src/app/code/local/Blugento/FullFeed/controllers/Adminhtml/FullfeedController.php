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
 * @package     Blugento_FullFeed
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FullFeed_Adminhtml_FullfeedController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Generate the feed file
     */
    public function generateAction()
    {
        $timeStart = microtime(true);

        $feedType = $this->getRequest()->getParam('type') ?
            $this->getRequest()->getParam('type'): 1;

        /** @var Blugento_FullFeed_Model_Feed $model */
        $model = Mage::getModel('blugento_fullfeed/feed');

        $result = $model->runFeedGeneration($feedType);

        $executionTime = number_format((microtime(true) - $timeStart) , 2);

        if ($result == 'success') {
            $message = $this->__('Feed was generated with success in: %s seconds' , $executionTime);

            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } else {
            Mage::getSingleton('adminhtml/session')->addError('ERROR: ' . $result);
        }

        $this->_redirectReferer();
    }

    /**
     * Clean the feed last run time
     */
    public function cleanAction()
    {
        $feedType = $this->getRequest()->getParam('type') ?
            $this->getRequest()->getParam('type'): 1;

        try {
            Mage::getConfig()->saveConfig('blugento_fullfeed/feed_' . $feedType . '/last_run_time', '', 'default', 0)->reinit();

            $filename = end(explode('/', Mage::getStoreConfig('blugento_fullfeed/feed_' . $feedType . '/file_path')));
            $path = Mage::getBaseDir('media') . DS . Blugento_FullFeed_Model_Feed::FILE_SAVE_PATH . '/' . $filename;

            if (file_exists($path)) {
                unlink($path);
            }

            Mage::getConfig()->saveConfig('blugento_fullfeed/feed_' . $feedType . '/file_path', '', 'default', 0);

            $message = $this->__('Feed cache was deleted with success');

            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('ERROR: ' . $e->getMessage());
        }

        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
