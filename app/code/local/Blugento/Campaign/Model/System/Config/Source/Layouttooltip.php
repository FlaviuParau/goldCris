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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Model_System_Config_Source_Layouttooltip
{
    /**
     * Return tooltip code (html)
     *
     * @return string
     */
    public function getTooltipHtml()
    {
        /** @var Blugento_Campaign_Helper_Data $model */
        $helper = Mage::helper('blugento_campaign');

        $content = $helper->__('The layout that you will select will be automatically updated on the selected CMS Page.');
        $content .= '<br><b>' . $helper->__('Be careful! This could affect the shortcode and you must copy it again in the CMS Page content.') . '</b>';

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
