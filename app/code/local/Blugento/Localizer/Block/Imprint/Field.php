<?php
/**
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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block to retrieve data from imprint config field.
 */
class Blugento_Localizer_Block_Imprint_Field extends Blugento_Localizer_Block_Imprint_Content
{
    /**
     * Render imprint field
     *
     * @return string Field value
     */
    protected function _toHtml()
    {
        if ($this->getValue() == 'email') {
            return $this->getEmail(false); // TODO @Simona
        }

        return $this->getData($this->getValue());
    }
}
