<?php
/**
 * Blugento Feeds
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Feed_Save_Abstract extends Varien_File_Object
{
    /**
     * Definition filename
     * @var string
     */
    protected $_definitionFilename = '';

    /**
     * Definition file path
     * @var string
     */
    protected $_definitionFilePath = '';

    /**
     * Save CSV file
     * @var bool
     */
    protected $_saveAsCSV = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        try {

            $defaultWebsite = Mage::getModel('core/website')->load('1', 'is_default');
            $storeId = Mage_Core_Model_App::DISTRO_STORE_ID;
            if ($defaultWebsite->getId()) {
                $storeGroup = Mage::getModel('core/store_group')->load($defaultWebsite->getId(), 'website_id');
            }
            if ($storeGroup->getId()) {
                $storeId = $storeGroup->getDefaultStoreId();
            }
            //$fileName = 'store_' . Mage::app()->getStore()->getStoreId() . '_' . $this->_definitionFilename;
            $fileName = 'store_' . $storeId . '_' . $this->_definitionFilename;



            $directory = 'media';
            $configDirectory = Mage::getStoreConfig('blugento_feeds/feeds_directory');
            if ($configDirectory) {
                $directory = $configDirectory;
            }

            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
                $storeId
            );
            $filePath = Mage::getBaseDir($directory) . DS . 'blugento_datafeeds' . DS . $fileName;
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

            // Create the file if it doesn't exist
            if (!file_exists($filePath)) {
                if (!is_dir(Mage::getBaseDir($directory) . DS . 'blugento_datafeeds')) {
                    @mkdir(Mage::getBaseDir($directory) . DS . 'blugento_datafeeds', 0775);
                }
                $fd = fopen($filePath, 'w');
                if ($fd === false) {
                    return null;
                }
                fclose($fd);
            }

            $this->_definitionFilePath = $filePath;
        } catch (Exception $e) {
            Mage::logException($e);
        }

        if (!$this->_definitionFilePath) {
            throw new Exception('Cannot create feed file!');
        }

        parent::__construct($this->_definitionFilePath);
    }

    /**
     * Save the data in file
     * @param string|array $content
     * @throws Exception
     * @return Blugento_Feeds_Model_Feed_Save_Abstract|null
     */
    public function save($content)
    {
        if (!$this->_definitionFilePath) {
            return null;
        }

        if ($this->_saveAsCSV) {
            return $this->saveCSV($content);
        }

        $fh = fopen($this->_definitionFilePath, 'w');
        if ($fh === false) {
            return null;
        }

        if (is_array($content)) {
            $content = implode("\n", $content);
        } else {
            try {
                $content = (string)$content;
            } catch (Exception $e) {
                Mage::logException($e);
                throw new Exception('Cannot save feed file: invalid content!');
            }
        }

        fwrite($fh, $content);
        fclose($fh);

        return $this;
    }

    /**
     * Save the data in CSV file
     * @param array $content
     * @throws Exception
     * @return Blugento_Feeds_Model_Feed_Save_Abstract|null
     */
    public function saveCSV($content)
    {
        if (!$this->_definitionFilePath) {
            return null;
        }

        $fh = fopen($this->_definitionFilePath, 'w');
        if ($fh === false) {
            return null;
        }

        if (!is_array($content)) {
           $content = explode("\n", $content);
        }

        foreach ($content as $line) {
            fputcsv($fh, $line);
        }

        fclose($fh);

        return $this;
    }

    /**
     * Get file content
     * @return null|string
     */
    public function getContent()
    {
        if (!$this->_definitionFilePath) {
            return null;
        }

        $fd = fopen($this->_definitionFilePath, 'r');
        if ($fd === false) {
            return null;
        }
        $size = filesize($this->_definitionFilePath);
        $content = $size > 0 ? fread($fd, $size) : '';
        fclose($fd);

        return $content;
    }
}
