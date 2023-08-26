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

class Blugento_ProductLabels_Model_System_Config_Source_Data
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        $imagesNou = array('v1_nou.png', 'v2_nou.png', 'v3_nou.png', 'v4_nou.png');
        $imagesPromo = array('v1_promo.png', 'v2_promo.png', 'v3_promo.png', 'v4_promo.png');

        $data = array();

        $i = 1;
        foreach ($imagesPromo as $image) {
            $data[] = array('Promo ' . $i, 0, 'promo', $image, 0, 1, 0, 1);
            $i++;
        }

        $i = 1;
        foreach ($imagesNou as $image) {
            $data[] = array('New ' . $i, 0, 'new', $image, 0, 1, 0, 1);
            $i++;
        }

        return $data;
    }
}