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

class Blugento_ProductLabels_Model_Adminhtml_System_Config_Source_Label_Model
{
    /**
     * Options getter
     *
     * @param $id
     * @return string
     */
    public function toOptionArray($id)
    {
        /** @var Blugento_ProductLabels_Model_Label $model */
        $label = Mage::getModel('blugento_productlabels/label')->load($id);

        if ($label->getCreatedType() == 'custom') {
            $imagePath = Mage::getBaseUrl('media') . 'blugento_productlabels/custom/' . $label->getName() . '/' . $label->getPath();
        } else {
            $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/blugento/productlabels/images/default/' . $label->getPath();
        }

        $text = '<img src="' . $imagePath . '">';

        return $text;
    }
}
