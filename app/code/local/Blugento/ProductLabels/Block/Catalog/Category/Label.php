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

class Blugento_ProductLabels_Block_Catalog_Category_Label extends Mage_Core_Block_Template
{
    private $_product;

    public function setProduct($product)
    {
        $this->_product = $product;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function getProductLabels()
    {
        $storeId = Mage::app()->getStore()->getStoreId();

        /** @var Blugento_ProductLabels_Model_Label $model */
        $labelModel = Mage::getModel('blugento_productlabels/label');

        $labels = $labelModel->getActiveLabelsForProduct($this->_product, $storeId, false, true);

        return $labels;
    }

    /**
     * Return html class by label position on category page.
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
     * Return label image path
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