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
 * @package     Blugento_ErpProcess
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ErpProcess_Model_Log extends Mage_Core_Model_Abstract
{
    const FILE_TYPE_PRODUCT = 'product';
    const FILE_TYPE_ORDER   = 'order';
    const FILE_TYPE_STOCK   = 'stock';
    const FILE_TYPE_PRICE   = 'price';
    const FILE_TYPE_INVOICE = 'invoice';

    /**
     * Create directory and log response.
     *
     * @param string $directory
     * @param string $filename
     * @param array $messages
     */
    public function logResponse($directory, $filename, $messages)
    {
        $filePath = $this->_buildDirectory($directory) . DS . $this->_buildFilename($filename);

        $content = array();
        if (!is_array($messages)) {
            $content[] = $messages;
        } else {
            $content = $messages;
        }

        $file = fopen($filePath, 'a');
        foreach ($content as $message) {
            fwrite($file, PHP_EOL . date("d-m-Y H:i:s") . ': ' . $message);
        }

        fclose($file);
    }

    /**
     * Delete logs.
     *
     * @param string $directory
     * @param string $cleanAfter
     */
    public function cleanLogs($directory, $cleanAfter)
    {
        if (file_exists($directory)) {
            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $filename = explode('_', $file);
                    $filename = $filename[0];

                    $createdTime = strtotime($filename);
                    $currentTime = strtotime(date('Y-m-d', time()));
                    $cleanLogAfter = $cleanAfter * 24 * 3600;

                    if ($currentTime - $createdTime > $cleanLogAfter) {
                        unlink($directory . DS . $file);
                    }
                }
            }
        }
    }

    /**
     * Return log file name.
     *
     * @param string $type
     * @return string
     */
    private function _buildFilename($type)
    {
        return date('Y-m-d', time()) . '_' . $type . '.log';
    }

    /**
     * Create log directory and return its path.
     *
     * @param string $dir
     * @return string
     */
    private function _buildDirectory($dir)
    {
        $logDirectory = Mage::getBaseDir('var') . DS . 'log';
        $directory = $logDirectory . DS . $dir;

        if (!file_exists($logDirectory)) {
            mkdir($logDirectory);
        }

        if (!file_exists($directory)) {
            mkdir($directory);
        }

        return $directory;
    }
}