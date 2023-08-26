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
 * @package     Blugento_DeliveryDay
 * @author      Marius Boia <marius.boia@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DeliveryDay_Model_System_Config_Source_Days
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => 1),
            array('value' => 2, 'label' => 2),
            array('value' => 3, 'label' => 3),
            array('value' => 4, 'label' => 4),
            array('value' => 5, 'label' => 5),
            array('value' => 6, 'label' => 6),
            array('value' => 7, 'label' => 7),
            array('value' => 8, 'label' => 8),
            array('value' => 9, 'label' => 9),
            array('value' => 10, 'label' => 10),
        );
    }
}
