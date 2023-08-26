<?php
/**
 * Blugento Feeds
 * Feeds Logos Block
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Feeds
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Block_Feeds extends Mage_Core_Block_Template
{
    protected $_template = 'blugento/feeds/list.phtml';

    /**
     * Feed logos active
     * @return bool
     */
    public function feedLogosActive()
    {
        return Mage::getStoreConfig('blugentolocalizer/data_feeds/enabled') && Mage::getStoreConfig('blugento_feeds/general/logos');
    }

    /**
     * Get active feeds logos and URLs
     * @return array
     */
    public function getFeeds()
    {
        $enabledFeeds = Mage::getStoreConfig('blugentolocalizer/data_feeds/enabled');
        if (!$enabledFeeds) {
            return array();
        }
        $enabledFeeds = explode(',', Mage::getStoreConfig('blugentolocalizer/data_feeds/feeds'));
        $feeds = array();

        foreach ($enabledFeeds as $enabledFeed) {
            $logo = Mage::getStoreConfig('blugento_feeds/' . $enabledFeed . '/logo');
            $html = Mage::getStoreConfig('blugento_feeds/' . $enabledFeed . '/logo_html');
            if ($logo || $html) {
                $feed = array('logo' => $logo, 'logo_html' => $html);
                $url = Mage::getStoreConfig('blugento_feeds/' . $enabledFeed . '/provider_url');
                if ($url && strpos($url, 'http') === false) {
                    $url = 'http://' . $url;
                }
                $feed['url'] = $url;
                $feeds[] = $feed;
            }
        }
        return $feeds;
    }
}
