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

class Blugento_ProductLabels_Block_Catalog_Product_Label extends Mage_Core_Block_Template
{
    /**
     * Return enabled labels for a certain product
     *
     * @return array
     */
    public function getProductLabels()
    {
        try {
            $product = Mage::registry('current_product');
            $storeId = Mage::app()->getStore()->getStoreId();

            /** @var Blugento_ProductLabels_Model_Label $model */
            $labelModel = Mage::getModel('blugento_productlabels/label');

            $labels = $labelModel->getActiveLabelsForProduct($product, $storeId, true);

            return $labels;
        } catch (Exception $e) {
            Mage::logException($e);
            return null;
        }
    }

    /**
     * Return html class by label position on product page.
     *
     * @param int $position
     * @return string
     */
    public function getClassByPosition($position)
    {
        switch ($position) {
            case 1:
                $class = 'top-left';
                break;
            case 2:
                $class = 'middle-left';
                break;
            case 3:
                $class = 'bottom-left';
                break;
            case 4:
                $class = 'top-right';
                break;
            case 5:
                $class = 'middle-right';
                break;
            case 6:
                $class = 'bottom-right';
                break;
            default:
                $class = '';
        }

        return $class;
    }

    /**
     * Return front skin url for label
     *
     * @param array $label
     * @return string
     */
    public function getFrontLabelPath($label)
    {
        if ($label['created_type'] == 'custom') {
            $imagePath = Mage::getBaseUrl('media') . 'blugento_productlabels/custom/' . $label['name'] . '/' . $label['path'];
        } else {
            $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/images/blugento_productlabels/default/' . $label['path'];
        }
        return $imagePath;
    }
}