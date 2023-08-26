<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Adminhtml_Amgeoip_ImportController extends Mage_Adminhtml_Controller_Action
{
    protected $_geoipFiles = array(
        'block' => 'GeoLite2-City-Blocks-IPv4.csv',
        'block_v6' => 'GeoLite2-City-Blocks-IPv6.csv',
        'location' => 'GeoLite2-City-Locations-en.csv'
    );

    protected $_geoipIgnoredLines = array(
        'block' => 1,
        'block_v6' => 1,
        'location' => 1
    );

    public function startAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');
            $action = $this->getRequest()->getParam('action');

            /* @var $geoIpModel Amasty_Geoip_Model_Import */
            $geoIpModel = Mage::getSingleton('amgeoip/import');
            $geoIpModel->resetDone();
            $filePath = $geoIpModel->getFilePath($type, $action);
            $ret = $geoIpModel->startProcess($type, $filePath, $this->_geoipIgnoredLines[$type], $action);
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);
            $result['status'] = 'started';
            $result['file'] = $this->_geoipFiles[$type];

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function processAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');
            $action = $this->getRequest()->getParam('action');

            $allowedTableTypes = array('block', 'location', 'block_v6');
            if (!in_array($type, $allowedTableTypes)) {
                throw new Exception('Invalid table type');
            }

            /** @var Amasty_Geoip_Model_Import $import */
            $import = Mage::getSingleton('amgeoip/import');
            $filePath = $import->getFilePath($type, $action);
            $ret = $import->doProcess($type, $filePath, $action);
            $result['type'] = $type;
            $result['status'] = 'processing';
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);
            if ($action == 'import') {
                if ($type == 'block' && $result['position'] == 100 && $ret['current_row'] + 3 < $ret['rows_count']) {
                    $result['position'] = 99;
                }
            } else {
                if ($result['position'] > 100) {
                    $result['position'] = 100;
                }
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function commitAction()
    {
        $result = array();

        try {
            /* @var $geoIpModel Amasty_Geoip_Model_Import */
            $geoIpModel = Mage::getModel('amgeoip/import');
            $type = $this->getRequest()->getParam('type');
            $isDownload = Mage::app()->getRequest()->getParam('is_download');
            $geoIpModel->commitProcess($type, $isDownload);
            $result['status'] = 'done';
            $result['full_import_done'] = $geoIpModel->isDone() ? "1" : "0";
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function startDownloadingAction()
    {
        $result = array();
        $needToUpdate = true;
        $needToDownload = true;
        try {
            $actionType = 'download_and_import';
            $type = $this->getRequest()->getParam('type');
            $import = Mage::getSingleton('amgeoip/import');
            $url = $this->_getFileUrl($type);
            $dir = $import->getDirPath($actionType);
            $newFilePath = $import->getFilePath($type, $actionType);

            if (file_exists($newFilePath)) {
                $hashUrl = $this->getHashUrl($type);
                if ($hashUrl
                    && hash_file('md5', $newFilePath) == trim(file_get_contents($hashUrl))
                ) {
                    $needToDownload = false;
                    if (Mage::getModel('amgeoip/import')->isDone()) {
                        $needToUpdate = false;
                    }
                } else {
                    unlink($newFilePath);
                }
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0770, true);
            }

            if ($needToUpdate) {
                if ($needToDownload) {
                    $this->downloadFile($url, $newFilePath);
                }
                $result['status'] = 'finish_downloading';
                $result['file'] = $this->_geoipFiles[$type];
            } else {
                $result['status'] = 'done';
                $result['file'] = $this->_geoipFiles[$type];
            }


        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    private function downloadFile($url, $filePath)
    {
        $ch = curl_init();
        $fp = fopen($filePath, "w");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    protected function _getFileUrl($type)
    {
        $url = '';
        switch ($type) {
            case 'block':
                $url = Mage::getStoreConfig('amgeoip/general/block_file_url');
                break;
            case 'block_v6':
                $url = Mage::getStoreConfig('amgeoip/general/block_v6_file_url');
                break;
            case 'location':
                $url = Mage::getStoreConfig('amgeoip/general/location_file_url');
                break;
        }

        return $url;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/amgeoip');
    }

    protected function getHashUrl($type)
    {
        switch ($type) {
            case 'block':
                return Mage::getStoreConfig('amgeoip/general/block_hash_url');
            case 'block_v6':
                return Mage::getStoreConfig('amgeoip/general/block_v6_hash_url');
            case 'location':
                return Mage::getStoreConfig('amgeoip/general/location_hash_url');
        }

        return '';
    }
}
