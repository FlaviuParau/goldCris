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
 * @package     Blugento_ConfigurableSwatch
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ConfigurableSwatch_Block_List extends Mage_Core_Block_Template
{
    public function _construct() {
        $this->setTemplate('blugento/configurableswatch/list.phtml');
    }

    public function getSwatchList()
    {
        $product = $this->getProduct();

        return 'test:: ' . $product->getId();
    }
}