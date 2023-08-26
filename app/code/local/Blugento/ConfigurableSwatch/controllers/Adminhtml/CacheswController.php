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
 * @package     Blugento_ConfigurableSwatch
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ConfigurableSwatch_Adminhtml_CacheswController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Delete the Swatch image cache directory
     */
    public function clearAction()
    {
        try {
            $swatchImgCacheDir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'swatches' . DS . 'cache';
            if (is_dir($swatchImgCacheDir)) {
                $this->_deleteDirectory($swatchImgCacheDir);
                Mage::getSingleton('adminhtml/session')->addSuccess('Swatch Image Cache Deleted');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('ERROR: ' . $e->getMessage());
        }
        $this->_redirectReferer();
    }

    /**
     * Recursively delete dir files and dir
     *
     * @param string $dirPath
     */
    private function _deleteDirectory($dirPath)
    {
        if (is_dir($dirPath)) {
            $objects = scandir($dirPath);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                        $this->_deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dirPath);
        }
    }
}
