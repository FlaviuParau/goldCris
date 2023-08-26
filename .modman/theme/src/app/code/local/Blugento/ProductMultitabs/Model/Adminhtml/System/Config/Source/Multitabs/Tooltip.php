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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Model_Adminhtml_System_Config_Source_Multitabs_Tooltip
{
    /**
     * Return tooltop html
     *
     * @param string $content
     * @return string
     */
    public function toHtml($content)
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
