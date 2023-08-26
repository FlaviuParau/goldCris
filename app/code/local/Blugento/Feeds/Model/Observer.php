<?php
/**
 * Blugento Feeds
 * Observer Class for Cron Jobs
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Feeds
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Observer
{
    public function checkcron(){

        if (!Mage::getStoreConfig('blugentolocalizer/data_feeds/enabled')) {
            return false;
        }
        $feeds = explode(',', Mage::getStoreConfig('blugentolocalizer/data_feeds/feeds'));
        if (!$feeds) {
            return false;
        }

        foreach($feeds as $feedId) {
            if (!Mage::getStoreConfig('blugento_feeds/'. $feedId . '/enabled')) {
                continue;
            }
            $cacheTime = Mage::getStoreConfig('blugento_feeds/' . $feedId . '/cache_time');
            $lastRunTime = Mage::getStoreConfig('blugento_feeds/' . $feedId . '/run_time');
            $currentTime = time();
            $validToRunAt = strtotime($lastRunTime) + 3600*$cacheTime;

            if ($validToRunAt <= $currentTime || !$lastRunTime) {
                $this->generate($feedId);
            }
        }
        return true;

    }

    public function generate($feed){

        try {
            $model = Mage::getModel('blugento_feeds/feed_provider_' . $feed);
            $model->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $date = new DateTime();
        Mage::getConfig()->saveConfig('blugento_feeds/' . $feed . '/run_time', $date->format('Y-m-d H:i:s'), 'default', 0)->reinit();

    }
}

