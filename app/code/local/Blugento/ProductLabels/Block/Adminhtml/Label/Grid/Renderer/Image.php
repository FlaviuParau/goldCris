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

class Blugento_ProductLabels_Block_Adminhtml_Label_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        if ($row->getPath()) {
            if ($row->getCreatedType() == 'custom') {
                $imagePath = Mage::getBaseUrl('media') . 'blugento_productlabels/custom/' . $row->getName() . '/' . $row->getPath();
            } else {
                $imagePath = $this->getAdminLabelPath($row->getPath());
            }

            $html = '<img style="max-width:100px" src="' . $imagePath . '" />';
        } else {
            $html = Mage::helper('blugento_productlabels')->__('No Model');
        }

        return $html;
    }

    /**
     * Return admin skin url for label
     *
     * @param string $image
     * @return string
     */
    private function getAdminLabelPath($image)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/blugento/productlabels/images/default/' . $image;
    }
}