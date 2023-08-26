<?php
/**
 * Blugento Feeds
 * Observer Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Feeds
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Adminhtml_Observer
{
    public function saveSection($observer)
    {
        $oldValues = Mage::registry('saved_blugento_feeds');
        $feeds = Mage::getStoreConfig('blugento_feeds/feeds');
        foreach ($feeds as $feedId => $value) {
            $update = false;
            if (isset($oldValues[$feedId])) {
                foreach ($oldValues[$feedId] as $key => $oldValue) {
                    if (Mage::getStoreConfig('blugento_feeds/' . $feedId . '/' . $key) != $oldValue) {
                        $update = true;
                        break;
                    }
                }
            } else {
                $update = true;
            }
            if ($update) {
                try {
                    $model = Mage::getModel('blugento_feeds/feed_provider_' . $feedId);
                    if ($model->isEnabled()) {
                        $model->save();
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    public function beforeSaveSection($observer)
    {
        try {
            $object  = $observer->getData('object');
            $section = $object->getSection();
            if ($section == 'blugento_feeds') {
                Mage::register('saved_blugento_feeds', Mage::getStoreConfig('blugento_feeds'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
