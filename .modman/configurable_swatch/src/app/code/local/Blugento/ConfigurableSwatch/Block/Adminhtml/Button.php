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

class Blugento_ConfigurableSwatch_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $url = Mage::helper('adminhtml')->getUrl('adminhtml/cachesw/clear');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setId('generate_feed')
            ->setClass('scalable')
            ->setLabel(Mage::helper('blugento_configurableswatch')->__('Clean Swatch Image Cache'))
            ->setOnClick("setLocation('{$url}')")
            ->toHtml();

        return $html;
    }
}
