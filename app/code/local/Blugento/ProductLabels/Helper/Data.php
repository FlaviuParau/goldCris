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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CACHE_TAG = 'block_html';
    const CACHE_ID = 'blugento_productlabels';

    /**
     * Check if module is enabled.
     *
     * @return bool|mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('blugento_productlabels/general/enabled');
    }

    /**
     * Unlink recursively a directory and all the files from in it.
     *
     * @param string $dir
     * @param bool $deleteParent
     */
    public function unlinkRecursive($dir, $deleteParent)
    {
        if(!$child = @opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($child))) {
            if($obj == '.' || $obj == '..') {
                continue;
            }

            if (!@unlink($dir . '/' . $obj)) {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }
        closedir($child);

        if ($deleteParent) {
            @rmdir($dir);
        }
        return;
    }

    /**
     * Remove duplicates from multidimensional array.
     *
     * @param array $array
     * @param string $compareKey
     * @return array
     */
    public function removeArrayDuplicates($array, $compareKey)
    {
        $newArr = array();
        foreach ($array as $key => $item) {
            if (isset($newArr[$item[$compareKey]])) {
                unset($array[$key]);
                continue;
            }

            $newArr[$item['id']] = true;
        }

        return $array;
    }

    /**
     * Unset promo/new core labels if they are enabled from this module.
     *
     * @param array $labels
     * @param string $type
     * @param int $storeId
     * @return array
     */
    public function disableCoreLabels($labels, $type, $storeId)
    {
        /** @var Blugento_ProductLabels_Model_Label $model */
        $model = Mage::getModel('blugento_productlabels/label');

        if (isset($labels['badge--sale'])) {
            if ($model->isDefaultLabelEnabled('promo', $type, $storeId)) {
                unset($labels['badge--sale']);
            }
        }

        if (isset($labels['badge--new'])) {
            if ($model->isDefaultLabelEnabled('new', $type, $storeId)) {
                unset($labels['badge--new']);
            }
        }

        return $labels;
    }
}