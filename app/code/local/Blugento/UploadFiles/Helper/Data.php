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

class Blugento_UploadFiles_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Extract zip file.
     *
     * @param string $file
     * @param string $directory
     * @return bool
     */
    public function unzipFile($file, $directory)
    {
        $zip = new ZipArchive();
        $res = $zip->open($directory . DS . $file);

        if ($res === true) {
            $zip->extractTo($directory);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}
