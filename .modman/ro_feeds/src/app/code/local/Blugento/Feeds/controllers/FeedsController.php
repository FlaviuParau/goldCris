<?php
/**
 * Blugento Feeds
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_FeedsController extends Mage_Core_Controller_Front_Action
{
    /**
     * Shows the given agreement
     */
    public function indexAction()
    {
        $feed = $this->getRequest()->getParam('feed');
        $refresh = $this->getRequest()->getParam('refresh');

        switch($feed) {
            case 'paralero':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_paralero');
                break;
            case 'okaziiro':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_okaziiro');
                break;
            case 'pricero':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_pricero');
                break;
            case 'compariro':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_compariro');
                break;
            case 'shopmania':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_shopmania');
                break;
            case 'mabor':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_mabor');
                break;
            case 'ga':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_ga');
                break;
            case 'gacustom':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_gacustom');
                break;
            case 'googleshopping':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_googleshopping');
                break;
            case 'bringo':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_bringo');
                break;
            case 'profitshare':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_profitshare');
                break;
	        case 'glami':
		        $feedModel = Mage::getModel('blugento_feeds/feed_provider_glami');
		        break;
            case 'favi':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_favi');
                break;
            case 'fashionup':
                $feedModel = Mage::getModel('blugento_feeds/feed_provider_fashionup');
                break;
            default:
                echo 'Not available';
                exit();
        }

        try {
            if ($feedModel->isEnabled()) {
                $feedModel->setRefresh($refresh);
                //$feedModel->output();
                $this->loadFile($feedModel->output());
            } else {
                echo 'Not available';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    protected function _isAllowed()
    {
        return true;
    }
    function loadFile($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
