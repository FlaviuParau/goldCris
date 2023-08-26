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

class Blugento_ProductLabels_Model_Adminhtml_System_Config_Source_Label_Tooltip
{
    /**
     * Options getter
     *
     * @return string
     */
    public function getPositionTooltip()
    {
        $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'adminhtml/default/default/blugento/productlabels/images/additional/position.png';

        $html = '';
        $html .= '<h4>' . Mage::helper('blugento_productlabels')->__('Product image positions:') . '</h4>';
        $html .= '<img src="' . $imagePath . '">';

        return $this->_getTooltipHtml($html);
    }

    /**
     * Return tooltip code (html)
     *
     * @param $content
     * @return string
     */
    private function _getTooltipHtml($content)
    {
        $tooltip = '
            <div class="field-tooltip toggle">
                <span class="field-tooltip-action action-help" tabindex="0" hidden="hidden"></span>
                <div class="field-tooltip-content">
                     ' . $content . '
                </div>
            </div>
        ';

        return $tooltip;
    }
}