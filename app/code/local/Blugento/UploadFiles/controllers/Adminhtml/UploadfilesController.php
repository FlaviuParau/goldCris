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
 * @package     Blugento_UploadFiles
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_UploadFiles_Adminhtml_UploadfilesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Extract all files with 'zip' extension from import path
     */
    public function unzipAction()
    {
        $configPath = Mage::getStoreConfig('blugento_uploadfiles/general/path');
        $fullPath = Mage::getBaseDir() . DS . $configPath;

        $extracted = array();
        if (file_exists($fullPath)) {
            foreach (scandir($fullPath) as $file) {
                if ($file != '.' && $file != '..') {
                    $fileArr = explode('.', $file);
                    $ext = $fileArr[count($fileArr) - 1];

                    if ($ext == 'zip') {
                        $unzipped = Mage::helper('blugento_uploadfiles')->unzipFile($file, $fullPath);

                        if ($unzipped) {
                            array_push($extracted, $file);
                            unlink($fullPath . DS . $file);
                        }
                    }
                }
            }
        }

        $count = count($extracted);

        if ($count > 0) {
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('blugento_uploadfiles')->__('%s archives were extracted: %s.', $count, implode(', ', $extracted))
            );
        } else {
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('blugento_uploadfiles')->__('There is no archive to extract!')
            );
        }
        $this->_redirectReferer();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
